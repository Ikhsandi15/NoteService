<?php

namespace App\Helpers;

class Helper
{
    public static function APIResponse($messages, $resCode, $error, $data)
    {
        return response()->json([
            'code' => $resCode,
            'msg' => $messages,
            'error' => $error,
            'data' => $data
        ], $resCode);
    }
}
