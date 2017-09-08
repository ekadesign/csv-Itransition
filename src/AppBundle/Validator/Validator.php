<?php

namespace AppBundle\Validator;

use function PHPSTORM_META\type;

class Validator
{
    /**
     * @return bool
     */

    public $allowedMinPrice = 500; //allowed Minimal Price in cents
    public $allowedMaxPrice = 100000; //allowed Maxmal Price in cents
    public $allowedMinStock = 10;
    public $preparedPriceInCents;
    public $item;

    public function __construct($item)
    {
        $this->item = $item;
        $this->preparedPriceInCents = preg_replace("/[^\d\.]/", '', $this->item->price) * 100;
    }

    public function validateMinPriceAndStock(): bool
    {
        if ($this->preparedPriceInCents < $this->allowedMinPrice && preg_replace("/[^\d\.]/", '', $this->item->qty) < $this->allowedMinStock)
        return false;
        return true;
    }


    public function validateMaxPrice(): bool
    {
        if ($this->preparedPriceInCents < (int)$this->allowedMaxPrice)
            return true;
        return false;
    }

    public function checkPriceField(): bool
    {
        if ($this->item->price !== NULL && is_numeric($this->item->price)) {
            return true;
        }
        return false;
    }

    public function checkQtyField(): bool
    {
        if ($this->item->qty !== NULL && !preg_match("/(\D+)/", $this->item->qty)) {
            return true;
        }
        return false;
    }

    public function getValidate()
    {
        if ($this->checkPriceField() && $this->checkQtyField() && $this->validateMaxPrice()) {
            return true;
        }
        return false;
    }

}