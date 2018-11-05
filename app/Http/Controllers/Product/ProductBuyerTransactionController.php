<?php

namespace App\Http\Controllers\Product;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];
        $this->validate($request, $rules);

        if($buyer->id == $product->seller_id) {
            return $this->errorResponse('Buyer must be different from seller', 409);
        }
        if(!$buyer->isVerified()) {
            return $this->errorResponse('A buyer must be verified user', 409);
        }
        if(!$product->seller->isVerified()) {
            return $this->errorResponse('The seller of the product must be verified user', 409);
        }
        if(!$product->isAvailable()) {
            return $this->errorResponse('The product is not available', 409);
        }
        if($product->quantity < $request->quantity) {
            return $this->errorResponse('The product does not have enough quantity', 409);
        }

        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id
            ]);

            return $this->showOne($transaction);
        });
    }
}
