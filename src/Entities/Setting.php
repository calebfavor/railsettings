<?php

namespace Railroad\Railsettings\Entities;

use Faker\Generator;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;
use Railroad\Railsettings\DataMappers\SettingDataMapper;

class Setting extends EntityBase
{
    use Timestamps;

    /**
     * @var string
     */
    protected $ownerType;

    /**
     * @var integer
     */
    protected $ownerId;

    /**
     * @var array
     */
    protected $settings = [];

    public function __construct()
    {
        $this->setOwningDataMapper(app(SettingDataMapper::class));
    }

    /**
     * @return string
     */
    public function getOwnerType(): string
    {
        return $this->ownerType;
    }

    /**
     * @param string $ownerType
     */
    public function setOwnerType(string $ownerType)
    {
        $this->ownerType = $ownerType;
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     */
    public function setOwnerId(int $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setOwnerType($faker->word);
        $this->setOwnerId($faker->randomNumber());
        $this->setSettings(
            [
                'data-1' => $faker->word,
                'data-2' => $faker->word,
                'data-3' => $faker->word
            ]
        );

        return $this;
    }
}