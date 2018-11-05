<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

Trait ApiResponser
{
    private function successResponse($date, $code) {
        return response()->json($date, $code);
    }
    protected function errorResponse($message, $code) {
        return response()->json([ 'error' => $message, 'code' => $code], $code);
    }
    protected function showAll(Collection $data, $code = 200) {
        if($data->isEmpty()) {
            return $this->successResponse(['data' => $data], $code);
        }
        $transformer = $data->first()->transformer;
        $data = $this->filterData($data, $transformer);
        $data = $this->sortData($data, $transformer);
        $data = $this->paginate($data, $transformer);
        $data = $this->transformData($data, $transformer);
        return response()->json([ 'data' => $data], $code);
    }
    protected function showOne(Model $data, $code = 200) {
        $transformer = $data->transformer;
        $data = $this->transformData($data, $transformer);
        return response()->json([ 'data' => $data], $code);
    }
    protected function showMessage($message, $code=200) {
        return $this->successResponse(['data' => $message], $code);
    }
    protected function sortData($data, $transformer) {
        if(request()->has('sort_by')) {
            $attr = $transformer::originalAttribute(request()->sort_by);
            $data = $data->sortBy->{$attr};
        }
        return $data;
    }
    protected function filterData(Collection $data, $transformer) {
        foreach (request()->query() as $query => $value) {
            $attr = $transformer::originalAttribute($query);
            if(isset($attr, $value)) {
                $data = $data->where($attr, $value);
            }
        }
        return $data;
    }
    protected function paginate(Collection $data) {
        $rules = ['per_page' => 'integer|min:2|max:50'];
        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;

        if(request()->has('per_page')) {
            $perPage = (int) request()->per_page;
        }

        $result = $data->slice(($page - 1)*$perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($result, $data->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPage()
        ]);
        $paginated->appends(request()->all());
        return $paginated;
    }
    protected function transformData($data, $transformer) {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }
}