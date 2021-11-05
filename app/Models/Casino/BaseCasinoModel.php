<?php
namespace App\Models\Casino;

use App\Lib\BaseCache;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseCasinoModel
 * @package App\Models\Casino
 */
class BaseCasinoModel extends Model
{
    use BaseCache;
    /**
     * @var string
     */
    public $errMsg = '';

    /**
     * @param array   $data 更新数据.
     * @param integer $id   更新ID.
     * @return boolean
     */
    public function saveBase(array $data, int $id)
    {
        $selfObj = $this;
        if ($id) {
            $selfObj = self::where('id', $id)->first();
        }

        $field = ['id', '_token'];
        foreach ($data as $key => $v) {
            if (!in_array($key, $field)) {
                $selfObj->$key   = $v;
            }
        }

        $selfObj->save();
        return true;
    }
}
