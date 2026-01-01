<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('success')) {
    function success($message = 'Success response', $code = 200, $data = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }
}
if (!function_exists('error')) {
    function error($message = 'Error response', $code = 500, $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $code);

    }
}

if (!function_exists('generateOtp')) {
    function generateOtp()
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        return $otp;
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($value, Model $model, $column = 'slug')
    {
        $slug = Str::slug($value);
        $slugBase = $slug;
        $count = 1;
        while ($model->newQuery()->where($column, $slug)->exists()) {
            $slug = "{$slugBase}-{$count}";
            $count++;
        }
        return $slug;
    }
}
