<?php

namespace App\Lib\Common;

class ImageArrange
{
    /**
     * 图片上传
     * @param  object $file
     * @param  string $path
     * @return array
     */
    public function uploadImg($file, $path)
    {
        // 检验一下上传的文件是否有效.
        if ($file->isValid()) {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
                chmod($path, 0777);
            }
//            $folder = 'uploaded_files';
//            if (!is_writable($folder)) {
//                return ['success' => false, 'msg' => '文件夹' . $folder . '没有写入权限'];
//            } else {
//                if (!is_writable($path)) {
//                    mkdir($path, 0777, true);
//                    chmod($path, 0777);
//                }
//            }
            // 缓存在tmp文件夹中的文件名 例如 php8933.tmp 这种类型的.
            $clientName = $file->getClientOriginalName();
            // 上传文件的后缀.
            $entension = $file->getClientOriginalExtension();
            $newName = md5(date('Y-m-d H:i:s') . $clientName) . '.' . $entension;
            $file->move($path, $newName);
            //文件名
            $namePath = $path . '/' . $newName;
            return ['success' => true, 'name' => $newName, 'path' => $namePath];
        } else {
            return ['success' => false];
        }
    }

    /**
     * 制作缩略图
     * @param string $srcPath  原图路径
     * @param int $maxWidth 画布的宽度
     * @param int $maxHight 画布的高度
     * @param bool $flag 是否是等比缩略图  默认为true
     * @param string $prefix 缩略图的前缀  默认为'sm_'
     * @return string
     */
    public function creatThumbnail($srcPath, $maxWidth, $maxHight, $prefix = 'sm_', $flag = true)
    {
        $srcPathArr = explode('.', $srcPath);//获取文件的后缀
        $picType = end($srcPathArr);
        if ($picType === 'jpg') {
            $picType = 'jpeg';
        }
        $open_fn = 'imagecreatefrom' . $picType;//拼接打开图片的函数
        if (!is_callable($open_fn)) {//打开源图
            return '';
        }
        $source = $open_fn($srcPath);
        $src_w = imagesx($source);//源图的宽
        $src_h = imagesy($source);//源图的高
        if ($flag) {//是否等比缩放
            //等比
            //求目标图片的宽高
            if ($maxWidth / $maxHight < $src_w / $src_h) {
                //横屏图片以宽为标准
                $dst_w = $maxWidth;
                $dst_h = $maxWidth * $src_h / $src_w;
            } else {
                //竖屏图片以高为标准
                $dst_h = $maxHight;
                $dst_w = $maxHight * $src_w / $src_h;
            }
            //在目标图上显示的位置
            // $dst_x = (int) (($maxWidth - $dst_w) / 2);
            // $dst_y = (int) (($maxHight - $dst_h) / 2);
        } else {
            //不等比
            // $dst_x = 0;
            // $dst_y = 0;
            $dst_w = $maxWidth;
            $dst_h = $maxHight;
        }
        $dst_x = 0;
        $dst_y = 0;
        $targetPic = imagecreatetruecolor((int) $dst_w, (int) $dst_h);//创建目标图
        if ($targetPic === false) {
            return '';
        }
        imagecopyresampled($targetPic, $source, $dst_x, $dst_y, 0, 0, (int) $dst_w, (int) $dst_h, $src_w, $src_h);//生成缩略图
        $filename = basename($srcPath);//文件名
        $foldername = substr(dirname($srcPath), 0);//文件夹名
        $thumb_path = $foldername . '/' . $prefix . $filename;//缩略图存放路径
        imagepng($targetPic, $thumb_path);//把缩略图上传到指定的文件夹
        imagedestroy($targetPic);//销毁图片资源
        imagedestroy($source);//销毁图片资源
        return $thumb_path;//返回新的缩略图的文件名
    }

    /**
     * 删除文件
     * @param $path
     * @return bool
     */
    public static function deletePic($path)
    {
        if (file_exists($path)) {
            if (!is_writable(dirname($path))) {
                return false;
            } else {
                unlink($path);
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 生成存放图片的路径
     * @param $name
     * @param $partnerSign
     * @return string
     */
    public function depositPath($name, $partnerSign = "system")
    {
        return 'app/public/upload/' . $partnerSign . "/" . $name;
    }

    /**
     * 生成存放图片的路径
     * @param $name
     * @return string
     */
    public function depositPathReal($name, $partnerSign)
    {
        return '';
    }

    /**
     * 生成头像图片的路径
     * @param $name
     * @return string
     */
    public function depositPathAvatars($name)
    {
        return 'uploaded_files/' . $name;
    }

    /**
     * 删除图片
     * @param $pathArr
     */
    public function deleteImgs($pathArr)
    {
        foreach ($pathArr as $path) {
            $this->deletePic($path);
        }
    }

    /**
     * 生成存放图片的路径(放入所在平台文件夹里)
     * @param $name
     * @param $partnerSign
     * @return string
     */
    public function depositSignPath($name, $partnerSign = "system")
    {
        return storage_path().'/app/public/upload/'. strtolower($partnerSign) . '/' . $name;
    }

    /**
     * 图片上传
     * @param  object $file
     * @param  string $path
     * @param  string $folderName
     * @return array
     */
    public function uploadImage($file, $path, $folderName = '')
    {
        // 检验一下上传的文件是否有效.
        if ($file->isValid()) {
            $folder = 'uploaded_files';
            if (!is_writable($folder)) {
                return ['success' => false, 'msg' => '文件夹' . $folder . '没有写入权限'];
            } else {
                if (!is_writable($path)) {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }
            }
            // 缓存在tmp文件夹中的文件名 例如 php8933.tmp 这种类型的.
            $clientName = $file->getClientOriginalName();
            // 上传文件的后缀.
            $entension = $file->getClientOriginalExtension();
            $newName = md5(date('Y-m-d H:i:s') . $clientName) . '.' . $entension;
            $file->move($path, $newName);
            //文件名
            $namePath = !$folderName ? $path . '/' . $newName : $folderName . '/' . $newName;
            return ['success' => true, 'name' => $newName, 'path' => $namePath];
        } else {
            return ['success' => false];
        }
    }

    public function typeUploadImage($file, $path, $folderName = '')
    {
        // 检验一下上传的文件是否有效.
        if ($file->isValid()) {
            $folder = 'finance';
            if (!is_writable($folder)) {
                if (!is_writable($path)) {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }
            } else {
                return ['success' => false, 'msg' => '文件夹' . $folder . '没有写入权限'];
            }
            // 缓存在tmp文件夹中的文件名 例如 php8933.tmp 这种类型的.
            $clientName = $file->getClientOriginalName();
            // 上传文件的后缀.
            $entension = $file->getClientOriginalExtension();
            $newName = md5(date('Y-m-d H:i:s') . $clientName) . '.' . $entension;
            $file->move($path, $newName);
            //文件名
            $namePath = $path . '/' .$folder . '/' . $newName;
            return ['success' => true, 'name' => $newName, 'path' => $namePath];
        } else {
            return ['success' => false];
        }
    }
}
