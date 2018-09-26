<?php namespace Syscover\ShoppingCart\GraphQL\Services;

use Syscover\ShoppingCart\Facades\CartProvider;

class ShoppingCartGraphQLService
{
    public function cart($root, array $args)
    {
        return CartProvider::instance($args['guard'] ?? null)->toArray();
    }

    public function items($root, array $args)
    {
        return CartProvider::instance($args['guard'] ?? null)->getCartItems()->toArray();
    }
}