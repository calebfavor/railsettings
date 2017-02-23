<?php

namespace Railroad\Railsettings\DataMappers;

use Illuminate\Database\Query\Builder;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;
use Railroad\Railsettings\Entities\Setting;

/**
 * Class SettingDataMapper
 *
 * @package Railroad\Railnotifications\DataMappers
 *
 * @method Setting[] getWithQuery(callable $queryCallback, $columns = ['*'])
 * @method Setting get($id)
 * @method Setting[] getMany($ids)
 */
class SettingDataMapper extends DatabaseDataMapperBase
{
    public $table = 'settings';

    /**
     * @return array
     */
    public function mapTo()
    {
        return [
            'id' => 'id',
            'ownerType' => 'owner_type',
            'ownerId' => 'owner_id',
            'settings' => 'settings',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    /**
     * @return Builder
     */
    public function gettingQuery()
    {
        return parent::gettingQuery();
    }

    /**
     * @return array
     */
    public function types()
    {
        return ['settings' => 'json'];
    }

    /**
     * @return Setting
     */
    public function entity()
    {
        return new Setting();
    }
}