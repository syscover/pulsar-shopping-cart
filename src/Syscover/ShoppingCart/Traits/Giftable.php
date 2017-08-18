<?php namespace Syscover\ShoppingCart\Traits;

trait Giftable
{
    /**
     * data of gift, message, from, to, etc.
     *
     * @var Collection
     */
    public $gift;

    /**
     * Check if cart has gift
     *
     * @return boolean | void
     */
    public function hasGift()
    {
        if( is_object($this->gift) &&
            get_class($this->gift) === 'Illuminate\Support\Collection' &&
            $this->gift->count() > 0
        )
            return true;

        return false;
    }

    /**
     * Get gift data
     *
     * @return Collection
     */
    public function getGift()
    {
        return $this->gift ? $this->gift : collect();
    }

    /**
     * Set gift
     *
     * @param   array   $gift
     * @return  void
     */
    public function setGift($gift)
    {
        $this->gift = collect($gift);
    }
}