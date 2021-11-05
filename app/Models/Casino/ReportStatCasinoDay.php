<?php
namespace App\Models\Casino;

use App\Models\Base;

class ReportStatCasinoDay extends Base
{
    public    $rules = [];

    /**
     * @param $condition
     * @param $pageSize
     * @return mixed
     */
    static function getList($condition, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($condition['pageIndex']) ? intval($condition['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    // 保存
    public function saveItem($data)
    {
        try {
            $month = date('Ym');
            foreach ($data as $key => $item) {
                $betDayData = self::where([
                    'day'          => $item['day'],
                    'partner_sign' => $item['partner_sign'],
                    'main_game_plat_code'    => $item['main_game_plat_code'],
                ])->first();

                if (is_null($betDayData)) {
                    $this->day                   = $item['day'];
                    $this->month                 = $month;
                    $this->bet_amount            = $item['bet_amount'];
                    $this->company_win_amount    = $item['company_win_amount'];
                    $this->company_payout_amount = $item['company_payout_amount'];
                    $this->casino_transfer_out   = $item['casino_transfer_out'];
                    $this->casino_transfer_in    = $item['casino_transfer_in'];
                    $this->partner_sign          = $item['partner_sign'];
                    $this->main_game_plat_code   = $item['main_game_plat_code'];
                    $this->save();
                } else {
                    $betDayData->bet_amount            = $item['bet_amount'];
                    $betDayData->company_win_amount    = $item['company_win_amount'];
                    $betDayData->company_payout_amount = $item['company_payout_amount'];
                    $betDayData->casino_transfer_out   = $item['casino_transfer_out'];
                    $betDayData->casino_transfer_in    = $item['casino_transfer_in'];
                    $betDayData->save();
                }
            }
        }catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    public function getItem($id){
        $listData = self::where('id',$id)->first();
        if (!$listData){
            $this->errMsg = '数据不存在';
            return 1;
        }
        return $listData;
    }

}
