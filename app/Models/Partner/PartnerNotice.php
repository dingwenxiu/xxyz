<?php

namespace App\Models\Partner;

use App\Models\BaseCache;
use Illuminate\Support\Facades\Validator;
use function Complex\sec;

class PartnerNotice extends Base
{
    use BaseCache;
    protected $table = "partner_notice";

    protected $fillable = ['notice_image'];
    public $rules = [
        'type'                  => 'required|in:1,2,3',
        'device_type'           => 'required|in:1,2,3',
        'title'                 => 'required|min:2|max:128',
        'content'               => 'required',
        'start_time'            => 'required',
        'end_time'              => 'required',
    ];

    static $types = [
        1 => "普通公告",
        2 => "维护公告",
        3 => "弹窗公告"
    ];

    static $deviceTypes = [
        1 => "通用",
        2 => "手机",
        3 => "WEB"
    ];

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 20) {
        $query = self::select('partner_notice.*','partners.name')->leftjoin('partners','partners.sign','=','partner_notice.partner_sign')->orderBy('partner_notice.top_score', 'desc');

        // 商户标识
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_notice.partner_sign', $c['partner_sign']);
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c["type"] != 'all') {
            $query->where('partner_notice.type', $c['type']);
        }

        // device_type
        if (isset($c['device_type']) && $c['device_type']) {
            $query->whereIn('partner_notice.device_type', $c['device_type']);
        }

        if (isset($c['status'])) {
            $query->where('partner_notice.status', $c['status']);
        }

        if (isset($c['timeNow']) && $c['timeNow']) {
            $query->where('partner_notice.start_time', '<=', $c['timeNow'])->where('partner_notice.end_time', '>=', $c['timeNow']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        foreach ($data as $key => $item) {
            $item->start_time       = date('Y-m-d H:i:s', $item->start_time);
            $item->end_time         = date('Y-m-d H:i:s', $item->end_time);

            $item->type_descs        = self::$types[$item->type];
            $item->device_type_desc  = self::$deviceTypes[$item->device_type];
        }
        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 置顶
    public function setTop() {
        $maxScoreItem       = self::orderBy("top_score", "desc")->first();
        $this->top_score    = $maxScoreItem->top_score + 1;
        $this->save();

        PartnerNotice::_flushCache("notice_" . $this->partner_sign);
        return true;
    }

    // 修改状态
    public function changeStatus() {
        $this->status = $this->status > 0 ? 0 : 1;
        $this->save();
        PartnerNotice::_flushCache("notice_" . $this->partner_sign);
        return true;
    }

    // 保存
    public function saveItem($data, $partnerSign, $partnerAdminUser = null) {
        $validator  = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 如果是编辑 是否有权限
        if ($this->id && $partnerSign != $this->partner_sign) {
            return "对不起, 您没有操作的权限!";
        }

        $this->type                         = $data['type'];

        // 不能编辑的字段
        if (!$this->id) {
            $this->partner_sign             = $partnerSign;
            $this->add_partner_admin_id     = $partnerAdminUser ? $partnerAdminUser->id : 0;
        }

        $this->device_type              = $data['device_type'];
        $this->title                    = $data['title'];
        $this->type_desc                = $data['type_desc'];
        $this->content                  = $data['content'];
        $this->start_time               = strtotime($data['start_time']);
        $this->end_time                 = strtotime($data['end_time']);
        $this->save();
        PartnerNotice::_flushCache("notice_" . $this->partner_sign);

        return true;
    }

	/**
	 * @param $partnerSign
	 * @param $c
	 * @return mixed
	 * @throws \Exception
	 */
    static function getDataFromCache($partnerSign, $c) {
        if (self::_hasCache('notice_' . $partnerSign)) {
            return self::_getCacheData('notice_' . $partnerSign);
        } else {
            $allCache = self::getAllNotice($partnerSign, $c);
            if ($allCache) {
                self::_saveCacheData('notice_' . $partnerSign, $allCache);
            }
            return $allCache;
        }
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    static function getTipNotice() {
        $data = self::getDataFromCache();
        if (!$data) {
            return "";
        }

        foreach ($data as $item) {
            if ($item['type'] == 3) {
                return $item['content'];
            }
        }

        return "";
    }

	/**
	 * 获取所有公告
	 * @param null $partnerSign
	 * @param $c
	 * @return array
	 */
    static function getAllNotice($partnerSign = null,$c) {
        $time = gettimeofday();
        $query = self::where('status', 1)->where('start_time', '<=', $time['sec'])->orderBy("top_score", 'desc')->orderBy("id", 'desc');
        if ($partnerSign) {
            $query->where("partner_sign", $partnerSign);
        }

		$pageSize = 20;
		$currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
		$pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
		$offset         = ($currentPage - 1) * $pageSize;

		$total  = $query->count();
		$data   = $query->skip($offset)->take($pageSize)->get()->toArray();
		return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    static function getListForApi() {
        $data = self::getDataFromCache();
        $time = time();
        foreach ($data as $val){
            if($val['start_time'] <= $time && $val['end_time'] >= $time){
                return $val['title'];
            }
        }
        return '暂无公告!!!';
    }

    /**
     * 保存图片
     * @param $imageObj
     * @param $path
     * @param $picSource
     * @return mixed
     */
    private function savePic($imageObj, $path, $picSource)
    {
        $picSavePath = $imageObj->depositPath($path, 1, 1);
        $previewPic = $imageObj->uploadImg($picSource, $picSavePath);
        return $previewPic;
    }
}
