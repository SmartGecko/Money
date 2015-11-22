<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 16/11/15
 * Time: 22:44
 */

namespace SmartGecko\Money;


class Currency implements \Serializable
{

    /**
     * @var string The currency code, not null.
     */
    private $code;
    /**
     * @var int The numeric currency code.
     */
    private $numericCode;
    /**
     * @var int The number of decimal places.
     */
    private $decimalPlaces;

    /**
     * @var array
     */
    private static $currenciesByCode;

    /**
     * @var array
     */
    private static $currenciesByNumericCode;

    /**
     * @var array
     */
    private static $currenciesByCountry;

    const CODE = "/[A-Z][A-Z][A-Z]/";

    /**
     * @param string $code
     * @param int $numericCode
     * @param int $decimalPlaces
     */
    private function __construct($code, $numericCode, $decimalPlaces)
    {
        $this->code = $code;
        $this->numericCode = $numericCode;
        $this->decimalPlaces = $decimalPlaces;
    }

    /**
     * Obtains an instance of {@code CurrencyUnit} for the specified three letter currency code.
     * <p>
     * A currency is uniquely identified by a three letter code, based on ISO-4217.
     * Valid currency codes are three upper-case ASCII letters.
     *
     * @param string $currencyCode the three-letter currency code, not null
     * @return Currency the singleton instance, never null
     * @throws InvalidCurrencyException if the currency is unknown
     */
    public static function of($currencyCode)
    {
        Utils::checkNotNull($currencyCode, "Currency code must not be null");

        if (!isset(self::$currenciesByCode[$currencyCode])) {
            throw new InvalidCurrencyException("Unknown currency \"".$currencyCode."\"");
        }

        return self::$currenciesByCode[$currencyCode];
    }

    /**
     * Obtains an instance of {@code CurrencyUnit} for the specified ISO-4217 numeric currency code.
     * <p>
     * The numeric code is an alternative to the three letter code.
     *
     * @param int $numericCurrencyCode the numeric currency code, not null
     * @return Currency the singleton instance, never null
     * @throws InvalidCurrencyException if the currency is unknown
     */
    public static function ofNumericCode($numericCurrencyCode)
    {
        Utils::checkNotNull($numericCurrencyCode, "Currency code must not be null");

        if (is_string($numericCurrencyCode)) {

            switch (strlen($numericCurrencyCode)) {
                case 1:
                    $numericCurrencyCode = intval($numericCurrencyCode[0]);
                    break;
                case 2:
                    $numericCurrencyCode = intval($numericCurrencyCode[0]) * 10 +
                        intval($numericCurrencyCode[1]);
                    break;
                case 3:
                    $numericCurrencyCode = intval($numericCurrencyCode[0]) * 100 +
                        intval($numericCurrencyCode[1]) * 10 +
                        intval($numericCurrencyCode[2]);
                    break;
                default:
                    throw new InvalidCurrencyException("Unknown currency \"".$numericCurrencyCode."\"");
            }
        }

        if (!isset(self::$currenciesByNumericCode[$numericCurrencyCode])) {
            throw new InvalidCurrencyException("Unknown currency \"".$numericCurrencyCode."\"");
        }

        return self::$currenciesByNumericCode[$numericCurrencyCode];
    }

    /**
     * Registers a currency allowing it to be used, allowing replacement.
     * <p>
     * This class only permits known currencies to be returned.
     * To achieve this, all currencies have to be registered in advance.
     * <p>
     * Since this method is public, it is possible to add currencies in
     * application code. It is recommended to do this only at startup.
     * <p>
     * This method uses a flag to determine whether the registered currency
     * must be new, or can replace an existing currency.
     * <p>
     * The currency code must be three upper-case ASCII letters, based on ISO-4217.
     * The numeric code must be from 0 to 999, or -1 if not applicable.
     *
     * @param string $currencyCode the three-letter upper-case currency code, not null
     * @param int $numericCurrencyCode the numeric currency code, from 0 to 999, -1 if none
     * @param int $decimalPlaces the number of decimal places that the currency
     *  normally has, from 0 to 9 (normally 0, 2 or 3), or -1 for a pseudo-currency
     * @param array $countryCodes the country codes to register the currency under,
     *  use of ISO-3166 is recommended, not null
     * @param bool $force true to register forcefully, replacing any existing matching currency,
     *  false to validate that there is no existing matching currency
     * @return Currency the new instance, never null
     * @throws \InvalidArgumentException if the code is already registered and {@code force} is false;
     *  or if the specified data is invalid
     */
    public static function registerCurrency(
        $currencyCode,
        $numericCurrencyCode,
        $decimalPlaces,
        $countryCodes,
        $force = false
    ) {
        Utils::checkNotNull($currencyCode, "Currency code must not be null");

        if (3 !== strlen($currencyCode)) {
            throw new \InvalidArgumentException("Invalid string code, must be length 3");
        }

        if (!preg_match(self::CODE, $currencyCode, $matches)) {
            throw new \InvalidArgumentException("Invalid string code, must be ASCII upper-case letters");
        }

        if ($numericCurrencyCode < -1 || $numericCurrencyCode > 999) {
            throw new \InvalidArgumentException("Invalid numeric code");
        }

        if ($decimalPlaces < -1 || $decimalPlaces > 9) {
            throw new \InvalidArgumentException("Invalid number of decimal places");
        }

        Utils::checkNotNull($countryCodes, "Country codes must not be null");

        $currency = new Currency($currencyCode, $numericCurrencyCode, $decimalPlaces);

        if ($force) {
            unset(self::$currenciesByCode[$currencyCode]);
            unset(self::$currenciesByNumericCode[$currencyCode]);

            foreach ($countryCodes as $countryCode) {
                unset(self::$currenciesByCountry[$countryCode]);
            }
        } else {
            if (isset(self::$currenciesByCode[$currencyCode]) || isset(self::$currenciesByNumericCode[$numericCurrencyCode])) {
                throw new \InvalidArgumentException("Currency already registered: ".$currencyCode);
            }
            foreach ($countryCodes as $countryCode) {
                if (isset(self::$currenciesByCountry[$countryCode])) {
                    throw new \InvalidArgumentException("Currency already registered for country: ".$countryCode);
                }
            }
        }

        self::$currenciesByCode[$currencyCode] = $currency;

        if ($numericCurrencyCode >= 0) {
            self::$currenciesByNumericCode[$numericCurrencyCode] = $currency;
        }

        foreach ($countryCodes as $countryCode) {
            self::$currenciesByCountry[$countryCode] = $currency;
        }

        return self::$currenciesByCode[$currencyCode];
    }

    /**
     * Gets the list of all registered currencies.
     * <p>
     * This class only permits known currencies to be returned, thus this list is
     * the complete list of valid singleton currencies. The list may change after
     * application startup, however this isn't recommended.
     *
     * @return Currency[] the sorted, independent, list of all registered currencies, never null
     */
    public static function registeredCurrencies()
    {
        // TODO sort
        return array_values(self::$currenciesByCode);
    }

    /**
     * Gets the number of decimal places typically used by this currency.
     * <p>
     * Different currencies have different numbers of decimal places by default.
     * For example, 'GBP' has 2 decimal places, but 'JPY' has zero.
     * Pseudo-currencies will return zero.
     * <p>
     * See also {@link #getDefaultFractionDigits()}.
     *
     * @return int the decimal places, from 0 to 9 (normally 0, 2 or 3)
     */
    public function getDecimalPlaces()
    {
        return $this->decimalPlaces < 0 ? 0 : $this->decimalPlaces;
    }

    /**
     * Checks if this is a pseudo-currency.
     *
     * @return bool true if this is a pseudo-currency
     */
    public function isPseudoCurrency()
    {
        return $this->decimalPlaces < 0;
    }

    /**
     * Gets the ISO-4217 three-letter currency code.
     * <p>
     * Each currency is uniquely identified by a three-letter upper-case code, based on ISO-4217.
     *
     * @return String the three-letter upper-case currency code, never null
     */
    public function getCode()
    {
        return $this->code;
    }

    //-----------------------------------------------------------------------
    /**
     * Gets the ISO-4217 three-letter currency code.
     * <p>
     * This method matches the API of {@link Currency}.
     *
     * @return string the currency code, never null
     */
    public function getCurrencyCode()
    {
        return $this->code;
    }

    /**
     * Gets the ISO-4217 numeric currency code.
     * <p>
     * The numeric code is an alternative to the standard string-based code.
     *
     * @return int the numeric currency code, -1 if no numeric code
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * Gets the number of fractional digits typically used by this currency.
     * <p>
     * Different currencies have different numbers of fractional digits by default.
     * For example, 'GBP' has 2 fractional digits, but 'JPY' has zero.
     * Pseudo-currencies are indicated by -1.
     * <p>
     * This method matches the API of {@link Currency}.
     * The alternative method {@link #getDecimalPlaces()} may be more useful.
     *
     * @return int fractional digits, from 0 to 9 (normally 0, 2 or 3), or -1 for pseudo-currencies
     */
    public function getDefaultFractionDigits()
    {
        return $this->decimalPlaces;
    }

    /**
     * Gets the country codes applicable to this currency.
     * <p>
     * A currency is typically valid in one or more countries.
     * The codes are typically defined by ISO-3166.
     * An empty set indicates that no the currency is not associated with a country code.
     *
     * @return array the country codes, may be empty, not null
     */
    public function getCountryCodes()
    {
        $countryCodes = [];

        foreach (self::$currenciesByCountry as $key => $currency) {
            if ($this == $currency) {
                $countryCodes[] = $key;
            }
        }

        return $countryCodes;
    }

    /**
     * Obtains an instance of {@code CurrencyUnit} for the specified ISO-3166 country code.
     * <p>
     * Country codes should generally be in upper case.
     * This method is case sensitive.
     *
     * @param string $countryCode the country code, typically ISO-3166, not null
     * @return Currency the singleton instance, never null
     * @throws InvalidCurrencyException if the currency is unknown
     */
    public static function ofCountry($countryCode)
    {
        Utils::checkNotNull($countryCode, "Country code must not be null");

        if (!isset(self::$currenciesByCountry[$countryCode])) {
            throw new InvalidCurrencyException("Unknown currency for country \"".$countryCode."\"");
        }

        return self::$currenciesByCountry[$countryCode];
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(
            [
                'code' => $this->code,
                'numericCode' => $this->numericCode,
                'decimalPlaces' => $this->decimalPlaces,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $currency = self::$currenciesByCode[$data['code']];

        if ($data['numericCode'] !== $currency->numericCode) {
            throw new \RuntimeException(
                "Deserialization found a mismatch in the numeric code for currency ".$data['code']
            );
        }

        if ($data['decimalPlaces'] !== $currency->decimalPlaces) {
            throw new \RuntimeException(
                "Deserialization found a mismatch in the decimal places for currency ".$data['code']
            );
        }

        $this->code = $currency->code;
        $this->numericCode = $currency->numericCode;
        $this->decimalPlaces = $currency->decimalPlaces;
    }


}