<?php namespace Syscover\ShoppingCart\Events;

use Syscover\Market\Models\Product;

class ShoppingCartAddProduct
{
    public $payload;
    public $product;

    public function __construct(array $payload, Product $product)
    {
        $this->payload = $payload;
        $this->product = $product;
    }
}