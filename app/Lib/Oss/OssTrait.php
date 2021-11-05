<?php namespace App\Lib\Oss;

use OSS\OssClient;
use OSS\Core\OssException;

trait OssTrait
{
    static function createBucket($bucket)
    {
        $accessKeyId     = configure("web_oss_key","LTAI4Fn8bncU2WC5E37f4g1G");
        $accessKeySecret = configure("web_oss_sercet","JGJUnF1k1roj9lSSLp0zn6EX554HuJ");
        $endpoint        = configure("web_oss_endpoint","http://oss-cn-hongkong.aliyuncs.com");

        $flag = true;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->createBucket($bucket);
        } catch (OssException $e) {
            $flag=$e->getMessage();
        }
        return $flag;
    }

    
    static function fileUpdate($bucket ,$filename ,$filepath)
    {
        $accessKeyId     = configure("web_oss_key","LTAI4Fn8bncU2WC5E37f4g1G");
        $accessKeySecret = configure("web_oss_sercet","JGJUnF1k1roj9lSSLp0zn6EX554HuJ");
        $endpoint        = configure("web_oss_endpoint","http://oss-cn-hongkong.aliyuncs.com");

        $flag =true;
        try{
           $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
           $ossClient->uploadFile($bucket, $filename, $filepath);
        } catch(OssException $e) {
            $flag=$e->getMessage();
        }
        
        return $flag;
    }

    static function fileDelete($bucket ,$filename)
    {
        $accessKeyId     = configure("web_oss_key","LTAI4Fn8bncU2WC5E37f4g1G");
        $accessKeySecret = configure("web_oss_sercet","JGJUnF1k1roj9lSSLp0zn6EX554HuJ");
        $endpoint        = configure("web_oss_endpoint","http://oss-cn-hongkong.aliyuncs.com");

        $flag =true;
        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $ossClient->deleteObject($bucket, $filename);
        } catch(OssException $e) {
            $flag=$e->getMessage();
        }

        return $flag;
    }

    static function fileExists($bucket ,$filename)
    {
        $accessKeyId     = configure("web_oss_key","LTAI4Fn8bncU2WC5E37f4g1G");
        $accessKeySecret = configure("web_oss_sercet","JGJUnF1k1roj9lSSLp0zn6EX554HuJ");
        $endpoint        = configure("web_oss_endpoint","http://oss-cn-hongkong.aliyuncs.com");

        $flag =false;
        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $flag = $ossClient->doesObjectExist($bucket, $filename);
        } catch(OssException $e) {
            $flag=$e->getMessage();
        }

        return $flag;
    }
}
