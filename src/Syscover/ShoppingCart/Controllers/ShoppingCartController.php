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
        $response['status']     = 200;
        $response['statusText'] = "OK";
        $response['data']       = CartProvider::instance(request('instance') ?? null);

        return response()->json($response);
    }

    public function items()
    {
        $response['status']     = 200;
        $response['statusText'] = "OK";
        $response['data']       = CartProvider::instance(request('instance') ?? null)->getCartItems();

        return response()->json($response);
    }
}