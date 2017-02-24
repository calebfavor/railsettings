<?php

namespace Tests;

use Railroad\Railsettings\Entities\Setting;
use Railroad\Railsettings\Services\SettingService;
use Tests\TestCase as NotificationsTestCase;

class SettingServiceTest extends NotificationsTestCase
{
    /**
     * @var SettingService
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(SettingService::class);
    }

    public function test_set()
    {
        $ownerType = $this->faker->word;
        $ownerId = $this->faker->randomNumber();
        $category = 'setting category';
        $name = 'setting name';
        $value = $this->faker->randomNumber();

        $settingsArray = [$category => [$name => $value]];

        $this->classBeingTested->set($ownerType, $ownerId, $category, $name, $value);

        $this->assertDatabaseHas(
            'settings',
            [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'settings' => json_encode($settingsArray),
            ]
        );
    }

    public function test_set_update()
    {
        $ownerType = $this->faker->word;
        $ownerId = $this->faker->randomNumber();
        $category = 'setting category';
        $name = 'setting name';
        $value = $this->faker->randomNumber();

        $settingsArray = [$category => [$name => $value]];

        $this->classBeingTested->set($ownerType, $ownerId, $category, $name, $value);

        $this->assertDatabaseHas(
            'settings',
            [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'settings' => json_encode($settingsArray),
            ]
        );

        $newValue = $this->faker->sentence();
        $newSettingsArray = [$category => [$name => $newValue]];

        $this->classBeingTested->set($ownerType, $ownerId, $category, $name, $newValue);

        $this->assertDatabaseHas(
            'settings',
            [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'settings' => json_encode($newSettingsArray),
            ]
        );
    }

    public function test_get()
    {
        $category = 'setting category';
        $name = 'setting name';
        $value = $this->faker->randomNumber();

        $setting = new Setting();
        $setting->randomize();
        $setting->setSettings(
            [
                $category => [
                    $name => $value,
                ]
            ]
        );
        $setting->persist();

        $responseSetting = $this->classBeingTested->get(
            $setting->getOwnerType(),
            $setting->getOwnerId(),
            $category,
            $name
        );

        $this->assertEquals($value, $responseSetting);
    }

    public function test_get_does_not_exist()
    {
        $responseSetting = $this->classBeingTested->get(
            $this->faker->word,
            rand(),
            $this->faker->word,
            $this->faker->word
        );

        $this->assertEquals(null, $responseSetting);
    }

    public function test_get_all_for_owner_category()
    {
        $ownerType = 'owner type';
        $ownerId = rand();
        $category = 'setting category';
        $settings = [
            $category => [
                rand() => $this->faker->randomNumber(),
                rand() => $this->faker->randomNumber(),
                rand() => $this->faker->randomNumber(),
            ]
        ];

        $setting = new Setting();
        $setting->randomize();
        $setting->setOwnerType($ownerType);
        $setting->setOwnerId($ownerId);
        $setting->setSettings($settings);
        $setting->persist();

        // create some other random settings for extra reassurance
        for ($i = 0; $i < 3; $i++) {
            $setting = new Setting();
            $setting->randomize();
            $setting->setSettings(
                [
                    $this->faker->word => [
                        $this->faker->word => $this->faker->randomNumber(),
                    ]
                ]
            );
            $setting->persist();
        }

        $responseSettings = $this->classBeingTested->getAllForOwnerCategory($ownerType, $ownerId, $category);

        $this->assertEquals($settings[$category], $responseSettings);
    }

    public function test_get_all_for_owner_category_none_exist()
    {
        $responseSettings = $this->classBeingTested->getAllForOwnerCategory(
            $this->faker->word,
            rand(),
            $this->faker->word
        );

        $this->assertEquals([], $responseSettings);
    }

    public function test_get_all_for_owner()
    {
        $ownerType = 'owner type';
        $ownerId = rand();
        $settings = [
            $this->faker->word => [
                $this->faker->word => $this->faker->randomNumber(),
                $this->faker->word => $this->faker->randomNumber(),
                $this->faker->word => $this->faker->randomNumber(),
            ],
            $this->faker->word => [
                $this->faker->word => $this->faker->randomNumber(),
                $this->faker->word => $this->faker->randomNumber(),
                $this->faker->word => $this->faker->randomNumber(),
            ]
        ];

        $setting = new Setting();
        $setting->randomize();
        $setting->setOwnerType($ownerType);
        $setting->setOwnerId($ownerId);
        $setting->setSettings($settings);
        $setting->persist();

        // create some other random settings for extra reassurance
        for ($i = 0; $i < 3; $i++) {
            $setting = new Setting();
            $setting->randomize();
            $setting->setSettings(
                [
                    $this->faker->word => [
                        $this->faker->word => $this->faker->randomNumber(),
                    ]
                ]
            );
            $setting->persist();
        }

        $responseSettings = $this->classBeingTested->getAllForOwner($ownerType, $ownerId);

        $this->assertEquals($settings, $responseSettings);
    }

    public function test_get_all_for_owner_none_exist()
    {
        $responseSettings = $this->classBeingTested->getAllForOwner($this->faker->word, rand());

        $this->assertEquals([], $responseSettings);
    }

    public function test_get_all_for_owners()
    {
        $ownerSettings = [];
        $ownerType = 'owner type';

        for ($i = 0; $i < 3; $i++) {
            $ownerId = rand();
            $settings = [
                $this->faker->word => [
                    $this->faker->word => $this->faker->randomNumber(),
                    $this->faker->word => $this->faker->randomNumber(),
                    $this->faker->word => $this->faker->randomNumber(),
                ],
                $this->faker->word => [
                    $this->faker->word => $this->faker->randomNumber(),
                    $this->faker->word => $this->faker->randomNumber(),
                    $this->faker->word => $this->faker->randomNumber(),
                ]
            ];

            $setting = new Setting();
            $setting->randomize();
            $setting->setOwnerType($ownerType);
            $setting->setOwnerId($ownerId);
            $setting->setSettings($settings);
            $setting->persist();

            $ownerSettings[$setting->getOwnerId()] = $settings;
        }

        // create some other random settings for extra reassurance
        for ($i = 0; $i < 3; $i++) {
            $setting = new Setting();
            $setting->randomize();
            $setting->setSettings(
                [
                    $this->faker->word => [
                        $this->faker->word => $this->faker->randomNumber(),
                    ]
                ]
            );
            $setting->persist();
        }

        $responseSettings = $this->classBeingTested->getAllForOwners($ownerType, array_keys($ownerSettings));

        $this->assertEquals($ownerSettings, $responseSettings);
    }

    public function test_get_all_for_owners_none_exist()
    {
        $responseSettings = $this->classBeingTested->getAllForOwners($this->faker->word, [rand()]);

        $this->assertEquals([], $responseSettings);
    }

}