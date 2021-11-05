<?php namespace App\Lib\Game\Method\Ssc;

// 定位胆
trait BaseDWD {
    public $positionsTpl = array(
        'w' => '万',
        'q' => '千',
        'b' => '百',
        's' => '十',
        'g' => '个'
    );

    public $supportExpand = true;

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = [0,1,2,3,4,5,6,7,8,9];

        foreach($numbers as $pos => $code) {
            $tmp    = [];
            foreach($arr as $_code) {
                $tmp[]  = intval($code == $_code);
            }
            $result[]   = $tmp;
        }

        return $result;
    }

    // 供测试用 生成随机投注
    public function randomCodes(&$poss = array())
    {
        $positions = array('w'=>'','q'=>'','b'=>'','s'=>'','g'=>'');
        if(!$this->pos) {
            // 集合
            $pos=(array) array_rand($positions,rand(1,count($positions)));
        } else {
            $pos=str_split($this->pos);
        }

        // 按为生成随机
        foreach($pos as $k => $v) {
            $positions[$v] = parent::randomCodes();
        }

        return implode('|',$positions);
    }

    // 格式判断
    public function regexp($sCodes)
    {
        $regexp = '/^(([0-9]&){0,9}[0-9])$/';
        if(!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$filterArr;

        // 去重
        $aCodes = explode('&', $sCodes);
        if(count($aCodes) != count(array_filter(array_unique($aCodes), function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) return false;

        if(count($aCodes) == 0) {
            return false;
        }

        return true;
    }

    // 注数计算
    public function count($sCodes)
    {
        return count(explode('&', $sCodes));
    }

    // 判断奖金
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $codes  = explode('&', $sBetCodes);
        $exists = array_flip($aOpenNumbers);
        foreach($codes as $c){
            if(isset($exists[$c])) {
                return 1;
            }
        }
        return 0;
    }

    public function expand($sCodes, $pos = null)
    {
        $result = [];
        $aCodes = explode('|', $sCodes);
        foreach($aCodes as $index => $code) {
            if(trim($code) === '') continue;
            switch($index){
                case 0:
                    $methodSign = $this->id . "_W";
                    break;
                case 1:
                    $methodSign = $this->id . "_Q";
                    break;
                case 2:
                    $methodSign = $this->id . "_B";
                    break;
                case 3:
                    $methodSign = $this->id . "_S";
                    break;
                case 4:
                    $methodSign = $this->id . "_G";
                    break;
                default:
                    $methodSign = "";
            }

            if(!$methodSign) continue;

            $result[]   = array(
                'method_sign'   => $methodSign,
                'codes'         => $code,
                'count'         => count(explode('&', $code)),
            );

        }
        return $result;
    }
}
