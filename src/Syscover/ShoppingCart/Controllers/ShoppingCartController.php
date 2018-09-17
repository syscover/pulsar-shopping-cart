<?php namespace Syscover\ShoppingCart\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Syscover\ShoppingCart\Facades\CartProvider;

/**
 * Class ProductController
 * @package Syscover\Market\Controllers
 */

class ShoppingCartController extends BaseController
{
    public function index()
    {
        return response()->json([
            'status'        => 200,
            'status_text'   => 'OK',
            'data'          => CartProvider::instance(request('instance') ?? null)
        ]);
    }

    public function items()
    {
        return response()->json([
            'status'        => 200,
            'status_text'   => 'OK',
            'data'          => CartProvider::instance(request('instance') ?? null)->getCartItems()
        ]);
    }
}