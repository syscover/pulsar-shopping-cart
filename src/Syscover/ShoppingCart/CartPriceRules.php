<?php namespace Syscover\ShoppingCart;

use Illuminate\Support\Collection;

class CartPriceRules extends Collection
{
    /**
     * Create array object with values to create a order
     *
     * @param int $customerId
     * @param int $orderId
     * @param bool $applied
     * @return array
     */
    public function getDataCustomerDiscountHistory(int $customerId, int $orderId, bool $applied)
    {
        $data = [];
        foreach ($this as $discount)
        {
            $priceRule = $discount->options->priceRule;

            $dataAux = [];
            $dataAux['customer_id']                             = $customerId;
            $dataAux['order_id']                                = $orderId;
            $dataAux['applied']                                 = $applied;
            $dataAux['discount_amount']                         = $discount->discountAmount;
            $dataAux['rule_type']                               = get_class($priceRule);
            $dataAux['rule_id']                                 = $priceRule->id;
            $dataAux['price_rule']                              = $priceRule;

            $data[] = array_merge($dataAux, $priceRule->toArray());
        }

        return $data;
    }
}