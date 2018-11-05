<?php namespace Tests\Feature;

use Tests\TestCase;
use Syscover\ShoppingCart\Facades\CartProvider;
use Syscover\ShoppingCart\Cart;
use Syscover\ShoppingCart\Item;
use Syscover\ShoppingCart\TaxRule;
use Syscover\ShoppingCart\PriceRule;

class ShoppingCartProviderTest extends TestCase
{
    public function testCartCanAdd()
    {
        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                1,
                9.99,
                1.000,
                true
            )
        );

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->count());
    }

    public function testCartCanAddWithOptions()
    {
        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 9.99, 1.000, true, [], ['size' => 'L']));
    }

    public function testCartCanAddMultipleCartItems()
    {
        $this->expectsEvents('cart.added');

        CartProvider::instance()->add([
            new Item('283ad', 'Product 2', 2, 10.00, 1.000),
            new Item('293ad', 'Product 1', 1, 9.99, 1.000),
            new Item('244ad', 'Product 3', 2, 20.50, 1.000)
        ]);

        $this->assertEquals(3, CartProvider::instance()->getCartItems()->count());
        $this->assertEquals(10.00, CartProvider::instance()->getCartItems()->first()->price);
    }

    public function testCartCanAddWithTaxRuleWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                1,
                9.99,
                1.000,
                true,
                new TaxRule('VAT', 21.00)
            )
        );

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(21, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['21'], CartProvider::instance()->getCartItems()->first()->getTaxRates());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(1.7338016528925621617673868968267925083637237548828125, $item->taxAmount);
        }
    }

    public function testCartCanAddWithTaxRuleWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                1,
                9.99,
                1.000,
                true,
                new TaxRule('VAT', 21.00)
            )
        );

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(21, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['21'], CartProvider::instance()->getCartItems()->first()->getTaxRates());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(2.09790000000000009805489753489382565021514892578125, $item->taxAmount);
        }
    }

    public function testCartCanAddVariousWithTaxRulesWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 2, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('150,26', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(150.262960180315559455266338773071765899658203125, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('200,00', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(200, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('31,56', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('18,18', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);
    }

    public function testCartCanAddVariousWithTaxRulesWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 2, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('200,00', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(200, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('266,20', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(266.19999999999998863131622783839702606201171875, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('42,00', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('24,20', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);

    }

    public function testCartCanAddWithTaxRulesWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('75,13', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(75.1314800901577797276331693865358829498291015625, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('24,87', CartProvider::instance()->getCartItems()->first()->getTaxAmount());
        $this->assertEquals('100,00', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(100, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('15,78', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('9,09', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);
    }

    public function testCartCanAddWithTaxRulesWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('100,00', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(100.00, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('33,10', CartProvider::instance()->getCartItems()->first()->getTaxAmount());
        $this->assertEquals('133,10', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(133.100, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('21,00', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('12,10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);

    }

    public function testCartCanAddWithSameTaxRulesWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 110.99, 1.000, true, [
            new TaxRule('VAT', 21.00),
            new TaxRule('VAT', 10.00)
        ]));

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(31, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['31'], CartProvider::instance()->getCartItems()->first()->getTaxRates());

        $this->assertEquals('84,73', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(84.7251908396946618040601606480777263641357421875, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('26,26', CartProvider::instance()->getCartItems()->first()->getTaxAmount());
        $this->assertEquals('110,99', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(110.99, CartProvider::instance()->getCartItems()->first()->total);
    }

    public function testCartCanAddWithSameTaxRulesWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 110.99, 1.000, true, [
            new TaxRule('VAT', 21.00),
            new TaxRule('VAT', 10.00)
        ]));

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(31, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['31'], CartProvider::instance()->getCartItems()->first()->getTaxRates());

        $this->assertEquals('110,99', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(110.99, CartProvider::instance()->getCartItems()->first()->subtotal);
        $this->assertEquals('34,41', CartProvider::instance()->getCartItems()->first()->getTaxAmount());
        $this->assertEquals('145,40', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(145.396899999999988040144671685993671417236328125, CartProvider::instance()->getCartItems()->first()->total);

    }

    public function testCartCanAddWithTaxRulesWithDifferentPrioritiesAndDiscountSubtotalPercentageWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        CartProvider::instance()->addCartPriceRule(
            new PriceRule(
                1,
                'Syscover\\Madrket\\Models\\CartPriceRule',
                'My first price rule',
                'For being a good customer',
                PriceRule::DISCOUNT_SUBTOTAL_PERCENTAGE,
                false,
                null,
                10.00
            )
        );

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('75,13', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(75.1314800901577797276331693865358829498291015625, CartProvider::instance()->getCartItems()->first()->subtotal);

        $this->assertEquals(10, CartProvider::instance()->getCartItems()->first()->discountsSubtotalPercentage->sum('percentage'));
        $this->assertEquals('10', CartProvider::instance()->getCartItems()->first()->getDiscountSubtotalPercentage());
        $this->assertEquals(7.51314800902, CartProvider::instance()->getCartItems()->first()->discountAmount);
        $this->assertEquals('7,51', CartProvider::instance()->getCartItems()->first()->getDiscountAmount());

        $this->assertEquals('22,38', CartProvider::instance()->getCartItems()->first()->getTaxAmount());

        $this->assertEquals('90,00', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(90, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('14,20', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('8,18', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);
    }

    public function testCartCanAddWithTaxRulesWithDifferentPrioritiesAndDiscountSubtotalPercentagesWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('293ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0),
            new TaxRule('OTHER IVA', 10.00, 1, 1)
        ]));

        CartProvider::instance()->addCartPriceRule(
            new PriceRule(
                1,
                'Syscover\\Madrket\\Models\\CartPriceRule',
                'My first price rule',
                'For being a good customer',
                PriceRule::DISCOUNT_SUBTOTAL_PERCENTAGE,
                false,
                null,
                10.00
            )
        );

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->taxRules->count());

        $this->assertEquals('100,00', CartProvider::instance()->getCartItems()->first()->getSubtotal());
        $this->assertEquals(100.00, CartProvider::instance()->getCartItems()->first()->subtotal);

        $this->assertEquals('29,79', CartProvider::instance()->getCartItems()->first()->getTaxAmount());
        $this->assertEquals('119,79', CartProvider::instance()->getCartItems()->first()->getTotal());
        $this->assertEquals(119.789999999999992041921359486877918243408203125, CartProvider::instance()->getCartItems()->first()->total);

        $this->assertEquals('18,90', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxAmount());
        $this->assertEquals('21', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->getTaxRate());
        $this->assertEquals('IVA', CartProvider::instance()->getTaxRules()->get(md5('IVA' . '0'))->name);

        $this->assertEquals('10,89', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxAmount());
        $this->assertEquals('10', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->getTaxRate());
        $this->assertEquals('OTHER IVA', CartProvider::instance()->getTaxRules()->get(md5('OTHER IVA' . '1'))->name);
    }

    public function testCartCanAddWithTaxRulesWithDiscountTotalPercentageWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->count());

        $this->assertEquals('171,64', CartProvider::instance()->getSubtotal());
        $this->assertEquals('36,05', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('207,69', CartProvider::instance()->getTotal());

        // apply 10% percentage discount over total
        CartProvider::instance()->addCartPriceRule(
            new PriceRule(
                1,
                'Syscover\\Madrket\\Models\\CartPriceRule',
                'discount 10% percentage',
                'For being a good customer',
                PriceRule::DISCOUNT_TOTAL_PERCENTAGE,
                false,
                null,
                10.00
            )
        );

        // check new amounts
        $this->assertEquals('10,00', CartProvider::instance()->getCartItems()->get('92f38118c1830f0893f9d3135bbcc705')->getDiscountAmount());
        $this->assertEquals('10,77', CartProvider::instance()->getCartItems()->get('4213a65a817336f9e62699ee2c1d16f6')->getDiscountAmount());
        $this->assertEquals('171,64', CartProvider::instance()->getSubtotal());
        $this->assertEquals('32,44', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('20,77', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('186,92', CartProvider::instance()->getTotal());

    }

    public function testCartCanAddWithTaxRulesWithDiscountTotalPercentageWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->count());

        $this->assertEquals('207,69', CartProvider::instance()->getSubtotal());
        $this->assertEquals('43,61', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('251,30', CartProvider::instance()->getTotal());

        // apply 10% percentage discount over total
        CartProvider::instance()->addCartPriceRule(
            new PriceRule(
                1,
                'Syscover\\Madrket\\Models\\CartPriceRule',
                'discount 10% percentage',
                'For being a good customer',
                PriceRule::DISCOUNT_TOTAL_PERCENTAGE,
                false,
                null,
                10.00
            )
        );

        // check new amounts
        $this->assertEquals('12,10', CartProvider::instance()->getCartItems()->get('92f38118c1830f0893f9d3135bbcc705')->getDiscountAmount());
        $this->assertEquals('13,03', CartProvider::instance()->getCartItems()->get('4213a65a817336f9e62699ee2c1d16f6')->getDiscountAmount());
        $this->assertEquals('207,69', CartProvider::instance()->getSubtotal());
        $this->assertEquals('186,92', CartProvider::instance()->getSubtotalWithDiscounts());
        $this->assertEquals('39,25', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('25,13', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('226,17', CartProvider::instance()->getTotal());

    }

    public function testCartCanAddVariousProductsWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->count());

        $this->assertEquals('171,64', CartProvider::instance()->getSubtotal());
        $this->assertEquals('36,05', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('207,69', CartProvider::instance()->getTotal());

        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals('343,29', CartProvider::instance()->getSubtotal());
        $this->assertEquals('72,09', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('415,38', CartProvider::instance()->getTotal());

    }

    public function testCartCanAddVariousProductsWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->count());

        $this->assertEquals('207,69', CartProvider::instance()->getSubtotal());
        $this->assertEquals('43,61', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('251,30', CartProvider::instance()->getTotal());

        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(new Item('294ad', 'Product 1', 1, 100, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));
        CartProvider::instance()->add(new Item('295ad', 'Product 2', 1, 107.69, 1.000, true, [
            new TaxRule('IVA', 21.00, 0, 0)
        ]));

        $this->assertEquals('415,38', CartProvider::instance()->getSubtotal());
        $this->assertEquals('87,23', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('0,00', CartProvider::instance()->getDiscountAmount());
        $this->assertEquals('502,61', CartProvider::instance()->getTotal());
    }

    public function testCartCanAddMultiple()
    {
        $this->expectsEvents('cart.added');

        for($i = 1; $i <= 5; $i++)
            CartProvider::instance()->add(new Item('293ad' . $i, 'Product', 2, 9.99, 1.000, true));

        $this->assertEquals(5, CartProvider::instance()->getCartItems()->count());
        $this->assertEquals(10, CartProvider::instance()->getQuantity());
    }

    public function testCartCanAddWithNumericId()
    {
        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(new Item(12345, 'Product', 2, 9.99, 1.000, true));
    }

    public function testCartCanChangeTaxRuleWithPriceWithTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITH_TAX]);

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                1,
                100,
                1.000,
                true,
                new TaxRule('VAT', 21.00)
            )
        );

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(21, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['21'], CartProvider::instance()->getCartItems()->first()->getTaxRates());
        $this->assertEquals('82,64', CartProvider::instance()->getSubtotal());
        $this->assertEquals('17,36', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('100,00', CartProvider::instance()->getTotal());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(17.35537190082644798394539975561201572418212890625, $item->taxAmount);
        }

        // add tax to each item from shopping cart
        foreach (CartProvider::instance()->getCartItems() as $item)
        {
            // reset tax rules from item
            $item->resetTaxRules();

            $item->addTaxRule(
                new TaxRule('VAT', 10.00)
            );

            $item->calculateAmounts();
        }

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(10, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['10'], CartProvider::instance()->getCartItems()->first()->getTaxRates());
        $this->assertEquals('82,64', CartProvider::instance()->getSubtotal());
        $this->assertEquals('8,26', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('90,91', CartProvider::instance()->getTotal());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(8.2644628099173562674195636645890772342681884765625, $item->taxAmount);
        }
    }

    public function testCartCanChangeTaxRuleWithPriceWithoutTax()
    {
        $this->expectsEvents('cart.added');

        config(['pulsar-shopping_cart.product_tax_prices' => Cart::PRICE_WITHOUT_TAX]);

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                1,
                100,
                1.000,
                true,
                new TaxRule('VAT', 21.00)
            )
        );

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(21, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['21'], CartProvider::instance()->getCartItems()->first()->getTaxRates());
        $this->assertEquals('100,00', CartProvider::instance()->getSubtotal());
        $this->assertEquals('21,00', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('121,00', CartProvider::instance()->getTotal());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(21.0, $item->taxAmount);
        }

        // add tax to each item from shopping cart
        foreach (CartProvider::instance()->getCartItems() as $item)
        {
            // reset tax rules from item
            $item->resetTaxRules();

            $item->addTaxRule(
                new TaxRule('VAT', 10.00)
            );

            $item->calculateAmounts();
        }

        $this->assertEquals(1, CartProvider::instance()->getCartItems()->first()->taxRules->count());
        $this->assertEquals(10, CartProvider::instance()->getCartItems()->first()->taxRules->first()->taxRate);
        $this->assertEquals(['10'], CartProvider::instance()->getCartItems()->first()->getTaxRates());
        $this->assertEquals('100,00', CartProvider::instance()->getSubtotal());
        $this->assertEquals('10,00', CartProvider::instance()->getTaxAmount());
        $this->assertEquals('110,00', CartProvider::instance()->getTotal());

        foreach(CartProvider::instance()->getCartItems() as $item)
        {
            $this->assertEquals(10.0, $item->taxAmount);
        }
    }

    public function testCartCanGetCost()
    {
        $this->expectsEvents('cart.added');

        CartProvider::instance()->add(
            new Item(
                '293ad',
                'Product 1',
                2,
                9.99,
                1.000,
                true,
                [],
                [],
                2
            )
        );

        $this->assertEquals(2, CartProvider::instance()->getCartItems()->first()->cost);
        $this->assertEquals('2,00', CartProvider::instance()->getCartItems()->first()->getCost());
        $this->assertEquals(4, CartProvider::instance()->getCartItems()->first()->totalCost);
        $this->assertEquals('4,00', CartProvider::instance()->getCartItems()->first()->getTotalCost());
    }
}