<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;

class PartnerAdminGroupUser extends Model
{
    protected $table = 'partner_admin_group_users';

    static function addItem($partnerSign, $partnerAdminUseId, $groupId, $adminUser = null) {
        $item = new self();
        $item->partner_sign         = $partnerSign;
        $item->group_id             = $groupId;
        $item->partner_admin_id     = $partnerAdminUseId;

        $item->add_admin_id         = $adminUser ? $adminUser->id : 999999;
        $item->save();

        return $item;
    }

    /**
     * 绑定管理员到组上
     * @param $group
     * @param $partnerAdminUser
     * @param null $adminUser
     * @return bool
     */
    static function bindUserToGroup($group, $partnerAdminUser, $adminUser = null) {
        return self::addItem($group->partner_sign, $partnerAdminUser->id, $group->id, $adminUser);
    }
}
