<?php

if (!function_exists('shared')) {
    /**
     * @return \Sowidu\SharedData\SharedData
     */
    function shared()
    {
        return app(\Sowidu\SharedData\SharedData::class);
    }
}

if (!function_exists('share')) {
    /**
     * @param  array  $args
     * @return \Sowidu\SharedData\SharedData
     */
    function share(...$args)
    {
        return app(\Sowidu\SharedData\SharedData::class)->put(...$args);
    }
}
