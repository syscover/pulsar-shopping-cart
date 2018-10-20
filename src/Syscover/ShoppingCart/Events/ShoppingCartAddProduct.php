<?php namespace Syscover\ShoppingCart\Events;

use Syscover\Market\Models\Product;

class ShoppingCartAddProduct
{
    public $id;
    public $lang_id;
    public $quantity;
    public $product;
    public $instance;

    public function __construct(int $id, string $lang_id, float $quantity, string $instance, Product $product)
    {
        $this->id = $id;
        $this->lang_id = $lang_id;
        $this->quantity = $quantity;
        $this->instance = $instance;
        $this->product = $product;
    }
}