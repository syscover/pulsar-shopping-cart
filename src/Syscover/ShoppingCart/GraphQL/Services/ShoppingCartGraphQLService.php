<?php namespace Syscover\ShoppingCart\GraphQL\Services;

use Illuminate\Support\Facades\Log;
use Syscover\ShoppingCart\Facades\CartProvider;

class ShoppingCartGraphQLService
{
    public function cart($root, array $args)
    {
        return CartProvider::instance($args['instance'] ?? null);
    }

    public function items($root, array $args)
    {
        Log::info(CartProvider::instance($args['instance'] ?? null)->getCartItems());
        return CartProvider::instance($args['instance'] ?? null)->getCartItems();
    }
}