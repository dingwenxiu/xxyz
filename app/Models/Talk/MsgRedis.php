<?php

namespace App\Models\Talk;

use App\Lib\Help;
use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use GatewayClient\Gateway;
use App\Models\Player\Player;

/**
 * 玩家聊天
 * Class MsgRedis
 */
class MsgRedis extends Model
{
    private $redisDataNum = 15;//默认
    private $userControl = 13;//用户聊天
    private $friend = 14;//用户关系
    private $service = 12;//客服
    private $serviceWork = 11;//客服工作
    private $serviceGro = 10;//客服聊天 消息通道
    private $serviceClient = 9;//用户与客服聊天双向
    private $askList = 8;//咨询列表 服务列表
    private $userId;
    private $writeData;
    private $typeMsg;
    private $onLine = 1;
    private $offLine = 2;
    private $historyGroupLimit = 2000;//聊天组设定存储条数;每条通道限定2001条信息
    //内部监听端口
    private $gateIp;
    private $redis;
    private $partner_sign;

    public function __construct()
    {
        $userId = isset(auth()->user()->id) ? auth()->user()->id : -999;
        $this->partner_sign = isset(auth()->user()->partner_sign) ? auth()->user()->partner_sign : 'system';
        $this->userId = $userId;
        $this->writeData['time'] = microtime();
        $this->typeMsg = array(
            'one' => 1,//一对一消息
            'group' => 2,//多对1代收消息，群组消息
            'friend' => 3,//加好友请求消息
            'joinFriendBack' => 4,
            'joinGroup' => 5,
            'userControl' => 6,//个人消息  被禁止发言
            'downLine' => 7,//用户被踢下线
            'moneyRecode' => 8,
            'unUserControl' => 9,//个人消息  取消禁止发言
            'userControlToGroup' => 10,//群组消息  禁止发言
            'unUserControlToGroup' => 11,//群组消息  取消禁止发言
            'groupTalkClose' => 12,//群组消息  关闭群组聊天功能
            'groupTalkOpen' => 13,//群组消息  开启群组聊天功能
            'outRoom' => 14,//个人消息  踢出房间
            'service' => 15,//客服回复
            'clientToService' => 16,//用户咨询客服

        );
        //$this->gateIp = config('app.talk');
        $this->gateIp = configure('system_wk');
        if ($this->gateIp == null) {
            exit('未配置聊天通道!');
        }
        Gateway::$registerAddress = $this->gateIp;
        $this->redis = Redis::connection('talk');
        $this->redis->command('select', [$this->redisDataNum]);

    }

    //发送消息给个人
    public function sendMsgUser($params)
    {
        $this->writeData['msg'] = $params['msg'];
        $this->writeData['send_user_id'] = $params['send_user_id'];
        $this->writeData['userStatus'] = $this->offLine;
        $this->writeData['type'] = $this->typeMsg['one'];
        $this->writeData['other'] = isset($params['other']) ? $params['other'] : '';
        $this->writeData['resource'] = $this->userId;
        return $this->sendMsg($this->writeData['send_user_id'], $this->writeData);
    }

    public function getFriend($system = false)
    {
        if ($system)
            $this->userId = $system;
        $key = $this->getFriendKey($this->userId);
        $this->redis->command('select', [$this->friend]);
        $relation = $this->redis->get($key);

        if (!$relation)
            $relation = $this->setFriend($this->userId);

        $relation = json_decode($relation);
        $relation->service = $this->searchService(2);

        return Help::returnApiJson(
            '获取成功!', 1, $relation);
    }

    //刷新用户上下级关系缓存
    public function setFriend($userId)
    {
        $this->redis->command('select', [$this->friend]);
        $key = $this->getFriendKey($userId);
        $user = Player::find($userId);

        if ($user["parent_id"] == 0)
            $res['parent'] = false;
        else
            $res['parent'] = Player::select("id", "user_icon")->find($user["parent_id"])->toArray();

        $res["second"] = Player::select("id", "user_icon as avatar", "username")->where("parent_id", $userId)->get()->toArray();
        $res["service"] = $user["partner_sign"];
        $res = json_encode($res, JSON_UNESCAPED_UNICODE);
        $this->redis->set($key, $res);
        $this->redis->expire($key, 604800);//非主动刷新设置七天过期，
        return $res;
    }

    public function getFriendKey($userId)
    {
        return $this->partner_sign . '-' . "friend-" . $userId;
    }

    public function clearCache($params)
    {
        $this->redis->command("select", [$this->friend]);
        $cusor = 0;
        switch ($params['type']) {
            case 1:
                $key = $this->partner_sign . "*";
                $option = array(
                    "MATCH" => $key,
                    "COUNT" => 2000,
                );
                while (true) {
                    $res = $this->redis->scan($cusor, $option);
                    $cusor = $res[0];

                    if (!empty($res[1]))
                        $this->redis->del($res[1]);

                    if ($res[0] == 0)
                        break;
                }
                return Help::returnApiJson('商户用户用户聊天关系缓存刷新成功!', 1);
                break;
            case 2:
                $this->redis->flushdb();
                return Help::returnApiJson('全系统所有商户用户聊天关系缓存刷新成功!', 1);
                break;
        }

        return Help::returnApiJson('刷洗失败-传入参数错误!', 0);
    }

    public function talkDelete($params)
    {
        $this->redis->command("select", [$this->userControl]);
        $cusor = 0;
        switch ($params['type']) {
            case 1:
                $key = $this->partner_sign . "*";
                $option = array(
                    "MATCH" => $key,
                    "COUNT" => 2000,
                );
                while (true) {
                    $res = $this->redis->scan($cusor, $option);
                    $cusor = $res[0];

                    if (!empty($res[1]))
                        $this->redis->del($res[1]);

                    if ($res[0] == 0)
                        break;
                }
                return Help::returnApiJson($this->partner_sign . '商户下所有用户的聊天记录已删除!', 1);
                break;
            case 2:
                if (!isset($params["send_user_id"])) {
                    return Help::returnApiJson('删除失败-传入参数错误!', 0);
                }

                $key = "*" . $params["send_user_id"] . "*";
                $option = array(
                    "MATCH" => $key,
                    "COUNT" => 2000,
                );
                while (true) {
                    $res = $this->redis->scan($cusor, $option);
                    $cusor = $res[0];

                    if (!empty($res[1]))
                        $this->redis->del($res[1]);

                    if ($res[0] == 0)
                        break;
                }
                return Help::returnApiJson('用户ID[' . $params["send_user_id"] . ']' . '所有的聊天记录已经双向删除', 1);
                break;
            case 3:
                if (!isset($params["send_user_id"]) || !isset($params["re_user_id"])) {
                    return Help::returnApiJson('删除失败-传入参数错误!', 0);
                }
                $key = $this->createHandKey(array(
                    'send_user_id' => $params['send_user_id'],
                    're_user_id' => $params['re_user_id']
                ), 2);

                $this->redis->del($key);
                return Help::returnApiJson('删除成功，已删除用户ID[' . $params['send_user_id'] . ']与用户ID为[' . $params['re_user_id'] . ']之间的聊天记录', 1);
                break;
        }

        return Help::returnApiJson('刷洗失败-传入参数错误!', 0);
    }

    public function clearTalkHistoryAll()
    {
        $this->redis->command('select', [$this->userControl]);
        $this->redis->flushdb();
        return Help::returnApiJson('全系统所有用户的聊天记录已删除!', 1);
    }

    //发送消息 离线和在线(管理缓存)
    public function sendMsg($userId, $data)
    {
        $revUserStatus = $this->checkUserOnline($userId);
        if ($revUserStatus) {
            //在线 直接发送
            $data['userStatus'] = $this->onLine;
            $sendPack = json_encode($data, JSON_UNESCAPED_UNICODE);
            Gateway::sendToUid($userId, $sendPack);
        } else {
            //离线 加入离线消息
            $data['userStatus'] = $this->offLine;
            $this->redis->lpush($userId, json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        $this->writeHistory($data);
        return Help::returnApiJson(
            '发送成功!', 1, json_encode($data, JSON_UNESCAPED_UNICODE)
        );

    }

    private function writeHistory($data,$model=1)
    {
        switch ($model){
            case 2:
                $key = $this->createHandKey($data);
                $this->redis->command('select', [$this->serviceGro]);
                $this->redis->lpush($key, json_encode($data, JSON_UNESCAPED_UNICODE));
                break;
            case 3:
                $key = $data['serviceUser'];
                $this->redis->command("select",[$this->serviceClient]);
                $this->redis->lpush($key, json_encode($data, JSON_UNESCAPED_UNICODE));
                break;
            default:
                $key = $this->createHandKey($data);
                $this->redis->command('select', [$this->userControl]);
                $this->redis->lpush($key, json_encode($data, JSON_UNESCAPED_UNICODE));
                break;
        }

        $range = $this->redis->llen($key);
        if ($range > $this->historyGroupLimit) {
            $start = $range - ($this->historyGroupLimit);
            $this->redis->ltrim($key, 0, -$start);
        }
        return true;
    }

    private function createHandKey($pkg, $flag = 1)
    {
        if ($flag === 1) {
            //组建通道唯一key
            $key = $this->userId < $pkg['send_user_id'] ? $this->userId . "-" . $pkg['send_user_id'] : $pkg['send_user_id'] . "-" . $this->userId;
            return $this->partner_sign . '-' . $key;
        } else if ($flag === 2) {
            $key = $pkg['re_user_id'] < $pkg['send_user_id'] ? $pkg['re_user_id'] . "-" . $pkg['send_user_id'] : $pkg['send_user_id'] . "-" . $pkg['re_user_id'];
            return $this->partner_sign . '-' . $key;
        } else {
            return false;
        }

    }

    //检测用户是否在线(ID查询)
    public function checkUserOnline($userId)
    {
        $onlineFlag = Gateway::getClientIdByUid($userId);
        if (count($onlineFlag) == 0) {
            return false;
        }
        return $onlineFlag;
    }

    public function serviceBind($params)
    {
        //改变客服ID通道组成
        $this->userId = $this->partner_sign . $this->userId;
        return $this->bindU($params);
    }

    /*绑定用户(用户标志与句柄绑定)*/
    public function bindU($params)
    {
        $clientId = $params['client_id'];
        $userId = $this->userId;
        //判断用户是否已经绑定
        $onlineFlag = Gateway::getClientIdByUid($userId);

        $resData = array('clientId' => $clientId, 'userId' => $userId);
        //重复绑定
        if (count($onlineFlag) == 1 && $onlineFlag[0] == $clientId) {
            return Help::returnApiJson('用户已经绑定客户端，请勿重复绑定!', 1, $resData);
        }
        //解除之前与其他平台发送解除信息
        if (count($onlineFlag) > 0) {
            $this->writeData['msg'] = '检测用户在其他端登陆，断开当前连接';
            $this->writeData['type'] = $this->typeMsg['downLine'];
            Gateway::sendToUid($userId, json_encode($this->writeData, JSON_UNESCAPED_UNICODE));
        }
        //解除历史绑定
        foreach ($onlineFlag as $value) {
            Gateway::unbindUid($value, $userId);
        }
        //进行绑定
        Gateway::bindUid($clientId, $userId);
        $resData['msg'] = MsgInits::UserInit();
        return Help::returnApiJson('绑定用户成功!', 1, $resData);
    }

    public function bindUPublic($params)
    {
        $this->userId = $params['touch_id'];
        return $this->bindU($params);
    }

    public function getTalkHistoryUnLogin($params)
    {
        $this->userId='YKFW-'.$params['touch_id'];
        $this->partner_sign = $params['partner_sign'];

        //判断来源是否和访问IP相同
        $data = $params;
        $data['send_user_id'] =  $this->partner_sign.$params['service_id'];
        $checkKey = $this->createHandKey($data);
        $this->redis->command('select',[$this->serviceGro]);
        $checkRes = $this->redis->lrange($checkKey,0,2);
        if(empty($checkRes) ){
            return Help::returnApiJson(
                '获取成功!', 1,[]
            );
        }
        $checkRes[0] = json_decode($checkRes[0]);
        $currentIp = $this->clientIp();
        if($currentIp != $checkRes[0]->client_ip){
            return Help::returnApiJson(
                '获取失败-非法获取!', 0
            );
        }
        /*--判断结束--*/

        $params['uid'] = json_encode(array(
            'YKFW-'.$params['touch_id']
        ));
        return $this->serviceHistory($params);
    }

    //客户端句柄查询(句柄查询)
    public function isCidOnLin($params)
    {
        $onlineFlag = Gateway::isOnline($params['cid']);
        if ($onlineFlag === 1) {
            return Help::returnApiJson(
                '在线!', 1
            );
        } else {
            return Help::returnApiJson(
                '已经下线!', 0
            );
        }
    }

    private function getServiceKey($partner_sign)
    {
        return $partner_sign . '-service';
    }

    //查询聊天商户的聊天通道
    public function searchService($model = 1)
    {
        $this->redis->command("select", [$this->service]);
        $key = $this->getServiceKey($this->partner_sign);
        $res = $this->redis->get($key);
        if (!$res)
            $res = $this->serviceInit($this->partner_sign, $this->userId);

        $res = json_decode($res);
        switch ($model) {
            case 1:
                return Help::returnApiJson(
                    '查询成功!', 1, $res
                );
            case 2:
                return $res;
        }
        return Help::returnApiJson(
            '请输入调用类型!', 0
        );
    }

    public function searchServicePublic($params)
    {
        $this->redis->command("select", [$this->service]);
        $key = $this->getServiceKey($params['partner_sign']);
        $res = $this->redis->get($key);
        if (!$res)
            $res = $this->serviceInit($this->partner_sign, $this->userId);

        $res = json_decode($res);
        return Help::returnApiJson(
            '查询成功!', 1, $res
        );
    }

    //开设客服通道
    public function openService($params)
    {
        $this->redis->command("select", [$this->service]);
        $key = $this->getServiceKey($this->partner_sign);

        $res = new CustomerService();
        $res->partner_sign = $this->partner_sign;
        $res->create_partner = $this->userId;
        $res->title = $params["title"];
        $res->desc = isset($params["desc"]) ? $params["desc"] : '';
        $res->sort = isset($params["sort"]) ? $params["sort"] : 0;
        $res->status = 1;
        $res->save();

        $data = CustomerService::where('partner_sign', $this->partner_sign)->get()->toArray();
        $data = json_encode(array_column($data, NULL, 'id'), JSON_UNESCAPED_UNICODE);

        $this->redis->set($key, $data);
        return Help::returnApiJson(
            '添加成功!', 1, $res
        );
    }

    //关闭客服通道
    public function closeService($params)
    {
        $askListIndexKey=$this->getAksKey($params);//8客服游客访问列表
        $serviceClientIndexKey = 'service-'.$params['service_id']; //9用户消息
        $serviceGroIndexKey = $this->partner_sign.$params['service_id'];//10通道消息
        $serviceWorkIndexKey = $this->getServiceWorkKey($this->partner_sign);//11上下位记录
        $serviceIndex = $this->getServiceKey($this->partner_sign);//12列表缓存

        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }

        $res = $this->searchService(2);
        $flag = $params['service_id'];
        if($res->$flag->service_flag==1){
            return Help::returnApiJson(
                '删除失败-系统默认保留一个客服,您可以关闭该客服位和删除聊天记录!', 0
            );
        }

        if($res->$flag->create_partner !== $this->userId){
            return Help::returnApiJson(
                '关闭失败-您不是该客服台创建人!', 0
            );
        }

        $deStatus = CustomerService::find($flag)->delete();

        if(!$deStatus){
            return Help::returnApiJson(
                '关闭失败-系统错误!', 0
            );
        }
        $this->deleteFlagCache($askListIndexKey,$this->askList);
        $this->deleteFlagCache($serviceClientIndexKey,$this->serviceClient);
        $this->deleteFlagCache($serviceGroIndexKey,$this->serviceGro);
        $this->deleteFlagCache($serviceWorkIndexKey,$this->serviceWork);
        $this->deleteFlagCache($serviceIndex,$this->service);
        return Help::returnApiJson(
            '删除成功-频道所有的记录均以删除!', 1
        );
    }

    //编辑客服台
    public function EditService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }


        $csObj = CustomerService::find($params['service_id']);

        unset($params['service_id']);

        $csObj->fill($params);
        $csObj->save();
        $serviceIndex = $this->getServiceKey($this->partner_sign);//12列表缓存
        $this->deleteFlagCache($serviceIndex,$this->service);

        return Help::returnApiJson(
            '修改成功!', 1
        );

    }

    //删除指定频道的聊天记录
    public function deleteServiceHistory($params)
    {
        $askListIndexKey=$this->getAksKey($params);//8客服游客访问列表
        $serviceClientIndexKey = 'service-'.$params['service_id']; //9用户消息
        $serviceGroIndexKey = $this->partner_sign.$params['service_id'];//10通道消息

        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->deleteFlagCache($askListIndexKey,$this->askList);
        $this->deleteFlagCache($serviceClientIndexKey,$this->serviceClient);
        $this->deleteFlagCache($serviceGroIndexKey,$this->serviceGro);
        return Help::returnApiJson(
            '删除成功!', 1
        );
    }

    private function deleteFlagCache($index,$dbNum){
        $this->redis->command('select',[$dbNum]);
        $key = '*'.$index.'*';
        $cusor = 0;//苗点
        $option = array(
            "MATCH" => $key,
            "COUNT" => 2000,
        );
        while (true) {
            $res = $this->redis->scan($cusor, $option);
            $cusor = $res[0];

            if (!empty($res[1]))
                $this->redis->del($res[1]);

            if ($res[0] == 0)
                break;
        }
    }


    /*参数$partner_sing不是初始化类的必填函数，所以需要传递引用
     * */
    private function getServiceWorkKey($partner_sing)
    {
        return $partner_sing . "-service-work";
    }

    private function checkServiceId($servicesId)
    {
        $res = $this->searchService(2);

        if (!isset($res->$servicesId))
            return false;

        return true;
    }

    //客服上位
    public function upService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->redis->command("select", [$this->serviceWork]);
        $key = $this->getServiceWorkKey($this->partner_sign);
        $res = $this->redis->hget($key, $params['service_id']);

        if (!$res) {
            $struct = ServiceRedis::reServiceWorkStatusHash();
            $struct["partner_sign"] = $this->partner_sign;
            $struct["service_id"] = $params['service_id'];
            $struct["staff_status"] = 1;
            $struct["parent_info"][auth()->user()->id]['username'] = auth()->user()->username;
            $struct["parent_info"][auth()->user()->id]['last_login_ip'] = auth()->user()->last_login_ip;
            $this->redis->hset($key, $params['service_id'], json_encode($struct, JSON_UNESCAPED_UNICODE));

            return Help::returnApiJson(
                '上位成功!', 1, $struct
            );
        } else {
            $res = json_decode($res);
            $userId = $this->userId;
            if (isset($res->parent_info->$userId)) {
                return Help::returnApiJson(
                    '您已经在这个客服位置了，请勿重复上位!', 0, $res
                );
            } else {
                $res = json_decode(json_encode($res), true);

                $res['staff_status']++;
                $res['work_status'] = 1;
                $res["parent_info"][auth()->user()->id]['last_login_ip'] = auth()->user()->last_login_ip;
                $res["parent_info"][auth()->user()->id]['username'] = auth()->user()->username;

                $this->redis->hset($key, $params['service_id'], json_encode($res, JSON_UNESCAPED_UNICODE));

                return Help::returnApiJson(
                    '上位成功!', 1, $res
                );
            }

        }
    }

    //客服下位
    public function downService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->redis->command("select", [$this->serviceWork]);
        $key = $this->getServiceWorkKey($this->partner_sign);
        $res = $this->redis->hget($key, $params['service_id']);
        if (!$res) {
            //初始化
            $struct = ServiceRedis::reServiceWorkStatusHash();
            $struct["partner_sign"] = $this->partner_sign;
            $struct["service_id"] = $params['service_id'];
            $struct["staff_status"] = 1;
            $struct["parent_info"] = [];
            $this->redis->hset($key, $params['service_id'], json_encode($struct, JSON_UNESCAPED_UNICODE));


            return Help::returnApiJson(
                '下位失败-你未曾在此客服台上位!', 0, $struct
            );

        }else {
            //正常下位
            $res = json_decode($res);
            $userId = $this->userId;
            if (isset($res->parent_info->$userId)) {
                $res = json_decode(json_encode($res), true);
                $pNumber=--$res['staff_status'];
                $res['staff_status']=$pNumber<0?0:$pNumber;
                unset($res["parent_info"][auth()->user()->id]);
                $this->redis->hset($key, $params['service_id'], json_encode($res, JSON_UNESCAPED_UNICODE));

                return Help::returnApiJson(
                    '下位成功!', 1, $res
                );
            } else {
                return Help::returnApiJson(
                    '下位失败-你未曾在此客服台上位!', 0, $res
                );
            }

        }

    }

    public function changeService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->redis->command("select", [$this->serviceWork]);
        $key = $this->getServiceWorkKey($this->partner_sign);
        $res = $this->redis->hget($key, $params['service_id']);

        if (!$res) {
            //初始化
            $struct = ServiceRedis::reServiceWorkStatusHash();
            $struct["partner_sign"] = $this->partner_sign;
            $struct["service_id"] = $params['service_id'];
            $struct["staff_status"] = 1;
            $struct["parent_info"] = [];
            $struct["work_status"] = $params['type'];
            $this->redis->hset($key, $params['service_id'], json_encode($struct, JSON_UNESCAPED_UNICODE));

            return Help::returnApiJson(
                '设定成功!', 0, $struct
            );

        }else {
            $res = json_decode($res);
            if ($res->work_status == $params['type']) {
                $info = $params['type']==1?"正常":"休息";
                return Help::returnApiJson(
                    '设定失败-当前客服通道状态已是['.$info."]状态,请勿重复设定", 0, $res
                );

            } else {
                $res->work_status=$params['type'];
                $this->redis->hset($key, $params['service_id'], json_encode($res, JSON_UNESCAPED_UNICODE));

                return Help::returnApiJson(
                    '设定成功!', 0, $res
                );
            }

        }
    }

    private function serviceInit($partner_sign, $partner_id)
    {
        $res = CustomerService::where('partner_sign', $partner_sign)->get();
        if ($res->isEmpty()) {
            $res = new CustomerService();
            $res->partner_sign = $partner_sign;
            $res->create_partner = $partner_id;
            $res->title = "系统客服";
            $res->desc = "默认系统客服";
            $res->sort = 0;
            $res->status = 1;
            $res->service_flag = 1;
            $res->save();
            $data[$res->id] = $res->attributes;
            $res = $data;
        } else {
            $res = $res->toArray();
            $res = array_column($res, NULL, 'id');
        }


        $this->redis->command("select", [$this->service]);
        $key = $this->getServiceKey($partner_sign);

        $res = json_encode($res);
        $this->redis->set($key, $res);
        return $res;
    }

    //获取聊天记录
    public function getTalkHistory($params)
    {
        $ids = json_decode($params['uid']);
        if ($ids == null || count($ids) < 1) {
            return Help::returnApiJson(
                'uid参数错误!', 0
            );
        }
        $res = array();
        $this->redis->command('select', [$this->userControl]);
        foreach ($ids as $v) {
            $key = $this->createHandKey(array('send_user_id' => $v));
            $start = $params['item'] * ($params['page'] - 1);
            $end = $start + $params['item'] - 1;
            $res[$v] = $this->redis->lrange($key, $start, $end);
        }
        return Help::returnApiJson(
            '查询成功!', 1,
            json_encode($res, JSON_UNESCAPED_UNICODE)
        );
    }

    //进入客服台
    public function enterService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->userId = $this->partner_sign . $this->userId;
        $revUserStatus = $this->checkUserOnline($this->userId);
        if (!$revUserStatus) {
            return Help::returnApiJson(
                '进入失败-请先绑定消息通道', 0
            );
        }
        $clientId = $revUserStatus[0];
        $groupId= $this->partner_sign.$params['service_id'];

        $res = Gateway::getClientSessionsByGroup($groupId);

        if(isset($res[$clientId])){
            return Help::returnApiJson(
                '您已经进入了'.$params['service_id'].'号客服台', 0
            );
        }

        Gateway::joinGroup($clientId,$groupId);

        return Help::returnApiJson(
            '进入客服台成功', 1
        );
    }

    /*获取离线消息
    * */
    public function getHistoryMsg()
    {
        $range = $this->redis->llen($this->userId);
        if (!$range) {
            return [];
        }
        $resData = $this->redis->lrange($this->userId, 0, $range);
        $this->redis->del($this->userId);//出栈
        return $resData;
    }

    public function sendService($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->writeData['msg'] = $params['msg'];
        $this->writeData['send_user_id'] = $this->partner_sign.$params['service_id'];
        $this->writeData['type'] = $this->typeMsg['one'];
        $this->writeData['other'] = isset($params['other']) ? $params['other'] : '';
        $this->writeData['resource'] = $this->userId;
        $this->writeData['client_ip'] = $this->clientIp();//出关IP
        $this->clientSendToService($params);
        return $this->sendGrp($params);
    }

    public function sendServiceUnLogin($params)
    {
        $this->userId='YKFW-'.$params['touch_id'];
        $this->partner_sign=$params['partner_sign'];
        return $this->sendService($params);
    }

    private function getCtoS($params){
         $key = $this->partner_sign.'-user-'.$params['user_id'].'-service-'.$params['service_id'];
         return $key;
    }

    public function clientSendToService($params)
    {
        $params['user_id'] = $this->userId;
        $params['serviceUser'] =  $this->getCtoS($params);
        $this->writeHistory($params,3);
        //咨询列表添加
        $this->askList($params);
        return true;
    }

    private function askList($params)
    {
        $this->redis->command('select',[$this->askList]);
        $key = $this->getAksKey($params);
        $this->redis->hset($key,$params['user_id'],json_encode($params,JSON_UNESCAPED_UNICODE));
        return true;
    }

    private function getAksKey($params){
        return 'ASK-'.$this->partner_sign.'-'.$params['service_id'];
    }

    public function sendGrp($params)
    {
        $this->writeHistory($this->writeData,2);
        $this->writeData['type'] = $this->typeMsg['clientToService'];
        Gateway::sendToGroup($this->writeData['send_user_id'],json_encode($this->writeData));
        return Help::returnApiJson(
            '发送成功!', 1, json_encode($this->writeData, JSON_UNESCAPED_UNICODE)
        );
    }

    private function clientIp(){
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

    //检测用户是否上位
    public function checkServiceUp($params)
    {

        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }
        $this->redis->command("select", [$this->serviceWork]);
        $key = $this->getServiceWorkKey($this->partner_sign);
        $res = $this->redis->hget($key, $params['service_id']);

        if (!$res) {
            return false;
        } else {
            $res = json_decode($res);
            $userId = $this->userId;
            if (isset($res->parent_info->$userId)) {
               return true;
            } else {
               return false;
            }
        }
    }

    public function serviceSendClient($params)
    {
        if (!$this->checkServiceId($params['service_id'])) {
            return Help::returnApiJson(
                '不存在的service_id!', 0
            );
        }

        if(!$this->checkServiceUp($params)){
            return Help::returnApiJson(
                '回复失败-您还没有上位服务台!', 0
            );
        }

        $params['serviceUser'] =  $this->getCtoS($params);
        $this->writeHistory($params,3);
        $params['send_user_id'] = $params['user_id'];
        unset($params['user_id']);
        $this->writeData['msg'] = $params['msg'];
        $this->writeData['send_user_id'] = $params['send_user_id'];
        $this->writeData['userStatus'] = $this->offLine;
        $this->writeData['type'] = $this->typeMsg['service'];
        $this->writeData['other'] = isset($params['other']) ? $params['other'] : '';
        $this->writeData['resource'] = $this->userId;

        $this->sendMsg($params['send_user_id'],$this->writeData);
        $this->writeData['to_user']=$params['send_user_id'];
        $this->writeData['send_user_id'] = $this->partner_sign.$params['service_id'];
        $this->sendGrp($params);
        $params['user_id']=$params['send_user_id'];
        unset($params['send_user_id']);
        $this->askList($params);
        return Help::returnApiJson(
            '发送成功!', 1, json_encode($this->writeData, JSON_UNESCAPED_UNICODE)
        );
    }

    public function endService($params)
    {
        $key = $this->getAksKey($params);
        $this->redis->command('select',[$this->askList]);
        $this->redis->hdel($key,$params['user_id']);
        return Help::returnApiJson(
            '结束成功!', 1, $params
        );
    }

    public function serviceList($params)
    {
        $key = $this->getAksKey($params);
        $this->redis->command('select',[$this->askList]);
        $res = $this->redis->hgetall($key);
        return Help::returnApiJson(
            '获取成功!', 1, $res
        );
    }

    public function serviceHistory($params)
    {
        $ids = json_decode($params['uid']);
        if ($ids == null || count($ids) < 1) {
            return Help::returnApiJson(
                'uid参数错误!', 0
            );
        }
        $res = array();
        $this->redis->command('select', [$this->serviceClient]);
        foreach ($ids as $v) {
            $params['user_id']=$v;
            $key= $this->getCtoS($params);
            $start = $params['item'] * ($params['page'] - 1);
            $end = $start + $params['item'] - 1;
            $res[$v] = $this->redis->lrange($key, $start, $end);
        }
        return Help::returnApiJson(
            '查询成功!', 1,
            json_encode($res, JSON_UNESCAPED_UNICODE)
        );

    }

}
