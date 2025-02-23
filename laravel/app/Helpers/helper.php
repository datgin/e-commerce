<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('transaction')) {
    function transaction($callback)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            Log::error('Exception Details:', [
                'error' => $e->getMessage(),  // Tên lỗi
                'file' => $e->getFile(),      // File xảy ra lỗi
                'line' => $e->getLine(),      // Dòng xảy ra lỗi
                'function' => getErrorFunction($e), // Function xảy ra lỗi
                'trace' => $e->getTraceAsString(), // Stack trace (tùy chọn)
            ]);
            DB::rollBack();
            return errorResponse('Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    }
}

if (!function_exists('logInfo')) {
    function logInfo($data)
    {
        Log::info($data);
    }
}

if (!function_exists('getErrorFunction')) {
    function getErrorFunction(Throwable $exception): ?string
    {
        // Kiểm tra nếu có trace và function gọi lỗi
        $trace = $exception->getTrace();
        return isset($trace[0]['function']) ? $trace[0]['function'] : null;
    }
}

if (!function_exists('successResponse')) {
    function successResponse($message, $data = null, $code = 200)
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }
}

if (!function_exists('handleResponse')) {
    function handleResponse($message, $code = 200)
    {
        return response()->json(['success' => true, 'message' => $message], $code);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message,  $code = 500)
    {

        $response = [
            'success' => false,
            'message' => $message
        ];

        return   response()->json($response, $code);
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug(string $text)
    {
        return Str::slug($text);
    }
}

function showImage($path)
{
    /** @var FilesystemAdapter $storage */
    $storage = Storage::disk('public');

    if ($path && Storage::exists($path)) {
        return $storage->url($path);
    }

    return null;
}
