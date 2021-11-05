<?php namespace App\Lib\Game\Method\Klsf;

// 基础处理
trait rx_expands_normal {
    public $positionsTpl = array(
        'w'=>'万',
        'q'=>'千',
        'b'=>'百',
        's'=>'十',
        'g'=>'个'
    );

    public $supportExpand = true;

    public function checkPos($pos)
    {
        $pos=(array) $pos;
        //比对位置
        if(isset($this->expands['num']) && $this->expands['num']>0){
            if(count($pos) < $this->expands['num'] || count(array_diff($pos,array_keys($this->positionsTpl))) >0 ){
                return false;
            }
        }

        return true;
    }

    //冷热 & 遗漏
    public function bingoCode(Array $numbers)
    {
        return [];
    }

    //供测试用 生成随机投注
    public function randomCodes(&$poss=array())
    {
        $poss=[];

        if(!$this->pos){
            //集合
            $pos=(array) array_rand($this->positionsTpl,$this->expands['num']);
        }else{
            $pos=str_split($this->pos);
        }

        $keys=array_flip(array_keys($this->positionsTpl));
        foreach($pos as $k){
            $poss[$k]=$keys[$k];
        }

        return parent::randomCodes();
    }

    public function expand($sCodes,$pos=null)
    {
        if(!$pos || empty($pos)) return [];

        //对pos按wqbsg做排序
        $pos=array_keys(array_intersect_key($this->positionsTpl, array_flip($pos)));

        $result=[];

        $cnt=$this->count($sCodes);
        $aP1 = $this->getCombination($pos,$this->expands['num']);
        foreach($aP1 as $p){
            $result[]=array(
                'methodid'=>$this->id."@".str_replace(' ','',$p),
                'codes'=>$sCodes,
                'num'=>$cnt,
            );
        }

        return $result;
    }

}

class Base extends \App\Lib\Game\Method\Base {
    public static $_abc = array(
        '01' => 'a',
        '02' => 'b',
        '03' => 'c',
        '04' => 'd',
        '05' => 'e',
        '06' => 'f',
        '07' => 'g',
        '08' => 'h',
        '09' => 'i',
        '10' => 'j',
        '11' => 'k',

        '12' => 'l',
        '13' => 'm',
        '14' => 'n',
        '15' => 'o',
        '16' => 'p',
        '17' => 'q',
        '18' => 'r',
        '19' => 's',
        '20' => 't',
    );

    public static $codeTransfer = [
        "01" => 1,
        "02" => 2,
        "03" => 3,
        "04" => 4,
        "05" => 5,
        "06" => 6,
        "07" => 7,
        "08" => 8,
        "09" => 9,
        "10" => 10,
        "11" => 11,
        "12" => 12,
        "13" => 13,
        "14" => 14,
        "15" => 15,
        "16" => 16,
        "17" => 17,
        "18" => 18,
        "19" => 19,
        "20" => 20,
    ];

    // 将 01 - 20 转成 单字符 a b c,以便跟数字形统一逻辑
    public function convertKlsfCodes($lt, $encode = true)
    {
        $keys   = array_keys(self::$_abc);
        $values = array_values(self::$_abc);

        if ($encode) {
            if (is_array($lt)) {
                foreach ($lt as &$l) {
                    $l = str_replace($keys, $values, $l);
                }

            } else {
                $lt = str_replace($keys, $values, $lt);
            }
        } else {
            if (is_array($lt)) {
                foreach ($lt as &$l) {
                    $l = str_replace($values, $keys, $l);
                }
            } else {
                $lt = str_replace($values, $keys, $lt);
            }
        }

        return $lt;
    }
}
