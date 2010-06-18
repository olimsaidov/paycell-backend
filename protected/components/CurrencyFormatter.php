<?php

class CurrencyFormatter extends CNumberFormatter
{

    public function __construct()
    {
        parent::__construct('ru');
    }

    public function format($amount)
    {
        return parent::format(',###', $amount);
    }

    public function init()
    {

    }
}

