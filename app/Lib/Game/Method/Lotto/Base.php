<?php namespace App\Lib\Game\Method\Lotto;

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

class Base extends \App\Lib\Game\Method\Base{}
