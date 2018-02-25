<?php namespace Syscover\ShoppingCart;

use Illuminate\Support\Collection;

class CartItems extends Collection
{
    /**
     * Create array object with values to create a order
     *
     * @param int $orderId
     * @param string $langId
     * @return array
     */
    public function getDataOrder(int $orderId, string $langId)
    {
        $data = [];
        foreach ($this as $item)
        {
            $dataAux = [];
            $dataAux['order_id']                                = $orderId;
            $dataAux['lang_id']                                 = $langId;

            // product
            $dataAux['product_id']                              = $item->id;
            $dataAux['name']                                    = $item->name;
            $dataAux['description']                             = $item->options->product->description;
            $dataAux['data']                                    = ['product' => $item->options->product];

            // amounts
            $dataAux['price']                                   = $item->price;
            $dataAux['quantity']                                = $item->quantity;
            $dataAux['subtotal']                                = $item->subtotal;
            $dataAux['total_without_discounts']                 = $item->totalWithoutDiscounts;

            // discounts
            $dataAux['discount_subtotal_percentage']            = $item->discountSubtotalPercentage;
            $dataAux['discount_total_percentage']               = $item->discountTotalPercentage;
            $dataAux['discount_subtotal_percentage_amount']     = $item->discountSubtotalPercentageAmount;
            $dataAux['discount_total_percentage_amount']        = $item->discountTotalPercentageAmount;
            $dataAux['discount_subtotal_fixed_amount']          = $item->discountSubtotalFixedAmount;
            $dataAux['discount_total_fixed_amount']             = $item->discountTotalFixedAmount;
            $dataAux['discount_amount']                         = $item->discountAmount;

            // subtotal with discounts
            $dataAux['subtotal_with_discounts']                 = $item->subtotalWithDiscounts;

            // taxes
            $dataAux['tax_rules']                               = $item->taxRules->values();
            $dataAux['tax_amount']                              = $item->taxAmount;

            // total
            $dataAux['total']                                   = $item->total;

            // gift
            $dataAux['has_gift']                                = $item->options->gift != null? true : false;
            $dataAux['gift_from']                               = isset($item->options->gift['from'])? $item->options->gift['from'] : null;
            $dataAux['gift_to']                                 = isset($item->options->gift['to'])? $item->options->gift['to'] : null;
            $dataAux['gift_message']                            = isset($item->options->gift['message'])? $item->options->gift['message'] : null;
            $dataAux['gift_comments']                           = isset($item->options->gift['comments'])? $item->options->gift['comments'] : null;

            $data[] = $dataAux;
        }
        return $data;
    }
}