<?php namespace App\Lib\Game\Method\Lotto\EM;

use App\Lib\Game\Method\Lotto\Base;

// 二胆拖组选
class LTQ2DTZU2 extends Base
{
    public static $filterArr = [
        '01'    => 1,
        '02'    => 1,
        '03'    => 1,
        '04'    => 1,
        '05'    => 1,
        '06'    => 1,
        '07'    => 1,
        '08'    => 1,
        '09'    => 1,
        '10'    => 1,
        '11'    => 1
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt=count(self::$filterArr);
        $rand=1;
        $rand2=$cnt-$rand;

        $temp=(array)array_rand(self::$filterArr,$rand);
        $_arr2=array_diff(array_keys(self::$filterArr),$temp);
        $arr[]=implode('&',$temp);
        $arr[]=implode('&',(array)array_rand(array_flip($_arr2),$rand2));

        return implode('|',$arr);
    }

    public function fromOld($sCodes){
        return implode('|',array_map(function($v){
            return implode('&',explode(' ',$v));
        },explode('|',$sCodes)));
    }

    public function regexp($sCodes)
    {
        if (!preg_match("/^(((0[1-9]&)|(1[01]&)){0,6}((0[1-9])|(1[01]))\|){1}(((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01])))$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        if (count($aDan) != count(array_filter(array_unique($aDan),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) { //不能有重复的号码
            return false;
        }
        $aTuo = explode('&', $aTmp[1]);
        if (count($aTuo) != count(array_filter(array_unique($aTuo),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) { //不能有重复的号码
            return false;
        }

        if(count($aDan)==0 || count($aTuo)==0){
            return false;
        }

        if (count($aDan) >= 2) {
            return false;
        }

        //有重复的
        if (count(array_intersect($aDan, $aTuo)) > 0) {
            return false;
        }

        return true;
    }

    public function count($sCodes)
    {
        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        return $this->getCombinCount(count($aTuo), 2 - count($aDan));
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        // 胆码必须 + 拖码 顺序不限
        $sCodes     = $this->convertLtCodes($sCodes);
        $numbers    = $this->convertLtCodes($numbers);

        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);

        $iNum = count($aDan);
        // 胆码都存在
        if(count(array_intersect($aDan,$numbers)) == $iNum) {
            $iCnt = 2-$iNum;
            $i  = 0;
            $arr = array_diff($numbers, $aDan);
            foreach($aTuo as $t) {
                if(in_array($t, $arr)) {
                    $i++;
                }
            }

            if($i >= $iCnt){
                return 1;
            }
        }
    }
}
