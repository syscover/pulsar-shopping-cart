<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopping Cart
    |--------------------------------------------------------------------------
    |
    | Config file
    | If you have installed market package, product_prices_values and shipping_prices_values translations are defined on market config file
    |
    */

    // 1 excluding tax, 2 including tax
    'product_tax_prices'              => env('SHOPPING_CART_PRODUCT_TAX_PRICES', 1),
    'taxShippingPrices'             => env('SHOPPING_CART_TAX_SHIPPING_PRICES', 1),

    // Display prices
    'productTaxDisplayPrices'       => env('SHOPPING_CART_PRODUCT_TAX_DISPLAY_PRICES', 1),
    'taxShippingDisplayPrices'      => env('SHOPPING_CART_TAX_SHIPPING_DISPLAY_PRICES', 1),
];