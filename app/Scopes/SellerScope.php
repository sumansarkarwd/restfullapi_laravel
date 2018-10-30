<?php
namespace App\Scopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Created by PhpStorm.
 * User: suman
 * Date: 10/28/2018
 * Time: 5:59 PM
 */
class SellerScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // TODO: Implement apply() method.
        $builder->has('products');
    }
}