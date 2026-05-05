<?php

namespace App\Helpers;
use App\Models\Setting;

class WebsiteHelper
{
    public static function WebsiteApiResponse(
        bool $status = true,
        string $message = '',
        array $data = [],
        int $statusCode = 200,
        array $extra = []
    ) {
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }

        if (!empty($extra)) {
            $response['extra'] = $extra;
        }
        return response()->json(
            $response,
            $statusCode
        );
    }
    public static function WebsiteInternalErrorResponse($e)
    {
        if (in_array(env('APP_ENV'), ['local'])) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",

            ], 500);
        }
    }
    public static function getTax()
    {
        return Setting::where('id', 1)->value('tax');
    }
    public static function getShippingFree()
    {
        return Setting::where('id', 1)->value('free_shipping');
    }

    public static function formatPrice($price)
    {
        return env('CURRENCY_SYMBOL', '$') . number_format($price, 2);
    }

}
