<?php

namespace App\Models\Player;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PlayerAvatarImg extends Model
{
    protected $table = 'partner_player_avatar_img';

    public $rules = [
        'avatar'        => 'required|max:254',
    ];

    public $msg = [
        'avatar.required'      => '会员头像图标不能为空',
        'avatar.max'           => '会员头像图标长度必须小于255个字符',
    ];


    public function saveItem($params)
    {
        $validator  = Validator::make($params, $this->rules,$this->msg);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->partner_sign            = $params['partner_sign'];
        $this->avatar                  = $params['avatar'];
        $this->save();

        return true;
    }

}
