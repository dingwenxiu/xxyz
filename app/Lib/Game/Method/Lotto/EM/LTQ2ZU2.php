<?php namespace App\Lib\Game\Method\Lotto\EM;

use App\Lib\Game\Method\Lotto\Base;

// 二组选
class LTQ2ZU2 extends Base
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
    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=rand(2,count(self::$filterArr));
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($sCodes){
        return implode('&',explode('|',$sCodes));
    }

    public function regexp($sCodes)
    {
        //格式
        if (!preg_match("/^((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01]))$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        //重复
        $aCode = explode("&", $sCodes);
        $nums = count(array_filter(array_unique($aCode),function($v) use($filterArr) {
            return isset($filterArr[$v]);
        }));

        if($nums==0){
            return false;
        }

        if($nums != count($aCode)) return false;

        return true;
    }

    public function count($sCodes)
    {
        //C(n,2)
        $n = count(explode("&",$sCodes));

        return $this->getCombinCount($n,2);
    }

    public function bingoCode(Array $numbers)
    {
        $exists=array_flip($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[]=intval(isset($exists[$_code]));
        }

        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes = $this->convertLtCodes($sCodes);
        $numbers = $this->convertLtCodes($numbers);

        //非对子
        if ($numbers[0] != $numbers[1]) {
            $preg = "|[" . str_replace('&', '', $aCodes) . "]{2}|";

            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        }
    }


    //检查封锁
    public function tryLockScript($sCodes,$plan,$prizes,$lockvalue)
    {
        //01&03&04&05
        $aCodes = explode('&', $sCodes);
        $aP1=$this->getCombination($aCodes,2);
        $codes=[];
        foreach($aP1 as $v1){
            $v1=explode(' ',$v1);
            sort($v1);
            $codes[implode(' ',$v1)]=1;
        }
        if(count($codes)){
            $codes="'".implode("','",array_keys($codes))."'";
        }else{
            $codes='';
        }

        $pos=array_keys(array_intersect($this->lottery->position,$this->levels[1]['position']));
        array_walk($pos,function(&$v){$v++;});

        $script=
            <<<LUA

LUA;

        $max=$lockvalue-$prizes[1];
        $script.= <<<LUA

exists=cmd('exists','{$plan}')

if exists==0 and {$max}<0 then
    do return 0 end
end

ret=cmd('zrangebyscore','{$plan}',{$max},'+inf')
if (#ret==0) then
    do return 1 end
end

codes={{$codes}}
_codes={}
for _,str in pairs(ret) do
    _codes={}
    str:gsub("%w+",function(c) table.insert(_codes,c) end)
    _codes={_codes[{$pos[0]}],_codes[{$pos[1]}]}
    table.sort(_codes)
    _code=table.concat(_codes,' ')

    for _,code in pairs(codes) do
        if code==_code then
            do return 0 end
        end
    end
end

do return 1 end

LUA;

        return $script;
    }

    //写入封锁值
    public function lockScript($sCodes,$plan,$prizes)
    {
        //01&03&04&05
        $aCodes = explode('&', $sCodes);
        $aP1=$this->getCombination($aCodes,2);
        $codes=[];
        foreach($aP1 as $v1){
            $v1=explode(' ',$v1);
            sort($v1);
            $codes[implode(' ',$v1)]=1;
        }
        if(count($codes)){
            $codes="'".implode("','",array_keys($codes))."'";
        }else{
            $codes='';
        }

        $pos=array_keys(array_intersect($this->lottery->position,$this->levels[1]['position']));
        array_walk($pos,function(&$v){$v++;});

        $script='';
        //不同奖级的中奖金额
        $script.= <<<LUA

x={'01','02','03','04','05','06','07','08','09','10','11'}
codes={{$codes}}
_codes={}

for i1,a in pairs(x) do
for i2,b in pairs(x) do
for i3,c in pairs(x) do
for i4,d in pairs(x) do
for i5,e in pairs(x) do
	if i1==i2 or i1==i3 or i1==i4  or i1==i5
		or i2==i3  or i2==i4  or i2==i5
		or i3==i4  or i3==i5
		or i4==i5 then

	else
	    _codes={a,b,c,d,e}
        _codes={_codes[{$pos[0]}],_codes[{$pos[1]}]}
        table.sort(_codes)
        _code=table.concat(_codes,' ')

	    for _,code in pairs(codes) do
            if code==_code then
	            cmd('zincrby','{$plan}',{$prizes[1]},table.concat({a,b,c,d,e},' '))
	            break
            end
	    end
	end
end
end
end
end
end

LUA;

        return $script;
    }

}
