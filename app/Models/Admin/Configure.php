<?php

namespace App\Models\Admin;

class Configure extends Base
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'sys_configures';

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        // 上级
        if (isset($c['pid']) && $c['pid']) {
            $query->where('pid', '=', $c['pid']);
        } else {
            $query->where('pid', '=', 0);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    //獲取後台配置列表  目前使用
    static function getAllConfigureList(){

        $data = self::orderBy('id', 'desc')->get();
        $_data = $parentData = [];
        foreach ($data as $item) {
            if ($item->pid > 0) {
                $item->pid*=1000;
                if (!isset($parentData[$item->pid])) {
                    $parentData[$item->pid] = [];
                }

                $parentData[$item->pid][] = $item->toArray();
            } else {
                if (!isset($_data[$item->id])) {
                    $_item = $item->toArray();
                    $_item['child'] = [];
                    $_data[$item->id] = $_item;
                }
            }
        }

        foreach ($_data as &$parent) {
            $parent['child'] = isset($parentData[$parent['id']]) ? $parentData[$parent['id']] : [];
        }

        return $_data;
    }

    // 获取所有的配置 层级  停用
    static function getConfigList()
    {
        $data = self::orderBy('id', 'desc')->get();

        $_data = $parentData = [];
        foreach ($data as $item) {
            if ($item->pid > 0) {
                if (!isset($parentData[$item->pid])) {
                    $parentData[$item->pid] = [];
                }

                $parentData[$item->pid][] = $item->toArray();
            } else {
                if (!isset($_data[$item->id])) {
                    $_item = $item->toArray();
                    $_item['child'] = [];
                    $_data[$item->id] = $_item;
                }
            }
        }

        foreach ($_data as &$parent) {
            $parent['child'] = isset($parentData[$parent['id']]) ? $parentData[$parent['id']] : [];
        }

        return $_data;
    }

    /**
     * 保存
     * @param $parent
     * @param $params
     * @param int $adminId
     * @return bool|string
     */
    public function saveItem($parent, $params, $adminId = 0)
    {

        $name = $params["name"];
        if (!$name) {
            return "对不起, 无效的用户名";
        }

        // sign
        $sign = trim($params["sign"]);
        if (!$sign) {
            return "对不起, SIGN不存在!!";
        }

        $partnerEdit = trim($params["partner_edit"]);
        $partnerEdit = $partnerEdit ? true : false;

        $partnerShow = trim($params["partner_show"]);
        $partnerShow = $partnerShow ? true : false;

        if ($parent && !$this->id) {
            $sign    = $parent->sign . "_" . $sign;
        }

        // 如果是新增
        if (!$this->id) {
            $exit = Configure::where('sign', '=', $sign)->count();
            if ($exit) {
                return "对不起, SIGN已经存在!!";
            }
            $lastItemPid = self::where("pid", $params['pid']/1000)->select('id','pid')->orderBy("id", 'DESC')->first();

            if ($params['pid']){
                if ($parent){
                    $this->id    = $lastItemPid ? $lastItemPid->id + 1 : $parent->id * 1000 + 1;

                }else{
                    $lastItem    = self::where("id", $params['id'])->select('id','pid')->orderBy("id", 'DESC')->first();
                    $this->pid   = $lastItem ?? $lastItem->id/1000;
                    $this->id    = $lastItemPid ? $lastItemPid->id + 1:'';
                }
            }else{
                $this->id = $lastItemPid ? $lastItemPid->id + 1000 : 1000;
            }

            // 如果是编辑
        } else {

            $exit = Configure::where('sign', '=', $sign)->where('sign', '<>', $this->sign)->count();
            if ($exit) {
                return "对不起, SIGN已经存在!!";
            }
        }
        $value          = trim($params["value"]);
        $description    = trim($params["description"]);

        // 如果是添加 查找最后一个id
        if (!$this->id) {
            $pid = $parent ? $parent->id : 0;
            $lastItem = self::where("pid", $pid)->orderBy("id", 'DESC')->first();
            if ($pid) {
                $this->id = $lastItem ? $lastItem->id + 1 : $parent->id * 1000 + 1;
            } else {
                if ($params['pid']){
                    $this->id = $lastItem ? $lastItem->id + 1 : $parent->id * 1000 + 1;
                }else{
                    $this->id = $lastItem ? $lastItem->id + 1000 : 1000;
                }
            }

        }

        // 如果是编辑
        if ($this->id && isset($params['is_edit_pid'])&& $params['is_edit_pid'] == 1 && $params['pid'] %1000 == 0 && $params['pid'] >0) {

            $this->pid     = $params['pid']/1000;
        }

        $this->sign                     = $sign;
        $this->name                     = $name;
        $this->value                    = $value;
        $this->description              = $description;
        $this->partner_edit             = $partnerEdit;
        $this->partner_show             = $partnerShow;
        if ($params['pid']&&!$this->id){
            $this->pid                  = $params['pid']/1000;
        }

        // 变更人
        if (!$this->id) {
            $this->add_admin_id         = $adminId;
        } else {
            $this->last_update_admin_id = $adminId;
        }
        $this->save();
        return true;
    }

    static function getConfigParentOption()
    {
        $config = self::where('status', 1)->where('pid', 0)->get();
        $data = [];
        foreach ($config as $item) {
            $data[$item->id] = $item->name;
        }

        return $data;
    }

    static function getAllConfig()
    {
        $config = self::select('sign', 'value')->where('status', 1)->get();
        return $config;
    }

    // 倍数获取
    static function findBySign($betTimes) {
        return self::where("sign", $betTimes)->first();
    }

    // 设置
    static function configureSet($key, $value)
    {
        db()->table("sys_configures")->where('sign', $key)->update(['value' => $value]);
        return true;
    }
}
