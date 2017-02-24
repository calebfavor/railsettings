<?php

namespace Railroad\Railsettings\Services;

use Railroad\Railsettings\DataMappers\SettingDataMapper;
use Railroad\Railsettings\Entities\Setting;

class SettingService
{
    private $settingDataMapper;

    public function __construct(SettingDataMapper $settingDataMapper)
    {
        $this->settingDataMapper = $settingDataMapper;
    }

    /**
     * @param $ownerType
     * @param $ownerId
     * @param $category
     * @param $name
     * @param $value
     */
    public function set($ownerType, $ownerId, $category, $name, $value)
    {
        $setting = $this->settingDataMapper->getForOwner($ownerType, $ownerId);

        if (empty($setting)) {
            $setting = new Setting();
            $setting->setOwnerType($ownerType);
            $setting->setOwnerId($ownerId);
            $setting->persist();

            $currentSettings = [];
        } else {
            $currentSettings = $setting->getSettings();
        }

        $currentSettings[$category][$name] = $value;

        $setting->setSettings($currentSettings);
        $setting->persist();
    }

    /**
     * @param $ownerType
     * @param $ownerId
     * @param $category
     * @param $name
     * @return string|null
     */
    public function get($ownerType, $ownerId, $category, $name)
    {
        $setting = $this->settingDataMapper->getForOwner($ownerType, $ownerId);

        if (empty(!$setting)) {
            $currentSettings = $setting->getSettings();

            if (isset($currentSettings[$category][$name])) {
                return $currentSettings[$category][$name];
            }
        }

        return null;
    }

    /**
     * @param $ownerType
     * @param $ownerId
     * @param $category
     * @return array
     */
    public function getAllForOwnerCategory($ownerType, $ownerId, $category)
    {
        $setting = $this->settingDataMapper->getForOwner($ownerType, $ownerId);

        if (empty(!$setting)) {
            $currentSettings = $setting->getSettings();

            if (isset($currentSettings[$category])) {
                return $currentSettings[$category];
            }
        }

        return [];
    }

    /**
     * Returns multidimensional array:
     * ['setting category' => ['setting name' => 'value', ...], ...]
     *
     * @param $ownerType
     * @param $ownerId
     * @return array
     */
    public function getAllForOwner($ownerType, $ownerId)
    {
        $setting = $this->settingDataMapper->getForOwner($ownerType, $ownerId);

        if (empty(!$setting)) {
            return $setting->getSettings();
        }

        return [];
    }

    public function getAllForOwners($ownerType, $ownerIds)
    {

    }
}