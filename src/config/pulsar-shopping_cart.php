<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopping Cart
    |--------------------------------------------------------------------------
    |
    | Config file
    |
    */

    // 1 - excluding tax,
    // 2 - including tax

    // input prices
    'product_tax_prices'            => env('SHOPPING_CART_PRODUCT_TAX_PRICES', 1),
    'tax_shipping_prices'           => env('SHOPPING_CART_TAX_SHIPPING_PRICES', 1),

    // display prices
    'product_tax_display_prices'    => env('SHOPPING_CART_PRODUCT_TAX_DISPLAY_PRICES', 1),
    'tax_shipping_display_prices'   => env('SHOPPING_CART_TAX_SHIPPING_DISPLAY_PRICES', 1),
];