<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 13:44
 */

namespace SmartGecko\Money;

/**
 * Provider for available currencies.
 */
abstract class AbstractCurrencyDataProvider
{
    /**
     * Registers all the currencies known by this provider.
     */
    public abstract function registerCurrencies();

    /**
     * Registers a currency allowing it to be used.
     * <p>
     * This method is called by {@link #registerCurrencies()} to perform the
     * actual creation of a currency.
     *
     * @param string $currencyCode  the currency code, not null
     * @param int $numericCurrencyCode  the numeric currency code, -1 if none
     * @param int $decimalPlaces  the number of decimal places that the currency
     *  normally has, from 0 to 3, or -1 for a pseudo-currency
     * @param array $countryCodes  the country codes to register the currency under, not null
     */
    protected final function registerCurrency(
        $currencyCode,
        $numericCurrencyCode,
        $decimalPlaces,
        array $countryCodes = []
    ) {
        Currency::registerCurrency($currencyCode, $numericCurrencyCode, $decimalPlaces, $countryCodes, true);
    }
}