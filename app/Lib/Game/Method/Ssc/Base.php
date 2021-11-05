<?php namespace App\Lib\Game\Method\Ssc;

// 基础处理
trait rx_expands_normal {
    public $positionsTpl = array('w'=>'万','q'=>'千','b'=>'百','s'=>'十','g'=>'个');
    public $supportExpand=true;

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

trait rx_expands_zx {
    public $positionsTpl = array('w'=>'万','q'=>'千','b'=>'百','s'=>'十','g'=>'个');

    public $supportExpand=true;

    public function checkPos($pos)
    {
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
        $positions=array('w'=>'','q'=>'','b'=>'','s'=>'','g'=>'');
        if(!$this->pos){
            //集合
            $pos=(array) array_rand($positions,$this->expands['num']);
        }else{
            $pos=str_split($this->pos);
        }

        //按为生成随机
        foreach($pos as $k=>$v){
            $positions[$v]=parent::randomCodes();
        }

        return implode('|',$positions);
    }

    public function expand($sCodes,$pos=null)
    {
        $result=[];
        $aCodes=explode('|',$sCodes);
        $pos=[];
        $codes=[];
        foreach($aCodes as $index=>$code){
            if(trim($code)==='') continue;
            switch($index){
                case 0:
                    $pos[]='w';
                    $codes['w']=trim($code);
                    break;
                case 1:
                    $pos[]='q';
                    $codes['q']=trim($code);
                    break;
                case 2:
                    $pos[]='b';
                    $codes['b']=trim($code);
                    break;
                case 3:
                    $pos[]='s';
                    $codes['s']=trim($code);
                    break;
                case 4:
                    $pos[]='g';
                    $codes['g']=trim($code);
                    break;
            }
        }

        if(empty($pos)) return [];

        $aP1 = $this->getCombination($pos,$this->expands['num']);

        foreach($aP1 as $v){
            $v=str_replace(' ','',$v);
            $vs=str_split($v);
            $code=implode('|',array_intersect_key($codes,array_flip($vs)));

            $result[]=array(
                'methodid'=>$this->id."@".implode('',$vs),
                'codes'=>$code,
                'num'=>$this->count($code),
            );
        }

        return $result;
    }
}


//定位胆
trait expands_dwd {
    public $positionsTpl = array('w'=>'万','q'=>'千','b'=>'百','s'=>'十','g'=>'个');

    public $supportExpand=true;

    public function bingoCode(Array $numbers)
    {
        $result=[];
        $arr=[0,1,2,3,4,5,6,7,8,9];

        foreach($numbers as $pos=>$code){
            $tmp=[];
            foreach($arr as $_code){
                $tmp[]=intval($code==$_code);
            }
            $result[]=$tmp;
        }

        return $result;
    }

    //供测试用 生成随机投注
    public function randomCodes(&$poss=array())
    {
        $positions=array('w'=>'','q'=>'','b'=>'','s'=>'','g'=>'');
        if(!$this->pos){
            //集合
            $pos=(array) array_rand($positions,rand(1,count($positions)));
        }else{
            $pos=str_split($this->pos);
        }

        //按为生成随机
        foreach($pos as $k=>$v){
            $positions[$v]=parent::randomCodes();
        }

        return implode('|',$positions);
    }

    public function expand($sCodes,$pos=null)
    {
        $result=[];
        $aCodes=explode('|',$sCodes);
        foreach($aCodes as $index=>$code){
            if(trim($code)==='') continue;
            switch($index){
                case 0:
                    $methodid=$this->id."@w";
                    break;
                case 1:
                    $methodid=$this->id."@q";
                    break;
                case 2:
                    $methodid=$this->id."@b";
                    break;
                case 3:
                    $methodid=$this->id."@s";
                    break;
                case 4:
                    $methodid=$this->id."@g";
                    break;
                default:
                    $methodid="";
            }
            if(!$methodid) continue;

            $result[]=array(
                'methodid'=>$methodid,
                'codes'=>$code,
                'num'=>count(explode('&',$code)),
            );

        }

        return $result;
    }
}


//定位胆3
trait expands_dwd3 {
    public $positionsTpl = array('b'=>'百','s'=>'十','g'=>'个');

    public $supportExpand=true;

    public function bingoCode(Array $numbers)
    {
        $result=[];
        $arr=[0,1,2,3,4,5,6,7,8,9];

        foreach($numbers as $pos=>$code){
            $tmp=[];
            foreach($arr as $_code){
                $tmp[]=intval($code==$_code);
            }
            $result[]=$tmp;
        }

        return $result;
    }

    //供测试用 生成随机投注
    public function randomCodes(&$poss=array())
    {
        $positions=array('b'=>'','s'=>'','g'=>'');
        if(!$this->pos){
            //集合
            $pos=(array) array_rand($positions,rand(1,count($positions)));
        }else{
            $pos=str_split($this->pos);
        }

        //按为生成随机
        foreach($pos as $k=>$v){
            $positions[$v]=parent::randomCodes();
        }

        return implode('|',$positions);
    }

    public function expand($sCodes,$pos=null)
    {
        $result=[];
        $aCodes=explode('|',$sCodes);
        foreach($aCodes as $index=>$code){
            if(trim($code)==='') continue;
            switch($index){
                case 0:
                    $methodid=$this->id."@b";
                    break;
                case 1:
                    $methodid=$this->id."@s";
                    break;
                case 2:
                    $methodid=$this->id."@g";
                    break;
                default:
                    $methodid="";
            }
            if(!$methodid) continue;

            $result[]=array(
                'methodid'=>$methodid,
                'codes'=>$code,
                'num'=>count(explode('&',$code)),
            );
        }
        return $result;
    }
}

class Base extends \App\Lib\Game\Method\Base {

    /** ========================　展开 ======================= */

    /**
     * @param $codeArr
     * @return array
     */
    public function unpPackZu2($codeArr)
    {
        $aP1    = $this->getCombination($codeArr, 2);
        $z2     = [];
        foreach($aP1 as $v1){
            $v1     = explode(' ', $v1);
            $z2[$v1[0] . $v1[1]]   = 1;
            $z2[$v1[1] . $v1[0]]   = 1;
        }

        return $z2;
    }

    /**
     * @param $codeArr
     * @return array
     */
    public function unpPackZu3($codeArr)
    {
        $aP1    = $this->getCombination($codeArr, 2);
        $z3     = [];
        foreach($aP1 as $v1){
            $v1 = explode(' ', $v1);
            $z3[$v1[0].$v1[0].$v1[1]]=1;
            $z3[$v1[0].$v1[1].$v1[0]]=1;
            $z3[$v1[1].$v1[0].$v1[0]]=1;
            $z3[$v1[1].$v1[1].$v1[0]]=1;
            $z3[$v1[1].$v1[0].$v1[1]]=1;
            $z3[$v1[0].$v1[1].$v1[1]]=1;
        }

        return $z3;
    }

    /**
     * @param $codeArr
     * @return array
     */
    public function unpPackZu6($codeArr)
    {
        $aP1    = $this->getCombination($codeArr, 3);
        $z6     = [];
        foreach($aP1 as $v1){
            $v1 = explode(' ', $v1);
            $z6[$v1[0].$v1[1].$v1[2]]=1;
            $z6[$v1[0].$v1[2].$v1[1]]=1;
            $z6[$v1[1].$v1[0].$v1[2]]=1;
            $z6[$v1[1].$v1[2].$v1[0]]=1;
            $z6[$v1[2].$v1[0].$v1[1]]=1;
            $z6[$v1[2].$v1[1].$v1[0]]=1;
        }

        return $z6;
    }

    /**
     * @param $codeArr
     * @return array
     */
    public function getKd($codeArr)
    {
        sort($codeArr);
        return $codeArr[count($codeArr) - 1] - $codeArr[0];
    }

    /**
     * @param $codeArr
     * @return array
     */
    public function getRepeatCode($codeArr, $count = 2)
    {
        $data = [];
        foreach ($codeArr as $_code) {
            if ($count == 2) {
                $data[$_code.$_code] = 1;
            } else if ($count == 3){
                $data[$_code.$_code.$_code] = 1;
            } else if ($count == 4){
                $data[$_code.$_code.$_code.$_code] = 1;
            }
        }
        return $data;
    }

    public function getRepeatCombination($codeArr, $count = 2)
    {
        $data       = [];
        $codeArr    = $this->getCombination($codeArr, $count);

        foreach ($codeArr as $_code) {
            $_codeArr = explode(' ', $_code);
            if (count(array_unique($_codeArr)) == 1) {
                $data[] = implode('', $_codeArr);
            }
        }

        return $data;
    }

    public function getBdwCode2($aCodes) {
        $aCodes = $this->getCombination($aCodes, 2);

        $data = [];
        foreach ($aCodes as $_code) {
            $_codeArr = explode(' ', $_code);
            $data[$_codeArr[0].$_codeArr[1]] = 1;
            $data[$_codeArr[1].$_codeArr[0]] = 1;
        }

        return $data;
    }

    public function getBdwCode3($aCodes) {
        $aCodes = $this->getCombination($aCodes, 3);

        $data = [];
        foreach ($aCodes as $_code) {
            $_codeArr = explode(' ', $_code);
            $data[$_codeArr[0].$_codeArr[1].$_codeArr[2]] = 1;
            $data[$_codeArr[0].$_codeArr[2].$_codeArr[1]] = 1;
            $data[$_codeArr[1].$_codeArr[0].$_codeArr[2]] = 1;
            $data[$_codeArr[1].$_codeArr[2].$_codeArr[0]] = 1;
            $data[$_codeArr[2].$_codeArr[1].$_codeArr[0]] = 1;
            $data[$_codeArr[2].$_codeArr[0].$_codeArr[1]] = 1;
        }

        return $data;
    }
}
