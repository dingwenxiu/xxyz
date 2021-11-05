<?php

namespace App\Models\Partner;

use App\Models\BaseCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerMessage extends Base
{
    use BaseCache;
    protected $table = "partner_message";

    protected $fillable = ['title', 'content', 'user_type', 'user_id'];

    /**
     * 添加过滤参数
     * @var array
     */
    public $rules = [
		'user_type'   => 'required|in:1,2,3',
		'title'       => 'required|min:2|max:32',
		'content'     => 'required',
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $create_messages = [
		'user_type'            => '用户类型必须选择',
		'title.required'       => '标题不能为空',
		'content.required'     => '内容不能为空',
    ];

    static $userTypes = [
        1 => '直属',
        2 => '代理',
        3 => '指定用户',
    ];


    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c) {
		$query = self::orderBy('id', 'DESC');

        // 商户标识
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        if (isset($c['title'], $c['title'])) {
            $query->where('title', $c['title']);
        }

        if (isset($c['user_type']) && $c['user_type']) {
			$query->where('user_type', $c['user_type']);
		}


		$currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total,  'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    /**
     * 保存
     * @param $data
     * @return bool|string
     */
    public function saveItem($data) {
        $validator  = Validator::make($data, $this->rules, $this->create_messages);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $datas = [];
        foreach ($data['username'] as $item) {
			$datas[$item] = 0;
		}

        $dataNew = [
			'partner_sign' => $data['partner_sign'],
			'title'        => $data['title'],
			'content'      => $data['content'],
			'user_type'    => $data['user_type'],
			'user_config'  => serialize($datas),
			'created_at'   => date("Y-m-d H:i:s")
		];

		self::insert($dataNew);

        return true;
    }

}
