<?php namespace Syscover\ShoppingCart\Events;

use Syscover\Market\Models\Product;

class ShoppingCartAddProduct
{
    public $id;
    public $lang_id;
    public $quantity;
    public $instance;
    public $product;

    public function __construct(int $id, string $lang_id, float $quantity, Product $product, string $instance = null)
    {
        $this->id = $id;
        $this->lang_id = $lang_id;
        $this->quantity = $quantity;
        $this->product = $product;
        $this->instance = $instance;
    }
}