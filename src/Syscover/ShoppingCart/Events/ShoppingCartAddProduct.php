<?php namespace Syscover\ShoppingCart\Events;

use Syscover\Market\Models\Product;

class ShoppingCartAddProduct
{
    public $id;
    public $lang_id;
    public $quantity;
    public $product;
    public $cloneProduct;
    public $isTransportable;
    public $taxRules;
    public $instance;

    public function __construct(
        int $id,
        string $lang_id,
        float $quantity,
        Product $product,
        Product $cloneProduct,
        bool $isTransportable,
        array $taxRules,
        string $instance = null
    )
    {
        $this->id = $id;
        $this->lang_id = $lang_id;
        $this->quantity = $quantity;
        $this->product = $product;
        $this->cloneProduct = $cloneProduct;
        $this->isTransportable = $isTransportable;
        $this->taxRules = $taxRules;
        $this->instance = $instance;
    }
}