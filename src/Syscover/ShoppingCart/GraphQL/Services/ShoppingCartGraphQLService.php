<?php namespace Syscover\ShoppingCart\GraphQL\Services;

use Syscover\ShoppingCart\Facades\CartProvider;

class ShoppingCartGraphQLService
{
    public function cart($root, array $args)
    {
        return CartProvider::instance($args['instance'] ?? null);
    }

    public function items($root, array $args)
    {
        return CartProvider::instance($args['instance'] ?? null)->getCartItems();
    }
}