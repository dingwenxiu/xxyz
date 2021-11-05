<?php

namespace App\Models\Account;

use App\Models\Base;

class AccountChangeType extends Base
{

    public $timestamps = false;
    protected $table = 'account_change_type';

    protected $rules = [
        'name'          => 'required',
        'sign'          => 'required',
        'type'          => 'required|in:1,2',
        'user_id'       => 'required|in:0,1',
        'amount'        => 'required',
        'room_id'       => 'required|in:0,1',
        'project_id'    => 'required|in:0,1',
        'related_id'    => 'required|in:0,1',
        'admin_id'      => 'required|in:0,1',
        'froze_type'    => 'required|in:0,1',
        'activity_sign' => 'required|in:0,1',
    ];

    static function getList($c, $pageSize = 15)
    {
        $query = self::orderBy('id', 'desc');


        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize       = isset($c['pageSize']) ? intval($c['pageSize']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function getAll()
    {
        $query = self::orderBy('id', 'desc')->get();

        return ['data' => $query];
    }

    // 保存
    public function saveItem($data)
    {

        $validator  = \Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->name         = $data['name'];
        $this->sign         = $data['sign'];
        $this->type         = $data['type'];
        $this->froze_type   = $data['froze_type'];
        $this->amount       = $data['amount'];
        $this->user_id      = $data['user_id'];
        $this->room_id      = $data['room_id'];
        $this->project_id   = $data['project_id'];
        $this->related_id   = $data['related_id'];
        $this->admin_id     = $data['admin_id'];
        $this->activity_sign    = $data['activity_sign'];
        $this->brokerage_type   = isset($data['brokerage_type']) ? $data['brokerage_type'] : 0;

        $this->save();
        return true;
    }


    /**
     * @param $sign
     * @return array|mixed
     * @throws \Exception
     */
    static function getTypeBySign($sign)
    {
        $data = self::getDataListFromCache();
        if (isset($data[$sign])) {
            return $data[$sign];
        }

        return [];
    }

    /**
     * @return array
     * @throws \Exception
     */
    static function getTypeOptions()
    {
        $data = self::getDataListFromCache();
        $options = [];
        foreach ($data as $key => $item) {
            $options[$item['sign']] = $item['name'];
        }

        return $options;
    }

    /**
     * @param string $cacheKey
     * @return array|mixed
     * @throws \Exception
     */
    static function getDataListFromCache($cacheKey = 'account_change_type')
    {

        if (self::_hasCache($cacheKey)) {
            return self::_getCacheData($cacheKey);
        } else {
            $allCache = self::getDataFromDb();
            if ($allCache) {
                self::_saveCacheData($cacheKey, $allCache);
            }

            return $allCache;
        }
    }

    /**
     * 获取数据
     * @return array
     */
    static function getDataFromDb()
    {
        $items = self::orderBy('id', 'desc')->get();

        $data = [];
        foreach ($items as $item) {
            $data[$item->sign] = $item->toArray();
        }

        return $data;
    }
}
