<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 18/11/15
 * Time: 21:38
 */

namespace SmartGecko\Money;


class CurrencyMismatchException extends \Exception
{
    /** @var Currency First currency. */
    private $firstCurrency;
    /** @var Currency  Second currency. */
    private $secondCurrency;

    /**
     * Constructor.
     *
     * @param Currency $firstCurrency the first currency, may be null
     * @param Currency $secondCurrency the second currency, not null
     */
    public function __construct(Currency $firstCurrency = null, Currency $secondCurrency = null)
    {
        parent::__construct(
            "Currencies differ: ".
            (null !== $firstCurrency ? $firstCurrency->getCode() : "null")."/".
            (null !== $secondCurrency ? $secondCurrency->getCode() : "null")
        );
        $this->firstCurrency = $firstCurrency;
        $this->secondCurrency = $secondCurrency;
    }

//-----------------------------------------------------------------------
    /**
     * Gets the first currency at fault.
     *
     * @return Currency the currency at fault, may be null
     */
    public function getFirstCurrency()
    {
        return $this->firstCurrency;
    }

    /**
     * Gets the second currency at fault.
     *
     * @return Currency the currency at fault, may be null
     */
    public function getSecondCurrency()
    {
        return $this->secondCurrency;
    }
}