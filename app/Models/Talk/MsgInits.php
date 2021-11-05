<?php

namespace App\Models\Talk;

use Illuminate\Database\Eloquent\Model;

class MsgInits extends Model
{
    //用户绑定
    public static function bind($params)
    {
         $msgRds = new MsgRedis();
         unset($params['token']);
         return $msgRds->bindU($params);
    }

    public static function bindUnLogin($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->bindUPublic($params);
    }

    public static function getTalkHistoryUnLogin($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->getTalkHistoryUnLogin($params);
    }

    //查询句柄是否在线
    public static function isCidOnLine($params)
    {
        $msgRds = new MsgRedis();
        unset($params['token']);
        return $msgRds->isCidOnLin($params);
    }

    public static function searchService()
    {
        $msgRds = new MsgRedis();
        return $msgRds->searchService();
    }

    public static function searchServicePublic($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->searchServicePublic($params);
    }

    public static function openService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->openService($params);
    }

    public static function closeService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->closeService($params);
    }

    public static function EditService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->EditService($params);
    }

    public static function deleteServiceHistory($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->deleteServiceHistory($params);
    }

    public static function upService($params){

        $msgRds = new MsgRedis();
        return $msgRds->upService($params);
    }

    public static function downService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->downService($params);
    }

    public static function changeService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->changeService($params);
    }

    public static function enterService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->enterService($params);
    }

    //获取聊天记录
    public static function getTalkHistory($params)
    {
        $msgRds = new MsgRedis();
        unset($params['token']);
        return $msgRds->getTalkHistory($params);
    }
    //个人发送消息
    public static function  SendMsg($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->sendMsgUser($params);
    }
    //初始化用户缓存
    public static function UserInit()
    {
        $msgRds = new MsgRedis();
        return $msgRds->getHistoryMsg();
    }
    //获取用户关系
    public static function Friend(){
        $msgRds = new MsgRedis();
        return $msgRds->getFriend();
    }
    //刷新用户关系缓存
    public static function SetFriendCache($params){
        $msgRds = new MsgRedis();
        $msgRds->setFriend($params['id']);
        if($params['id'] !== $params['parent_id'] && $params['parent_id']!=null && $params['parent_id']!=''){
            $msgRds->setFriend($params['parent_id']);
        }
        return true;
    }
    //删除用户相关缓存
    public static function ClearCache($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->clearCache($params);
    }
    //删除聊天记录
    public static function TalkDelete($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->talkDelete($params);
    }
    //删除系统所有用户的聊天记录
    public static function ClearTalkHistoryAll(){
        $msgRds = new MsgRedis();
        return $msgRds->clearTalkHistoryAll();
    }
    //绑定客服通道
    public static function ServiceBind($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->serviceBind($params);
    }

    public static function sendService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->sendService($params);
    }

    public static function sendServiceUnLogin($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->sendServiceUnLogin($params);
    }

    public static function serviceSendClient($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->serviceSendClient($params);
    }

    public static function endService($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->endService($params);
    }

    public static function serviceList($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->serviceList($params);
    }

    public static function serviceHistory($params)
    {
        $msgRds = new MsgRedis();
        return $msgRds->serviceHistory($params);
    }

}
