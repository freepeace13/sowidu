<?php

if (!function_exists('appHelper')) {
    function appHelper()
    {
        return app('app.helper');
    }
}

if (!function_exists('jsonify')) {
    function jsonify($success, $data = [])
    {
        return appHelper()->jsonify($success, $data);
    }
}

if (!function_exists('successify')) {
    function successify($data = [])
    {
        return appHelper()->successify($data);
    }
}

if (!function_exists('errorify')) {
    function errorify($data = [])
    {
        return appHelper()->errorify($data);
    }
}
