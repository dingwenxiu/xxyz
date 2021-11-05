<?php

use Illuminate\Database\Seeder;

class ActivityRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('activity_rules')->insert(
            [
                'type' => 'checkin',
                'name' => '签到',

                'params'     => json_encode(
                    [
                        'checkin_days' => ['cn_name'  => '连续签到天数',
                                           'type'     => 'input',
                                           'dataType' => 'int', 'data' => ''],
                        'possible'     => ['cn_name'  => '每天需要达到条件',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '投注量',
                                                          2 => '充值量']],
                        'possible_val' => ['cn_name'  => '条件值',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'prize'        => ['cn_name'  => '奖品',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '礼金', 2 => '积分',
                                                          3 => '钱']],
                        'prize_value'  => ['cn_name'  => '奖品数量',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'obtain_type'  => ['cn_name'  => '领取方式',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '及时领取',
                                                          2 => '次日发放',
                                                          3 => '客服领取']],
                        'check'        => ['cn_name'  => '是否需要审核',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '需要审核',
                                                          2 => '不需要审核']],
                        'participants' => ['cn_name'  => '参与人员',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '直属', 2 => '代理',
                                                          3 => '会员']],
                        'home'         => ['cn_name'  => '首页轮播图',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '显示',
                                                          2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '需要登录',
                                                            2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );
        DB::table('activity_rules')->insert(
            [
                'type'       => 'turntable',
                'name'       => '转盘',
                'params'     => json_encode(
                    [
                        'cells'        => ['cn_name'  => '槽位个数',
                                           'type'     => 'input',
                                           'dataType' => 'int', 'data' => ''],
                        'turn_num'     => ['cn_name'  => '转动次数',
                                           'type'     => 'input',
                                           'dataType' => 'int', 'data' => ''],
                        'probability'  => ['cn_name'  => '中奖概率',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible_val' => ['cn_name'  => '条件值',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible'     => ['cn_name'  => '每天需要达到条件',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '投注量',
                                                          2 => '充值量']],
                        'lottery_sign'   => ['cn_name'  => '彩票sign',
                                           'type'     => 'radio',
                                           'dataType' => 'int', 'data' => ''],
                        'prize'        => ['cn_name'  => '奖品',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '礼金', 2 => '积分',
                                                          3 => '钱']],
                        'prize_value'  => ['cn_name'  => '奖品数量',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'obtain_type'  => ['cn_name'  => '领取方式',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '及时领取',
                                                          2 => '次日发放',
                                                          3 => '客服领取']],
                        'check'        => ['cn_name'  => '是否需要审核',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '需要审核',
                                                          2 => '不需要审核']],
                        'angle'        => ['cn_name'  => '角度',
                                           'type'     => 'input',
                                           'dataType' => 'array', 'data' => ''
                        ],
                        'participants' => ['cn_name'  => '参与人员',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '直属', 2 => '代理',
                                                          3 => '会员']],
                        'img'          => ['cn_name' => '游戏图片',

                                           'type'     => 'input',
                                           'dataType' => 'img', 'data' => ''],
                        'home'         => ['cn_name'  => '首页轮播图',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '显示',
                                                          2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                                 'type'     => 'radio',
                                                 'dataType' => 'int',
                                                 'data'     => [1 => '需要登录',
                                                                2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );

        DB::table('activity_rules')->insert(
            [
                'type'       => 'turntable_one',
                'name'       => '转盘一',
                'params'     => json_encode(
                    [
                        'cells'        => ['cn_name'  => '槽位个数',
                                           'type'     => 'input',
                                           'dataType' => 'int', 'data' => ''],
                        'probability'  => ['cn_name'  => '中奖概率',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible_val' => ['cn_name'  => '条件值',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible'     => ['cn_name'  => '每天需要达到条件',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '投注量',
                                                          2 => '充值量']],
                        'lottery_sign'   => ['cn_name'  => '彩票sign',
                                             'type'     => 'radio',
                                             'dataType' => 'int', 'data' => ''],
                        'prize'        => ['cn_name'  => '奖品',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '礼金', 2 => '积分',
                                                          3 => '钱']],
                        'prize_value'  => ['cn_name'  => '奖品数量',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'obtain_type'  => ['cn_name'  => '领取方式',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '及时领取',
                                                          2 => '次日发放',
                                                          3 => '客服领取']],
                        'check'        => ['cn_name'  => '是否需要审核',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '需要审核',
                                                          2 => '不需要审核']],
                        'angle'        => ['cn_name'  => '角度',
                                           'type'     => 'input',
                                           'dataType' => 'array', 'data' => ''
                        ],
                        'participants' => ['cn_name'  => '参与人员',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '直属', 2 => '代理',
                                                          3 => '会员']],
                        'img'          => ['cn_name' => '游戏图片',

                                           'type'     => 'input',
                                           'dataType' => 'img', 'data' => ''],
                        'home'         => ['cn_name'  => '首页轮播图',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '显示',
                                                          2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                                 'type'     => 'radio',
                                                 'dataType' => 'int',
                                                 'data'     => [1 => '需要登录',
                                                                2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );
        DB::table('activity_rules')->insert(
            [
                'type'       => 'first_recharge',
                'name'       => '首次充值',
                'params'     => json_encode(
                    [
                        'recharge'     => ['cn_name'  => '充值金额',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible'     => ['cn_name'  => '每天需要达到条件',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '投注量',
                                                          2 => '充值量']],
                        'possible_val' => ['cn_name'  => '条件值',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'prize'        => ['cn_name'  => '奖品',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '礼金', 2 => '积分',
                                                          3 => '钱']],
                        'prize_value'  => ['cn_name'  => '奖品数量',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],

                        'obtain_type'  => ['cn_name'  => '领取方式',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '及时领取',
                                                          2 => '次日发放',
                                                          3 => '客服领取']],
                        'check'        => ['cn_name'  => '是否需要审核',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '需要审核',
                                                          2 => '不需要审核']],
                        'participants' => ['cn_name'  => '参与人员',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '直属', 2 => '代理',
                                                          3 => '会员']],
                        'give_type'    => ['cn_name'  => '赠送类型',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '固定金额',
                                                          2 => '比例']],
                        'give_val'     => ['cn_name'  => '赠送值/比例',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'home'         => ['cn_name'  => '首页轮播图',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '显示',
                                                          2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                                 'type'     => 'radio',
                                                 'dataType' => 'int',
                                                 'data'     => [1 => '需要登录',
                                                                2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );
        DB::table('activity_rules')->insert(
            [
                'type'       => 'gift_recharge',
                'name'       => '充值赠送',
                'params'     => json_encode(
                    [
                        'recharge'     => ['cn_name'  => '充值金额',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'possible'     => ['cn_name'  => '每天需要达到条件',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '投注量',
                                                          2 => '充值量']],
                        'possible_val' => ['cn_name'  => '条件值',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'prize'        => ['cn_name'  => '奖品',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '礼金', 2 => '积分',
                                                          3 => '钱']],
                        'prize_value'  => ['cn_name'  => '奖品数量',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],

                        'obtain_type'  => ['cn_name'  => '领取方式',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '及时领取',
                                                          2 => '次日发放',
                                                          3 => '客服领取']],
                        'give_type'    => ['cn_name'  => '赠送类型',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '固定金额',
                                                          2 => '比例']],
                        'give_val'     => ['cn_name'  => '赠送值/比例',
                                           'type'     => 'input',
                                           'dataType' => 'float', 'data' => ''],
                        'check'        => ['cn_name'  => '是否需要审核',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '需要审核',
                                                          2 => '不需要审核']],
                        'participants' => ['cn_name'  => '参与人员',
                                           'type'     => 'select',
                                           'dataType' => 'int',
                                           'data'     => [1 => '直属', 2 => '代理',
                                                          3 => '会员']],
                        'home'         => ['cn_name'  => '首页轮播图',
                                           'type'     => 'radio',
                                           'dataType' => 'int',
                                           'data'     => [1 => '显示',
                                                          2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                                 'type'     => 'radio',
                                                 'dataType' => 'int',
                                                 'data'     => [1 => '需要登录',
                                                                2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );
        DB::table('activity_rules')->insert(
            [
                'type'       => 'red_pack_rain',
                'name'       => '红包雨',
                'params'     => json_encode(
                    [
                        'total_num'     => ['cn_name'  => '红包总数',
                                            'type'     => 'input',
                                            'dataType' => 'int', 'data' => ''],
                        'winning_num'   => ['cn_name'  => '多少个红包有钱',
                                            'type'     => 'input',
                                            'dataType' => 'int', 'data' => ''],
                        'max_money'     => ['cn_name'  => '每个红包最大金额',
                                            'type'     => 'input',
                                            'dataType' => 'float',
                                            'data'     => ''],
                        'red_pack_time' => ['cn_name'  => '红包雨时间',
                                            'type'     => 'input',
                                            'dataType' => 'int', 'data' => ''],
                        'possible'      => ['cn_name'  => '需要达到条件',
                                            'type'     => 'radio',
                                            'dataType' => 'int',
                                            'data'     => [1 => '投注量',
                                                           2 => '充值量']],
                        'possible_val'  => ['cn_name'  => '条件值',
                                            'type'     => 'input',
                                            'dataType' => 'float',
                                            'data'     => ''],
                        'prize'         => ['cn_name'  => '奖品',
                                            'type'     => 'radio', 'int',
                                            'dataType' => 'prize',
                                            'data'     => [1 => '礼金',
                                                           2 => '积分',
                                                           3 => '钱']],
                        'prize_value'   => ['cn_name'  => '奖品数量',
                                            'type'     => 'input',
                                            'dataType' => 'float',
                                            'data'     => ''],
                        'obtain_type'   => ['cn_name'  => '领取方式',
                                            'type'     => 'select',
                                            'dataType' => 'int',
                                            'data'     => [1 => '及时领取',
                                                           2 => '次日发放',
                                                           3 => '客服领取']],
                        'check'         => ['cn_name'  => '是否需要审核',
                                            'type'     => 'radio',
                                            'dataType' => 'int',
                                            'data'     => [1 => '需要审核',
                                                           2 => '不需要审核']],
                        'participants'  => ['cn_name'  => '参与人员',
                                            'type'     => 'select',
                                            'dataType' => 'int',
                                            'data'     => [1 => '直属',
                                                           2 => '代理',
                                                           3 => '会员']],
                        'home'          => ['cn_name'  => '首页轮播图',
                                            'type'     => 'radio',
                                            'dataType' => 'int',
                                            'data'     => [1 => '显示',
                                                           2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                        'login_show'         => ['cn_name'  => '登录显示活动规则',
                                                 'type'     => 'radio',
                                                 'dataType' => 'int',
                                                 'data'     => [1 => '需要登录',
                                                                2 => '不需要登录']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );
        DB::table('activity_rules')->insert(
            [
                'type' => 'active_info',
                'name' => '活动说明',
                'prize'    => '',
                'obtain_type'  => '',
                'home'         => '',
                'participants' => '',

                'params'     => json_encode(
                    [
                        'info' => ['活动详情', 'text', 'text', ''],
                        'home'          => ['cn_name'  => '首页轮播图',
                                            'type'     => 'radio',
                                            'dataType' => 'int',
                                            'data'     => [1 => '显示',
                                                           2 => '不显示']],
                        'status'         => ['cn_name'  => '是否禁用',
                                             'type'     => 'radio',
                                             'dataType' => 'int',
                                             'data'     => [1 => '开启',
                                                            2 => '禁用']],
                    ]
                ),
                'open_partner' => '',
                'img_list'   => '',
                'img_info'   => '',
                'start_time' => '',
                'end_time'   => '',
            ]
        );


//        DB::table('partner_activity_rules')->insert(
//            [
//                'partner_sign' => 'CX',
//                'type'         => 'red_pack_rain',
//                'name'         => '红包雨',
//                'params'       => '{"cells":3,"turn_num":[{"0":1,"1":1000,"red_pack_time":10,"possible":"1","possible_val":1},{"0":1,"1":2000,"red_pack_time":2,"possible":"1","possible_val":1}],"prize":[{"prize":"1","prize_val":10,"total_num":100,"winning_num":70,"max_money":2,"prize_value":1},{"prize":"2","prize_val":10,"total_num":100,"winning_num":70,"max_money":2,"prize_value":2},{"prize":"1","prize_val":10,"total_num":100,"winning_num":70,"max_money":2,"prize_value":3},{"prize":"2","prize_value":50,"probability":50,"img":null,"angle":[216,287],"total_num":30,"winning_num":10,"max_money":1},{"prize":"1","prize_value":20,"probability":20,"img":null,"angle":[288,359],"total_num":30,"winning_num":10,"max_money":1}],"obtain_type":"1","participants":"1,2,3","check":"1","home":"1"}
//',
//                'text_h5'      => '',
//                'text'         => '',
//                'home'         => 2,
//                'img_banner'   => '',
//                'img_list'     => '',
//                'img_info'     => '',
//                'start_time'   => '2019-11-01 04:18:07',
//                'end_time'     => '2019-11-30 16:42:25',
//            ]
//        );
        DB::table('partner_activity_rules')->insert(
            [
                'partner_sign' => 'KLC',
                'type'         => 'checkin',
                'name'         => '签到',
                'params'       => '{"possible":"1","possible_val":1,"participants":["1","2","3"],"home":"1","cells":4,"prize":[{"checkin_days":1,"prize":"3","prize_value":1,"obtain_type":"1","check":"2"},{"checkin_days":7,"prize":"3","prize_value":18,"obtain_type":"1","check":"2"},{"checkin_days":15,"prize":"3","prize_value":38,"obtain_type":"1","check":"2"},{"checkin_days":30,"prize":"3","prize_value":58,"obtain_type":"1","check":"2"}],"status":"2"}',
                'pc_desc'      => '<h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">
    活动时间
</h2>
<p style="white-space: normal; padding-bottom: 15px;">
    常驻活动
</p>
<h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">
    活动规则
</h2>
<p style="white-space: normal;">
    第一步：绑定并锁定银行卡
</p>
<p style="white-space: normal;">
    第二步：联系客服报名，每日限额1000
</p>
<p style="white-space: normal;">
    第三步：充值后，联系客服申请礼金
</p>
<p style="white-space: normal; padding-bottom: 15px;">
    第四步：完成流水，提现。
</p>
<h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">
    注意事项
</h2>
<ul class=" list-paddingleft-2" style="width: 443.641px; white-space: normal;">
    <li>
        <p>
            参与该活动后，30天内不能更换绑定银行卡
        </p>
    </li>
    <li>
        <p>
            每人只能参与一次，使用多账户参与活动（同姓名、同银行卡、同IP）视为套利行为，资金将被冻结。
        </p>
    </li>
    <li>
        <p>
            投注码量不能超过80%，即定位胆玩法不能超过8注、二码玩法不能超过80注、三码玩法不能超过800注、四星玩法不能超过8000注、五星玩法不能超过80000注。
        </p>
    </li>
    <li>
        <p>
            全包玩法不计入有效投注。
        </p>
    </li>
    <li>
        <p>
            平台保留对活动的最终解释权。
        </p>
    </li>
</ul>
<p>
    <br/>
</p>',
                'h5_desc'      => '',
                'status'       => 1,
                'home'         => 2,
                'img_banner'   => '/yx/activity/20191126173300.jpg',
                'img_list'     => '/yx/activity/20191126173302.jpg',
                'img_info'     => '',
                'start_time'   => '2019-11-01 04:18:07',
                'end_time'     => '2022-11-30 16:42:25',
            ]
        );
        DB::table('partner_activity_rules')->insert(
            [
                'partner_sign' => 'KLC',
                'type'         => 'turntable_one',
                'name'         => '转盘一',
                'params'       => '{"cells":5,"prize":[{"prize":"3","prize_value":18,"probability":11,"angle":[0,72],"possible":"1","lottery_sign":"yxjsxy28","possible_val":1},{"prize":"3","prize_value":28,"probability":22,"angle":[73,144],"possible":"1","lottery_sign":"cxjslhc","possible_val":2},{"prize":"3","prize_value":38,"probability":10,"angle":[145,216],"possible":"1","lottery_sign":"cxjsssl","possible_val":3},{"prize":"3","prize_value":48,"probability":5,"angle":[217,288],"possible":"1","lottery_sign":"cxjsftpk10","possible_val":4},{"prize":"3","prize_value":58,"probability":1,"angle":[289,360],"possible":"1","lottery_sign":"cxjs1fk3","possible_val":5}],"obtain_type":"1","participants":["1","2","3"],"check":"1","home":"1","possibles":1,"img":"\/cx\/activity\/20191126173150.png","status":"2"}',
                'pc_desc'      => '<p>
    <img src="/cx/activity/20191213154809.jpg" width="100%"/>
</p>',
                'h5_desc'      => '<p>
    <img src="/cx/activity/20191213114129.jpg" width="100%"/>
</p>',
                'status'       => 1,
                'home'         => 2,
                'img_banner'   => '/yx/activity/20191126173141.jpg',
                'img_list'     => '/yx/activity/20191126173144.jpg',
                'img_info'     => '',
                'start_time'   => '2019-11-01 04:18:07',
                'end_time'     => '2022-11-30 16:42:25',
            ]
        );
        DB::table('partner_activity_rules')->insert(
            [
                'partner_sign' => 'YX',
                'type'         => 'turntable',
                'name'         => '转盘',
                'params'       => '{"cells":5,"turn_num":[{"lottery_sign":["yxjslhc","yxjsp3p5"],"possible":"1","possible_val":1,"turn_num":1}],"prize":[{"prize":"3","prize_value":18,"probability":12,"angle":[0,72]},{"prize":"3","prize_value":28,"probability":42,"angle":[73,144]},{"prize":"3","prize_value":38,"probability":3,"angle":[145,216]},{"prize":"3","prize_value":48,"probability":5,"angle":[217,288]},{"prize":"3","prize_value":58,"probability":4,"angle":[289,360]}],"obtain_type":"1","participants":["1","2","3"],"check":"1","home":"1","possibles":1,"img":"\/cx\/activity\/20191126173150.png"}',
                'pc_desc'      => '',
                'h5_desc'      => '',
                'status'       => 1,
                'home'         => 2,
                'img_banner'   => '/yx/activity/20191126173141.jpg',
                'img_list'     => '/yx/activity/20191126173144.jpg',
                'img_info'     => '',
                'start_time'   => '2019-11-01 04:18:07',
                'end_time'     => '2022-11-30 16:42:25',
            ]
        );
        DB::table('partner_activity_rules')->insert(
            [
                'partner_sign' => 'YX',
                'type'         => 'first_recharge',
                'name'         => '首冲赠送',
                'params'       => '{"home":"1","cells":3,"prize":[{"possible":"1","possible_val":1,"recharge":1,"give_type":"1","prize":"1","prize_value":1,"obtain_type":"1","participants":["1","2","3"],"check":"2","give_val":1},{"possible":"1","possible_val":1,"recharge":2,"give_type":"1","prize":"3","prize_value":2,"obtain_type":"1","participants":["1","2","3"],"check":"1","give_val":2},{"possible":"1","possible_val":1,"recharge":3,"give_type":"1","prize":"3","prize_value":3,"obtain_type":"1","participants":["3","1","2"],"check":"1","give_val":3}],"login_show":"1","status":"1"}',
                'pc_desc'      => '<h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">活动时间</h2><p style="white-space: normal; padding-bottom: 15px;">常驻活动</p><h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">活动规则</h2><p style="white-space: normal;">第一步：绑定并锁定银行卡</p><p style="white-space: normal;">第二步：联系客服报名，每日限额1000</p><p style="white-space: normal;">第三步：充值后，联系客服申请礼金</p><p style="white-space: normal; padding-bottom: 15px;">第四步：完成流水，提现。</p><h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">注意事项</h2><ul class=" list-paddingleft-2" style="width: 443.641px; white-space: normal;"><li><p>参与该活动后，30天内不能更换绑定银行卡</p></li><li><p>每人只能参与一次，使用多账户参与活动（同姓名、同银行卡、同IP）视为套利行为，资金将被冻结。</p></li><li><p>投注码量不能超过80%，即定位胆玩法不能超过8注、二码玩法不能超过80注、三码玩法不能超过800注、四星玩法不能超过8000注、五星玩法不能超过80000注。</p></li><li><p>全包玩法不计入有效投注。</p></li><li><p>平台保留对活动的最终解释权。</p></li></ul><p><br/></p>',
                'h5_desc'      => '',
                'status'       => 1,
                'home'         => 2,
                'img_banner'   => '/yx/activity/20191126172855.jpg',
                'img_list'     => '/yx/activity/20191126173040.jpg',
                'img_info'     => '',
                'start_time'   => '2019-11-01 04:18:07',
                'end_time'     => '2022-11-30 16:42:25',
            ]
        );
        DB::table('partner_activity_rules')->insert(
            [
                'partner_sign' => 'yx',
                'type'         => 'gift_recharge',
                'name'         => '充值赠送',
                'params'       => '{"home":"1","cells":3,"prize":[{"possible":"1","possible_val":1,"recharge":1,"give_type":"1","prize":"1","prize_value":1,"obtain_type":"1","participants":["1"],"check":"2","give_val":1},{"possible":"1","possible_val":1,"recharge":2,"give_type":"1","prize":"3","prize_value":2,"obtain_type":"1","participants":["2","3","1"],"check":"1","give_val":2},{"possible":"1","possible_val":1,"recharge":3,"give_type":"1","prize":"3","prize_value":3,"obtain_type":"1","participants":["3","2","1"],"check":"1","give_val":3}],"status":"2"}',
                'pc_desc'      => '<h2 style="font-size:22px;font-weight:400;margin:15px 0;">活动时间</h2><p style="padding-bottom:15px;">常驻活动</p><h2 style="font-size:22px;font-weight:400;margin:15px 0;">活动规则</h2><p>第一步：绑定并锁定银行卡</p><p>第二步：联系客服报名，每日限额1000</p><p>第三步：充值后，联系客服申请礼金</p><p style="padding-bottom:15px;">第四步：完成流水，提现。</p><h2 style="font-size:22px;font-weight:400;margin:15px 0;">注意事项</h2><ul style="list-style-type: disc;" class=" list-paddingleft-2"><li><p>参与该活动后，30天内不能更换绑定银行卡</p></li><li><p>每人只能参与一次，使用多账户参与活动（同姓名、同银行卡、同IP）视为套利行为，资金将被冻结。</p></li><li><p>投注码量不能超过80%，即定位胆玩法不能超过8注、二码玩法不能超过80注、三码玩法不能超过800注、四星玩法不能超过8000注、五星玩法不能超过80000注。</p></li><li><p>全包玩法不计入有效投注。</p></li><li><p>平台保留对活动的最终解释权。</p></li></ul>',
                'h5_desc'      => '<h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">活动时间</h2><p style="white-space: normal; padding-bottom: 15px;">常驻活动</p><h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">活动规则</h2><p style="white-space: normal;">第一步：绑定并锁定银行卡</p><p style="white-space: normal;">第二步：联系客服报名，每日限额1000</p><p style="white-space: normal;">第三步：充值后，联系客服申请礼金</p><p style="white-space: normal; padding-bottom: 15px;">第四步：完成流水，提现。</p><h2 style="white-space: normal; font-size: 22px; font-weight: 400; margin: 15px 0px;">注意事项</h2><ul class=" list-paddingleft-2" style="width: 443.641px; white-space: normal;"><li><p>参与该活动后，30天内不能更换绑定银行卡</p></li><li><p>每人只能参与一次，使用多账户参与活动（同姓名、</p></li><li><p>同银行卡、同IP）视为套利行为，资金将被冻结。</p></li><li><p>投注码量不能超过80%，即定位胆玩法不能超过8注、二码</p></li><li><p>玩法不能超过80注、三码玩法不能超过800注、四星玩法</p></li><li><p>不能超过8000注、五星玩法不能超过80000注。</p></li><li><p>全包玩法不计入有效投注。</p></li><li><p>平台保留对活动的最终解释权。</p></li></ul><p><br/></p>',
                'status'       => 1,
                'home'         => 2,
                'img_banner'   =>  '/yx/activity/20191126172758.jpg',
                'img_list'     =>  '/yx/activity/20191126172957.jpg',
                'img_info'     => '',
                'start_time'   => '2019-11-01 04:18:07',
                'end_time'     => '2020-11-30 16:42:25',
            ]
        );
    }
}
