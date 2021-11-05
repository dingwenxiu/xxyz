<?php namespace App\Models\Player;

use App\Lib\Help;
use App\Models\Base;
use App\Models\Admin\SysCity;
use App\Models\Partner\PartnerConfigure;
use Illuminate\Support\Facades\Validator;

class PlayerCard extends Base
{
    public $rules = [
        'owner_name' => 'required|min:2|max:128',
        'card_number' => 'required|integer',
        'province_id' => 'required|integer',
        'city_id' => 'required|integer',
        'branch' => 'required|min:4|max:128',
    ];

    public $msg =[
        'owner_name.required' => '持卡人名必须填写',
        'owner_name.min' => '用户名长度必须大于1',
        'owner_name.max' => '用户名长度必须小于129',
        'card_number.required' => '卡号必须填写',
        'card_number.integer' => '卡号必须为整型',
        'province_id.required' => '省份必须填写',
        'province_id.integer' => '省份必须为整型',
        'city_id.required' => '城市必须填写',
        'city_id.integer' => '城市必须为整型',
        'branch.required' => '开户支行名称必须填写',
        'branch.min' => '开户支行名称长度必须大于3',
        'branch.max' => '开户支行名称长度必须小于129',
    ];

    protected $table = 'user_bank_cards';

    static function getList($c, $pageSize = 10)
    {
        $query = self::orderBy('id', 'desc');

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if (isset($c['owner_name']) && $c['owner_name']) {
            $query->where('owner_name', $c['owner_name']);
        }

        // 用户id
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 支行
        if (isset($c['branch']) && $c['branch']) {
            $query->where('branch', $c['branch']);
        }

        // 状态
        if (isset($c['status'])) {
            $query->where('status', $c['status']);
        }

        // 卡号
        if (isset($c['card_number']) && $c['card_number']) {
            $query->where('card_number', $c['card_number']);
        }

        // 银行标识
        if (isset($c['bank_sign']) && $c['bank_sign']) {
            $query->where('bank_sign', $c['bank_sign']);
        }

        // 开户行
        if (isset($c['bank_name']) && $c['bank_name']) {
            $query->where('bank_name', $c['bank_name']);
        }

        // 添加时间
        if (isset($c['add_time']) && $c['add_time']) {
            $query->where('created_at', '>=', $c['add_time']);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $res = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $res, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    public function saveItem($data, $user)
    {
        $validator = Validator::make($data, $this->rules,$this->msg);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 卡号
        if (strlen($data['card_number']) < 15 || strlen($data['card_number']) > 19) {
            return "对不起, 银行卡号只能是15位和19位之间!";
        }

        // 银行
        $banks = config("web.banks");
        if (!isset($data['bank_sign']) || !isset($banks[$data['bank_sign']])) {
            return "对不起, 无效的开户行!";
        }

        // 只能前台添加
        $this->partner_sign = $user->partner_sign;
        $this->username = $user->username;
        $this->user_id = $user->id;
        $this->parent_id = $user->parent_id;
        $this->top_id = $user->top_id;

        $this->bank_sign = $data['bank_sign'];
        $this->bank_name = $data['bank_name'];
        $this->card_number = $data['card_number'];
        $this->branch = $data['branch'];
        $this->owner_name = $data['owner_name'];
        $this->province_id = $data['province_id'];
        $this->city_id = $data['city_id'];
        $this->status = 1;
        $this->save();
        return true;
    }


    // 修改银行卡
    public function editItem($data, $user)
    {
        $validator = Validator::make($data, $this->rules,$this->msg);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 卡号
        if (strlen($data['card_number']) < 15 || strlen($data['card_number']) > 19) {
            return "对不起, 银行卡号只能是15位和19位之间!";
        }

        // 银行
        $banks = config("web.banks");
        if (!isset($data['bank_sign']) || !isset($banks[$data['bank_sign']])) {
            return "对不起, 无效的开户行!";
        }

        // 只能前台添加
        $this->partner_sign = $user->partner_sign;
        $this->username = $user->username;
        $this->user_id = $user->user_id;
        $this->parent_id = $user->parent_id;
        $this->top_id = $user->top_id;

        $this->bank_sign = $data['bank_sign'];
        $this->bank_name = $data['bank_name'];
        $this->card_number = $data['card_number'];
        $this->branch = $data['branch'];
        $this->owner_name = $data['owner_name'];
        $this->province_id = $data['province_id'];
        $this->city_id = $data['city_id'];
        $this->status = 1;
        $this->save();
        return true;
    }

    /**
     * 获取加工过银行卡列表
     * @param $userId
     * @return array
     */
    static function getCards($userId)
    {
        $data = [];
        $res = self::where('user_id', '=', $userId)->where('status', '=', 1)->get();
        if ($res) {
            foreach ($res as $item) {
                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['created_time'] = date("Y/m/d", strtotime($item->created_at));
                $tmp['updated_time'] = strtotime($item->updated_at);
                $tmp['bank_sign'] = $item->bank_sign;
                $tmp['bank_name'] = $item->bank_name;
                $tmp['username'] = $item->owner_name;
                $tmp['card_number'] = $item->card_number;
                $tmp['owner_name'] = $item->owner_name;
                $tmp['show_card'] = substr($item->card_number, -4);
                $tmp['created_at'] = strtotime($item->created_at);

                $data[$tmp['id']] = $tmp;
            }
        }
        return $data;
    }

    /**
     * 获取加工过银行卡列表
     * @param $user
     * @return array
     */
    static function getCardForApi($user)
    {
        $data = [];
        $res = self::where('partner_sign', $user->partner_sign)->where('user_id', '=', $user->id)->where('status', '=', 1)->get();
        if ($res) {
            foreach ($res as $item) {
                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['bank_name'] = $item->bank_name;
                $tmp['bank_sign'] = $item->bank_sign;
                $tmp['owner_name'] = $item->owner_name;
                $tmp['card_num'] = Help::cutFrontStr($item->card_number, 4);
                $tmp['created_at'] = $item->created_at->format("Y-m-d H:i:s");
                $tmp['updated_at'] = $item->updated_at->format("Y-m-d H:i:s");

                $data[] = $tmp;
            }
        }
        return $data;
    }

    /**  =============== 辅助函数 =============== */
    /**
     * 是否存在省份
     * @param $pid
     * @return bool
     */
    static function isHaveProvince($pid)
    {
        $province = SysCity::where('region_id', $pid)->first();
        if ($province) {
            return true;
        }
        return false;
    }

    /**
     * 是否存在城市
     * @param $pid
     * @param $cid
     * @return bool
     */
    static function isHaveCity($pid, $cid)
    {
        //查询是否有该省份
        $province = SysCity::where('region_id', $pid)->first();

        //查询是否有该城市
        $city = SysCity::where('region_id', $cid)->first();
        if ($city && $city->region_parent_id == $province->region_id) {
            return true;
        }
        return false;
    }

    static function checkCardCode($code)
    {
        if (!preg_match("/^\d{16}$|^\d{19}|^\d{18}$/", $code)) { // 银行账号格式不正确
            return false;
        }
        return true;
    }

    static function checkBranchName($branchName)
    {
        if (mb_strlen($branchName) > 80 || preg_match("/[\<\>\~\!\@\#\$\%\^\&\*\-\+\=\|\\\'\"\?\,\.\/\[\]\{\}\(\)]{1,}/", $branchName)) { // 支行名称格式检查
            return false;
        }
        return true;
    }

    static function checkCardRealName($realName)
    {
        if (mb_strlen($realName) > 15 || preg_match("/[\<\>\~\!\@\#\$\%\^\&\*\-\+\=\|\\\'\"\?\,\/\[\]\{\}\(\)]{1,}/", $realName)) { // 开户人真实姓名检查
            return false;
        }
        return true;
    }

    /**
     * 剩余绑卡数量
     * @param $uid
     * @param $partnerSign
     * @return mixed
     */
    static function getLaveBindCard($uid,$partnerSign)
    {
        if (!isset($uid) || empty($uid) || !$uid){
            $getLaveBindCard = 0;
        }else{
            $getLaveBindCard = self ::where("user_id", $uid) -> count();
        }

        $finance_card_max_bind = PartnerConfigure::where('partner_sign',$partnerSign)->where('sign','finance_card_max_bind')->select('partner_sign','sign','name','value')->first();
        if (!isset($finance_card_max_bind) || empty($finance_card_max_bind) || !$finance_card_max_bind){
            $finance_card_max_bind = 0;
        }else{
            $finance_card_max_bind = $finance_card_max_bind -> value;
        }

        if ($finance_card_max_bind < $getLaveBindCard){
            $getLaveBindCard = 0;
        }else{
            $getLaveBindCard = $finance_card_max_bind - $getLaveBindCard;
        }
        return empty($getLaveBindCard)?'没有绑卡数量限制':$getLaveBindCard;
    }
}
