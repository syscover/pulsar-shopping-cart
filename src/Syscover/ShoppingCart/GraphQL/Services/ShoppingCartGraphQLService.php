<?php namespace Syscover\ShoppingCart\GraphQL\Services;

use Syscover\ShoppingCart\Facades\CartProvider;
use Syscover\ShoppingCart\Services\ShoppingCartService;

class ShoppingCartGraphQLService
{
    public function cart($root, array $args)
    {
        return CartProvider::instance($args['instance'] ?? null)->toArray();
    }

    public function items($root, array $args)
    {
        return CartProvider::instance($args['instance'] ?? null)->getCartItems()->toArray();
    }

    public static function add($root, array $args)
    {
        return ShoppingCartService::add($args['id'], $args['lang_id'] ?? base_lang(), $args['quantity'] ?? 1, $args['instance'] ?? null);
    }

    public static function update($root, array $args)
    {
        return ShoppingCartService::update($args['id'], $args['quantity'], $args['instance'] ?? null);
    }
}