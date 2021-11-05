<?php
namespace App\Models\Casino;

use App\Models\Account\AccountChangeReport;
use App\Models\Partner\PartnerConfigure;
use App\Models\Player\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CasinoPlayerBet extends BaseCasinoModel
{
    protected $table    = 'partner_casino_player_bet';
    public    $rules = [
        "main_game_plat_name"   => "required|min:2|max:64",
    ];

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $partner) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 20;
        $offset         = ($currentPage - 1) * $pageSize;

        if (!empty($partner->sign)) {
            $query = $query->where('partner_sign', $partner->sign);
        }

        if (!empty($c['main_game_plat_code'])) {
            $query = $query->where('main_game_plat_code', $c['main_game_plat_code']);
        }

        if (!empty($c['platform_order_id'])) {
            $query = $query->where('platform_order_id', $c['platform_order_id']);
        }

        if (!empty($c['username'])) {
            $query = $query->where('username', $c['username']);
        }

        // 开始时间
        if (isset($c['startTime']) && $c['startTime']) {
            $query -> where('bet_time', '>=', $c['startTime']);
        }

        // 结束时间
        if (isset($c['endTime']) && $c['endTime']) {
            $query -> where('bet_time', '<=', $c['endTime']);
        }

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    public function saveItem($data, $admin = null) {
        try {
            if (empty($data)){
                return true;
            }
            foreach ($data as $k => $betData) {
                if (!self::find($betData['id'])) {
                    $partnerC = PartnerConfigure::where('sign', 'casino_merchant')->where('value', $betData['site_username'])->first('partner_sign');
                    if ($partnerC === null) {
                        continue;
                    }
                    if (!empty($partnerC->partner_sign)) {

                        $userM = Player::where('username', $betData['accountusername'])->first();
                        $cName = CasinoMethod::where(['partner_sign' => $partnerC->partner_sign, 'main_game_plat_code' => $betData['plat_type'], 'record_match_code' => $betData['game_code']])->first();

                        $dataDB = [
                            'id'                    => $betData['id'],
                            'top_id'                => $userM->top_id ?? 0,
                            'parent_id'             => $userM->parent_id ?? 0,
                            'user_id'               => $userM->user_id ?? 0,
                            'rid'                   => $userM->rid ?? '',
                            'c_name'                => $cName->c_name ?? '',
                            'partner_sign'          => $partnerC->partner_sign,
                            'username'              => $betData['username'],
                            'account_username'      => $betData['accountusername'] ?? '',
                            'site_id'               => $betData['site_id'],
                            'site_username'         => $betData['site_username'],
                            'game_code'             => $betData['game_code'],
                            'main_game_plat_code'   => $betData['main_game_plat_code'],
                            'method_id'             => $betData['gameid'],
                            'platform_order_id'     => $betData['game_flow_code'],
                            'bet_amount'            => $betData['bet_amount'],
                            'company_payout_amount' => $betData['company_payout_amount'],
                            'company_win_amount'    => $betData['company_win_amount'],
                            'plat_type'             => $betData['plat_type'],
                            'lobby_type'            => $betData['lobby_type'],
                            'bet_detail'            => $betData['bet_detail'],
                            'result'                => $betData['result'],
                            'bet_flow_available'    => $betData['bet_flow_available'],
                            'status'                => $betData['status'],
                            'bet_time'              => $betData['bet_time'],
                            'day'                   => date('Ymd',strtotime($betData['bet_time'])),
                            'month'                 => date('Ym',strtotime($betData['bet_time'])),
                            'pull_at'               => $betData['pull_at'],
                            'api_data'              => $betData['api_data'],
                        ];
                        self::insert($dataDB);
                    }
                }
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    static function getOptions($partner) {
        $options = [];
        $platforms = self::where('partner_sign', $partner->sign)->where("status", 1)->get();

        foreach ($platforms as $platform) {
            $options[] = [
                "name" => $platform->main_game_plat_name,
                "code" => $platform->main_game_plat_code,
            ];
        }

        return $options;
    }

    public static function getStatistics($c, $partner)
    {
        $query = self::select(
            'day',
            DB::raw('SUM(bet_amount) as bet_amount'),
            DB::raw('SUM(company_win_amount) as company_win_amount'),
            DB::raw('SUM(company_payout_amount) as company_payout_amount')
        );
        if (!empty($partner->sign)) {
            $query = $query->where('partner_sign', $partner->sign);
        }

        if (!empty($c['main_game_plat_code'])) {
            $query = $query->where('main_game_plat_code', $c['main_game_plat_code']);
        }

        // 开始时间
        if (isset($c['startTime']) && $c['startTime']) {
            $query -> where('bet_time', '>=', $c['startTime']);
        }

        // 结束时间
        if (isset($c['endTime']) && $c['endTime']) {
            $query -> where('bet_time', '<=', $c['endTime']);
        }

        $data = $query->groupBy('day')->get();

        $moneyUnit    = config('game.main.money_unit');


        foreach ($data as $key => $val) {
            if (!empty($c['main_game_plat_code'])) {

                $casino_transfer_out =  AccountChangeReport::select(
                        DB::raw('SUM(amount) as amount')
                    )-> where('day_m', '>=', $val['day'] * 10000)-> where('day_m', '<=', ($val['day'] + 1) * 10000)->where([
                        'type_sign' => 'casino_transfer_out',
                        'casino_platform_sign' => $c['main_game_plat_code'],
                    ])->groupBy('type_sign')->first()->amount ?? 0;

                $casino_transfer_in = AccountChangeReport::select(
                        DB::raw('SUM(amount) as amount')
                    )-> where('day_m', '>=', $val['day'] * 10000)-> where('day_m', '<=', ($val['day'] + 1) * 10000)->where([
                        'type_sign' => 'casino_transfer_in',
                        'casino_platform_sign' => $c['main_game_plat_code'],
                    ])->groupBy('type_sign')->first()->amount ?? 0;

            } else {
                $casino_transfer_out =  AccountChangeReport::select(
                        DB::raw('SUM(amount) as amount')
                    )-> where('day_m', '>=', $val['day'] * 10000)-> where('day_m', '<=', ($val['day'] + 1) * 10000)->where('type_sign', 'casino_transfer_out')->groupBy('type_sign')->first()->amount ?? 0;

                $casino_transfer_in = AccountChangeReport::select(
                        DB::raw('SUM(amount) as amount')
                    )-> where('day_m', '>=', $val['day'] * 10000)-> where('day_m', '<=', ($val['day'] + 1) * 10000)->where('type_sign', 'casino_transfer_in')->groupBy('type_sign')->first()->amount ?? 0;

            }



            $val['casino_transfer_out'] =  bcdiv($casino_transfer_out, $moneyUnit,2);
            $val['casino_transfer_in'] =  bcdiv($casino_transfer_in, $moneyUnit,2);
        }


        return $data;
    }

    /**
     * @param array $data Data.
     * @return string
     */
    public function saveItemAll(array $data, $partner_sign)
    {
        self::truncate();
        DB::beginTransaction();
        foreach ($data as $item) {
            $item['partner_sign'] = $partner_sign;
            $insertStatus = self::insert($item);
        }
        DB::commit();
        return 1;
    }

    static function getcasinoPlayerBet($userId, $c)
    {
        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        if (isset($c['username']) && $c['username']) {
            //查询下级
            $query = self::where('parent_id', $userId)->orderBy('id', 'desc');
        } else {
            $query = self::where('user_id', $userId)->orderBy('id', 'desc');
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
            $query->whereBetween('bet_time',[$c['start_time'], $c['end_time']]);
        }else{
            $query->whereBetween('bet_time',[$timeToday, $timeTom]);
        }

        if (isset($c['plat_type'])) {
            $query->where('plat_type', $c['plat_type']);
        }

        if (isset($c['platform_order_id'])) {
            $query->where('platform_order_id', $c['platform_order_id']);
        }

        if (isset($c['c_name'])) {
            $query->where('c_name', 'like', '%' . $c['c_name'] .'%');
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 10;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $data  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];

    }


}
