<?php namespace Syscover\ShoppingCart;

class TaxRule
{
    /**
     * id tax rule
     *
     * @var string
     */
    public $id;

    /**
     * The tax rule name, this name has to be translated.
     *
     * @var string
     */
    public $name;

    /**
     * Priority to calculate tax on subtotal.
     *
     * @var string
     */
    public $priority;

    /**
     * Sort to appear on order.
     *
     * @var string
     */
    public $sort;

    /**
     * Array that contain float value of tax rates to apply on price.
     *
     * @var \float
     */
    public $taxRate;

    /**
     * Amount of this taxRule.
     *
     * @var \float
     */
    public $taxAmount;

    /**
     * CartItemTaxRule constructor.
     *
     * @param string $name
     * @param int $priority
     * @param int $sort
     * @param float $taxRate
     */
    public function __construct($name, $taxRate, $priority = 0, $sort = 0)
    {
        if (empty($name))
            throw new \InvalidArgumentException('Please supply a valid name.');

        if (strlen($taxRate) < 0 || ! is_numeric($taxRate))
            throw new \InvalidArgumentException('Please supply a valid taxRate.');

        if (strlen($priority) < 0 || ! is_numeric($priority))
            throw new \InvalidArgumentException('Please supply a valid priority.');

        if (strlen($sort) < 0 || ! is_numeric($sort))
            throw new \InvalidArgumentException('Please supply a valid sort.');

        $this->id           = $this->generateId($name, $priority);
        $this->name         = $name;
        $this->priority     = (int)$priority;
        $this->sort         = (int)$sort;
        $this->taxRate      = (float)$taxRate;
    }

    /**
     * Get an attribute from the cart item or get the associated model.
     *
     * @param string $attribute
     * @return mixed
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
    public function getTaxRate($decimals = 0, $decimalPoint = ',', $thousandSeparator = '.')
    {
        return number_format($this->taxRate, $decimals, $decimalPoint, $thousandSeparator);
    }

    /**
     * Returns the formatted sum tax rate.
     *
     * @param int       $decimals
     * @param string    $decimalPoint
     * @param string    $thousandSeparator
     * @return string
     */
    public function getTaxAmount($decimals = 2, $decimalPoint = ',', $thousandSeparator = '.')
    {
        return number_format($this->taxAmount, $decimals, $decimalPoint, $thousandSeparator);
    }

    /**
     * Generate a unique id for the cart item.
     *
     * @param string $name
     * @return string
     */
    protected function generateId($name, $priority)
    {
        return md5($name . $priority);
    }
}