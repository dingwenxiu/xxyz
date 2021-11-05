<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        // 通知到相关组
        $msg    = $exception->getMessage();
        $file   = $exception->getFile();
        $line   = $exception->getLine();

        $url = url()->full();
        $text = "地址:". $url . "\r\n";

        $text .= "文件:". $file . "\r\n";
        $text .= "行数:". $line . "\r\n";
        $text .= "信息:". $msg . "\r\n";

        $params = request()->all();
        if ($params) {
            $text .= "params:". json_encode($params) . "\r\n";

            $header = request()->headers;
            $text .= "header:". json_encode($header) . "\r\n";
        }

        $text .= "时间:". date("Y-m-d H:i:s") . "\r\n";

        $e = $this->prepareException($exception);

        if ($e instanceof HttpResponseException) {
            $text .= "Code:". $e->getResponse()->getStatusCode() . "\r\n";
        } elseif ($e instanceof AuthenticationException) {
            $text .= "Code:401\r\n";
        } elseif ($e instanceof ValidationException) {
            $text .= "Code:validation Error \r\n";
        } else if ($e instanceof  JWTException) {
            $text .= "Code:JWTException Error \r\n";
        } else if ($e instanceof TokenInvalidException) {
            return true;
        }

        info($text);

        telegramSend("send_exception", $text);
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
