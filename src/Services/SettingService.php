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
     * @param null $default
     * @return null|string
     */
    public function get($ownerType, $ownerId, $category, $name, $default = null)
    {
        $setting = $this->settingDataMapper->getForOwner($ownerType, $ownerId);

        if (empty(!$setting)) {
            $currentSettings = $setting->getSettings();

            if (isset($currentSettings[$category][$name])) {
                return $currentSettings[$category][$name];
            }
        }

        return $default;
    }

    /**
     * @param $ownerType
     * @param $ownerIds
     * @param $category
     * @param $name
     * @param null $default
     * @return array
     */
    public function getForOwnersKeyed($ownerType, $ownerIds, $category, $name, $default = null)
    {
        $settings = $this->settingDataMapper->getForOwners($ownerType, $ownerIds);

        $ownerSettings = [];

        foreach ($settings as $setting) {
            $currentSettings = $setting->getSettings();

            if (isset($currentSettings[$category][$name])) {
                $ownerSettings[$setting->getOwnerId()] = $currentSettings[$category][$name];
            } else {
                $ownerSettings[$setting->getOwnerId()] = $default;
            }
        }

        return $ownerSettings;
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

    /**
     * @param $ownerType
     * @param $ownerIds
     * @return array
     */
    public function getAllForOwners($ownerType, $ownerIds)
    {
        $settings = $this->settingDataMapper->getForOwners($ownerType, $ownerIds);

        $ownerSettings = [];

        foreach ($settings as $setting) {
            $ownerSettings[$setting->getOwnerId()] = $setting->getSettings();
        }

        return $ownerSettings;
    }
}