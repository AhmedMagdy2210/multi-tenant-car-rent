<?php

use App\Models\User;

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
        return $response;
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
        return $response;
    }
}

