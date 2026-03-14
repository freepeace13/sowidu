<?php

namespace App\Helpers\Traits;

use Illuminate\Http\JsonResponse;

/**
 * JSON response helper
 *
 * @author goper
 */
trait ResponseTrait
{
    /**
     * Return json response
     *
     * @param  bool  $success  if response is a success or not
     * @param  array  $data  extra data to include
     * @param  int  $status  response status code
     * @return Illuminate\Http\JsonResponse
     *
     * @author goper
     */
    public function jsonify($data = [], $status = 200)
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Return a successful json response
     *
     * @param  string  $message  message of the response
     * @param  array  $data  extra data to include
     * @return Illuminate\Http\JsonResponse
     *
     * @author goper
     */
    public function successify($data = [])
    {
        return $this->jsonify($data);
    }

    /**
     * Return an unsuccessful json response
     *
     * @param  array  $data  extra data to include
     * @return Illuminate\Http\JsonResponse
     *
     * @author goper
     */
    public function errorify($data = [])
    {
        return $this->jsonify(compact('data'), 422);
    }
}
