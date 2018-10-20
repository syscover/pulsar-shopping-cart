<?php namespace Syscover\ShoppingCart\Services;

use Syscover\Market\Models\Product;
use Syscover\Market\Services\TaxRuleService;
use Syscover\ShoppingCart\Facades\CartProvider;
use Syscover\ShoppingCart\Events\ShoppingCartAddProduct;
use Syscover\ShoppingCart\Item;

class ShoppingCartService
{
    public static function add($id, $lang_id, $quantity, $instance = null)
    {
        $product = Product::builder()
            ->where('market_product.id', $id)
            ->where('market_product_lang.lang_id', $lang_id)
            ->first()
            ->load('attachments');

        if($product === null) return null;

        //**************************************************************************************
        // know if product is transportable
        // Options:
        // 1 - downloadable
        // 2 - transportable
        // 3 - transportable_downloadable
        // 4 - service
        //
        // You can change this value, if you have same product transportable and downloadable
        //***************************************
        $isTransportable = $product->type_id == 2 || $product->type_id == 3;

        // when get price from product, internally calculate subtotal and total.
        // we don't want save this object on shopping cart, if login user with different prices and add same product,
        // will be different because the product will have different prices
        $cloneProduct = clone $product;

        // get shopping cart tax rule array (Syscover\ShoppingCart\TaxRule[])
        $taxRules = TaxRuleService::getShoppingCartTaxRules($product->product_class_tax_id);

        $item = null;
        $eventResponses = event(new ShoppingCartAddProduct($id, $lang_id, $quantity, $product));

        // check if we have any Item from event
        foreach ($eventResponses as $response)
        {
            if(get_class($response) === Item::class)
            {
                $item = $response;
            }
        }

        if(! $item) {
            $item = new Item(
                $product->id,
                $product->name,
                $quantity,
                $product->price,
                $product->weight,
                $isTransportable,
                $taxRules,
                [
                    'product' => $cloneProduct
                ]
            );
        }

        // instance row to add product
        $cartItem = CartProvider::instance($instance)->add(
            $item
        );

        return $cartItem;
    }
}