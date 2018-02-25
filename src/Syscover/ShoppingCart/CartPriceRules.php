<?php namespace Syscover\ShoppingCart;

use Illuminate\Support\Collection;

class CartPriceRules extends Collection
{
    /**
     * Create array object with values to create a order
     *
     * @param int $customerId
     * @param int $orderId
     * @return array
     */
    public function getDataCustomerDiscountHistory(int $customerId, int $orderId)
    {
        $data = [];
        foreach ($this as $discount)
        {
            $dataAux = [];
            $dataAux['customer_id']                             = $customerId;
            $dataAux['order_id']                                = $orderId;

            //$dataAux['has_coupon']                              = $discount->;



            $data[] = $dataAux;
        }
        return $data;
    }
}