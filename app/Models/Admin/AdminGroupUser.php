<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminGroupUser extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'admin_group_users';

    static function addItem($userId, $groupId) {
        $item = new self();
        $item->group_id = $groupId;
        $item->user_id  = $userId;
        $item->save();

        return true;
    }
}
