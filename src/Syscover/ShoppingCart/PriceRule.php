<?php namespace Syscover\ShoppingCart;

class PriceRule
{
    const WITHOUT_DISCOUNT                  = 1;
    const DISCOUNT_SUBTOTAL_PERCENTAGE      = 2;
    const DISCOUNT_SUBTOTAL_FIXED_AMOUNT    = 3;
    const DISCOUNT_TOTAL_PERCENTAGE         = 4;
    const DISCOUNT_TOTAL_FIXED_AMOUNT       = 5;

    /**
     * class name to generate id
     *
     * @var string
     */
    public $className;

    /**
     * ix from price rule to generate id
     *
     * @var string
     */
    public $ix;

    /**
     * id price rule
     *
     * @var string
     */
    public $id;

    /**
     * The price rule name, this name has to be translated.
     *
     * @var string
     */
    public $name;

    /**
     * The price rule name, this name has to be translated.
     *
     * @var string
     */
    public $description;

    /**
     * Discount type of price rule, this value can to be CONST defined
     *
     * @var int
     */
    public $discountType;

    /**
     * Discount object with values of discount
     *
     * @var \Syscover\ShoppingCart\Discount
     */
    public $discount;

    /**
     * Discount amount, this value is set in shopping cart in applyCartPriceRuleToCartItems method
     *
     * @var float
     */
    public  $discountAmount;

    /**
     * Check if this price rule is combinable with other price rules
     *
     * @var boolean
     */
    public $combinable;

    /**
     * Check if this price rule has free shipping
     *
     * @var boolean
     */
    public $freeShipping;

    /**
     * The options for this price rule
     *
     * @var array
     */
    public $options;


    /**
     * PriceRule constructor.
     * @param int       $ix                                     numeric data to generate unique ID
     * @param string    $className                              string data, class name if is possible, to generate unique ID
     * @param string    $name
     * @param string    $description
     * @param int       $discountType
     * @param bool      $combinable
     * @param float     $discountPercentage
     * @param float     $discountFixed
     * @param float     $maximumPercentageDiscountAmount
     * @param bool      $applyShippingAmount
     * @param bool      $freeShipping
     * @param array     $options
     */
    public function __construct(
        $ix,
        $className,
        $name,
        $description,
        $discountType,
        $freeShipping = false,
        $discountFixed = null,
        $discountPercentage = null,
        $maximumPercentageDiscountAmount = null,
        $applyShippingAmount = false,
        $combinable = true,
        array $options = []
    )
    {
        $this->ix                       = $ix;
        $this->className                = $className;
        $this->name                     = $name;
        $this->description              = $description;
        $this->discountType             = $discountType;
        $this->combinable               = $combinable;
        $this->freeShipping             = $freeShipping;
        $this->options                  = new Options($options);
        $this->id                       = $this->generateId();

        $this->discount = new Discount($this->id, $discountFixed, $discountPercentage, $maximumPercentageDiscountAmount, $applyShippingAmount);
    }

    /**
     * Generate a unique id for the new cartPriceRule
     *
     * @return string
     */
    protected function generateId()
    {
        return md5($this->ix . $this->className .  $this->discountType . $this->combinable);
    }

    /**
     * Get an attribute from the cart item or get the associated model.
     *
     * @param   string  $attribute
     * @return  mixed
     */
    public function __get($attribute)
    {
        if(property_exists($this, $attribute)) {
            return $this->{$attribute};
        }
        return null;
    }

    /**
     * Returns the formatted sum tax rate.
     *
     * @param int       $decimals
     * @param string    $decimalPoint
     * @param string    $thousandSeparator
     * @return string
     */
    public function getDiscountPercentage($decimals = 0, $decimalPoint = ',', $thousandSeparator = '.')
    {
        return number_format($this->discount->percentage, $decimals, $decimalPoint, $thousandSeparator);
    }

    /**
     * Returns the formatted discount fixed.
     *
     * @param int       $decimals
     * @param string    $decimalPoint
     * @param string    $thousandSeparator
     * @return string
     */
    public function getDiscountFixed($decimals = 0, $decimalPoint = ',', $thousandSeparator = '.')
    {
        return number_format($this->discount->fixed, $decimals, $decimalPoint, $thousandSeparator);
    }
}