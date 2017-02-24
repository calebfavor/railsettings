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
}