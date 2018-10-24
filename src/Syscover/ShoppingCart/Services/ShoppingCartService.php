<?php namespace Syscover\ShoppingCart\Services;

use Syscover\Market\Models\Product;
use Syscover\Market\Services\TaxRuleService;
use Syscover\ShoppingCart\Facades\CartProvider;
use Syscover\ShoppingCart\Events\ShoppingCartAddProduct;
use Syscover\ShoppingCart\Item;

class ShoppingCartService
{
    public static function add(int $id, string $lang_id, float $quantity, string $instance = null)
    {
        $dirtyProduct = Product::builder()
            ->where('market_product.id', $id)
            ->where('market_product_lang.lang_id', $lang_id)
            ->first()
            ->load('attachments');

        if($dirtyProduct === null) return null;

        // when get price from product, internally calculate subtotal and total.
        // we don't want save this object on shopping cart, if login user with different prices and add same product,
        // I would register it as a different product because it has a different price
        // will be different because the product will have different prices
        // product will be a shot of original product for to be serialized
        $product = clone $dirtyProduct;

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
        $isTransportable = $dirtyProduct->type_id == 2 || $dirtyProduct->type_id == 3;

        // get shopping cart tax rule array (Syscover\ShoppingCart\TaxRule[])
        $taxRules = TaxRuleService::getShoppingCartTaxRules($dirtyProduct->product_class_tax_id);

        $eventResponses = event(new ShoppingCartAddProduct($id, $lang_id, $quantity, $dirtyProduct, $product, $isTransportable, $taxRules, $instance));

        // check if we have any Item from event
        $item = null;
        foreach ($eventResponses as $response)
        {
            if(get_class($response) === Item::class)
            {
                $item = $response;
            }
        }

        if(! $item) {
            $item = new Item(
                $dirtyProduct->id,
                $dirtyProduct->name,
                $quantity,
                $dirtyProduct->price,
                $dirtyProduct->weight,
                $isTransportable,
                $taxRules,
                [
                    'product' => $product
                ]
            );
        }

        // instance row to add product
        CartProvider::instance($instance)->add($item);

        // return all shopping cart
        return CartProvider::instance($instance)->toArray();
    }

    public static function update(string $id, float $quantity, string $instance = null)
    {
        // set quantity
        CartProvider::instance($instance)->setQuantity($id, $quantity);

        // return all shopping cart
        return CartProvider::instance($instance)->toArray();
    }

    public static function delete(string $id, string $instance = null)
    {
        CartProvider::instance($instance)->remove($id);

        // return all shopping cart
        return CartProvider::instance($instance)->toArray();
    }
}