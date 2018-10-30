<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
/**
 * Created by PhpStorm.
 * User: suman
 * Date: 10/28/2018
 * Time: 5:53 PM
 */
class BuyerScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->has('transactions');
    }
}