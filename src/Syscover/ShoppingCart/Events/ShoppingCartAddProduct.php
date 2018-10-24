<?php namespace Syscover\ShoppingCart\Events;

use Syscover\Market\Models\Product;

class ShoppingCartAddProduct
{
    public $id;
    public $lang_id;
    public $quantity;
    public $dirtyProduct;
    public $product;
    public $isTransportable;
    public $taxRules;
    public $instance;

    public function __construct(
        int $id,
        string $lang_id,
        float $quantity,
        Product $dirtyProduct,
        Product $product,
        bool $isTransportable,
        array $taxRules,
        string $instance = null
    )
    {
        $this->id = $id;
        $this->lang_id = $lang_id;
        $this->quantity = $quantity;
        $this->dirtyProduct = $dirtyProduct;
        $this->product = $product;
        $this->isTransportable = $isTransportable;
        $this->taxRules = $taxRules;
        $this->instance = $instance;
    }
}