<?php

namespace App\Models\Admin;

use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminGroup;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class AdminReviewFlow extends Model
{

    protected $table = 'admin_review_flow';

    /**
     * 获取一条数据
     * @param string $adminUser
     * @return array
     */
    static function getOne($type)
    {
        $data = self::where('type', $type)->first();
        if (!$data) {
            return [];
        }

        return $data;
    }
    static function partnerReviewPermissionsList($c){
        $query = self::select('*');
        $data = $query->get();
        $result = [];
        $config =  config("admin.main.partner_review_type");

        foreach ($data as $item) {
            $item->type_sign = $config[$item->type]['sign'];

            $item->type_name = $config[$item->type]['name'];
            $item->admins = self::objectToArray(json_decode($item->admins));
            $partnerInfo = [];
            foreach ($item->admins as $admin){
                $partner = Partner::find($admin['id']);
                $partnerGroup = PartnerAdminGroup::findBySign($partner->sign);
                array_push($partnerInfo,['partner'=>$partner,'partnerGroup'=>$partnerGroup]);
            }
            $item->admins = $partnerInfo;
        }

        return $data;

    }

    static function bindPermissions($permission, $c, $config)
    {

        db()->beginTransaction();
        try {

            $admins = json_decode($permission->admins);
            $checkIds = [];
            foreach ($admins as $admin) {
                array_push($checkIds, $admin->id);
            }
            $admins = self::objectToArray($admins);
            $i = 0;
            foreach ($c['ids'] as $id) {
                if (!in_array($id, $checkIds)) {
                    $partner = Partner::find($id);
                    $partner_group = null;
                    if ($partner) {
                        $partner_group = PartnerAdminGroup::findBySign($partner->sign);
                        $groupId = isset($partner_group) ? $partner_group->id : '';
                        $data = [
                            'id' => $partner->id,
                            'group_id' => $groupId
                        ];
                        array_push($admins, $data);
                    }

                }
            }
            $permission->admins = json_encode($admins);

            db()->commit();
            return ['res' => 1];
        } catch (\Exception $e) {
            db()->rollback();
            return ['res' => 1, 'msg' => $e->getMessage()];
        }

    }

    static function addPartnerReviewPermissions($c)
    {

    }

    static function findByType($type)
    {
        return self::where('type', $type)->first();
    }

    static function findJsonByType($type)
    {

    }

    static function fullJsonColumn($type)
    {

    }

    static function setDefault($config, $c)
    {
        db()->beginTransaction();
        try {
            $item = new self();
            $item->type = $c['type'];
            $admins = [];
            foreach ($c['ids'] as $id) {
                $partner = Partner::find($id);
                $partner_group = null;
                if ($partner) {
                    $partner_group = PartnerAdminGroup::findBySign($partner->sign);
                    $groupId = isset($partner_group) ? $partner_group->id : '';
                    $data = [
                        'id' => $partner->id,
                        'group_id' => $groupId
                    ];
                    array_push($admins, $data);
                }
            }
            $item->admins = json_encode($admins);
            $item->save();
            db()->commit();
            return ['res' => 1];
        } catch (\Exception $e) {
            db()->rollback();
            return ['res' => 1, 'msg' => $e->getMessage()];
        }

    }

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    static function objectToArray($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::objectToArray($v);
            }
        }

        return $obj;
    }


    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    static function arrayToObject($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)arrayToObject($v);
            }
        }

        return (object)$arr;
    }
}
