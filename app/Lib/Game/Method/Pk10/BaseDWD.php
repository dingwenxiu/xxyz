<?php namespace App\Lib\Game\Method\Pk10;

// pk10 定位单胆
trait BaseDWD {
    public $positionsTpl    = array('1' => '冠军', '2' => '亚军', '3' => '季军', '4' => '第四名', '5' => '第五名', '6' => '第六名', '7' => '第七名', '8' => '第八名', '9' => '第九名', '10' => '第十名',);
    public $supportExpand   = true;

    public function expand($sCodes, $pos = null)
    {
        $result = [];
        $aCodes = explode('|', $sCodes);
        foreach($aCodes as $index => $code) {
            if(trim($code) === '') continue;
            switch($index){
                case 0:
                    $methodId = $this->id . "_1";
                    break;
                case 1:
                    $methodId = $this->id . "_2";
                    break;
                case 2:
                    $methodId = $this->id . "_3";
                    break;
                case 3:
                    $methodId = $this->id . "_4";
                    break;
                case 4:
                    $methodId = $this->id . "_5";
                    break;
                case 5:
                    $methodId = $this->id . "_6";
                    break;
                case 6:
                    $methodId = $this->id . "_7";
                    break;
                case 7:
                    $methodId = $this->id . "_8";
                    break;
                case 8:
                    $methodId = $this->id . "_9";
                    break;
                case 9:
                    $methodId = $this->id . "_10";
                    break;
                default:
                    $methodId = "";
            }

            if(!$methodId) continue;

            $result[]   = array(
                'method_sign' => $methodId,
                'codes'     => $code,
                'count'     => count(explode('&', $code)),
            );
        }

        return $result;
    }
}
