<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;

class PartnerNavigation extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'partner_navigations';

    public $rules = [
    ];


    public function saveItem($c, $partnerSign, $id) {
        $validator  = \Illuminate\Support\Facades\Validator::make($c, $this->rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        if (intval($id) === 0) {
            $thisModel = $this;
        } else {
            $thisModel = self::find($id);
        }
        if (!$thisModel) {
            return false;
        }

        if ( ! empty($c['name'])) {
            $thisModel->name = $c['name'];
        }
        if ( ! empty($c['url'])) {
            $thisModel->url = $c['url'];
        }
        if ( ! empty($c['style'])) {
            $thisModel->style = $c['style'];
        }
        if (isset($c['home'])) {
            $thisModel->home = $c['home'];
        }
        if ( ! empty($c['order'])) {
            $thisModel->order = $c['order'];
        }

        $thisModel->casino_plat_id = $c['casino_plat_id'] ?? '';
        $thisModel->partner_sign = $partnerSign;

        $thisModel->save();

        return true;
    }


    public function delNavigetion($id)
    {
        self::where('id', $id)->delete();
    }
}
