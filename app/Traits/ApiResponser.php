<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

Trait ApiResponser
{
    private function successResponse($date, $code) {
        return response()->json($date, $code);
    }
    protected function errorResponse($message, $code) {
        return response()->json([ 'error' => $message, 'code' => $code], $code);
    }
    protected function showAll(Collection $data, $code = 200) {
        return response()->json([ 'data' => $data], $code);
    }
    protected function showOne(Model $data, $code = 200) {
        return response()->json([ 'data' => $data], $code);
    }
}