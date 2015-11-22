<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 16/11/15
 * Time: 22:44
 */

namespace SmartGecko\Money;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class BigMoney implements BigMoneyProviderInterface, \Serializable
{

    /**
     * @var BigDecimal
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    const PARSE_REGEX = "/[+-]?[0-9]*[.]?[0-9]*/";

    /**
     * Constructor, creating a new monetary instance.
     *
     * @param Currency $currency the currency to use, not null
     * @param BigDecimal $amount the amount of money, not null
     */
    private function __construct(Currency $currency, BigDecimal $amount)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Parses an instance of {@code BigMoney} from a string.
     * <p>
     * The string format is '$currencyCode $amount' where there may be
     * zero to many spaces between the two parts.
     * The currency code must be a valid three letter currency.
     * The amount must match the regular expression {@code [+-]?[0-9]*[.]?[0-9]*}.
     * The spaces and numbers must be ASCII characters.
     * This matches the output from {@link #toString()}.
     * <p>
     * For example, {@code parse("USD 25")} creates the instance {@code USD 25}
     * while {@code parse("USD 25.95")} creates the instance {@code USD 25.95}.
     *
     * @param string $moneyStr the money string to parse, not null
     * @return BigMoney the parsed instance, never null
     * @throws \InvalidArgumentException
     */
    public static function parse($moneyStr)
    {
        Utils::checkNotNull($moneyStr, "Money must not be null");

        if (strlen($moneyStr) < 4) {
            throw new \InvalidArgumentException("Money '".$moneyStr."' cannot be parsed");
        }

        $currStr = substr($moneyStr, 0, 3);
        $amountStr = trim(substr($moneyStr, 3));

        if (!preg_match(self::PARSE_REGEX, $amountStr, $matches)) {
            throw new \InvalidArgumentException("Money amount '".$moneyStr."' cannot be parsed");
        }

        return BigMoney::of(Currency::of($currStr), $amountStr);
    }

    /**
     * @param Currency $currency
     * @param BigDecimal|float|string $amount
     * @return BigMoney
     */
    public static function of(Currency $currency, $amount)
    {
        Utils::checkNotNull($amount, "Amount must not be null");

        return new BigMoney($currency, BigDecimal::of($amount));
    }

    /**
     * Obtains an instance of {@code BigMoney} from a {@code double} using a
     * well-defined conversion, rounding as necessary.
     * <p>
     * This allows you to create an instance with a specific currency and amount.
     * If the amount has a scale in excess of the scale of the currency then the excess
     * fractional digits are rounded using the rounding mode.
     * The result will have a minimum scale of zero.
     *
     * @param Currency $currency the currency, not null
     * @param BigDecimal|float|string $amount the amount of money, not null
     * @param int $scale the scale to use, zero or positive
     * @param int $roundingMode the rounding mode to use, not null
     * @return BigMoney the new instance, never null
     * throws ArithmeticException if the rounding fails
     */
    /*   TODO  public static function ofScale(Currency $currency, $amount, $scale, $roundingMode = RoundingMode::UNNECESSARY)
        {
            Utils::checkNotNull($currency, "CurrencyUnit must not be null");
            Utils::checkNotNull($amount, "Amount must not be null");
            Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

            if ($amount instanceof BigDecimal) {
                $amount = $amount->toScale($scale, $roundingMode);
            } else {
                $amount = BigDecimal::ofUnscaledValue($amount, $scale);
            }

            return BigMoney::of($currency, $amount);
        }*/

    /**
     * Obtains an instance of {@code BigMoney} from an amount in major units.
     * <p>
     * This allows you to create an instance with a specific currency and amount.
     * The scale of the money will be zero.
     * <p>
     * The amount is a whole number only. Thus you can initialise the value
     * 'USD 20', but not the value 'USD 20.32'.
     * For example, {@code ofMajor(USD, 25)} creates the instance {@code USD 25}.
     *
     * @param Currency $currency the currency, not null
     * @param int $amountMajor the amount of money in the major division of the currency
     * @return BigMoney the new instance, never null
     */
    public static function ofMajor(Currency $currency, $amountMajor)
    {
        return BigMoney::of($currency, (string)$amountMajor);
    }

    /**
     * Obtains an instance of {@code BigMoney} from an amount in minor units.
     * <p>
     * This allows you to create an instance with a specific currency and amount
     * expressed in terms of the minor unit.
     * The scale of the money will be that of the currency, such as 2 for USD or 0 for JPY.
     * <p>
     * For example, if constructing US Dollars, the input to this method represents cents.
     * Note that when a currency has zero decimal places, the major and minor units are the same.
     * For example, {@code ofMinor(USD, 2595)} creates the instance {@code USD 25.95}.
     *
     * @param Currency $currency the currency, not null
     * @param int $amountMinor the amount of money in the minor division of the currency
     * @return BigMoney the new instance, never null
     */
    public static function ofMinor(Currency $currency, $amountMinor)
    {
        return BigMoney::of($currency, BigDecimal::ofUnscaledValue($amountMinor, $currency->getDecimalPlaces()));
        /*$multipliers = [1, 10, 100, 1000];

        return BigMoney::of(
            $currency,
            BigDecimal::fromFloat($amountMinor / $multipliers[$currency->getDecimalPlaces()])
        );*/
    }

    /**
     * Obtains an instance of {@code BigMoney} representing zero.
     * <p>
     * The scale of the money will be zero.
     * For example, {@code zero(USD)} creates the instance {@code USD 0}.
     *
     * @param Currency $currency the currency, not null
     * @return BigMoney the instance representing zero, never null
     */
    public static function zero(Currency $currency)
    {
        return BigMoney::of($currency, 0.0);
    }

    /**
     * Obtains an instance of {@code BigMoney} from a provider.
     * <p>
     * This allows you to create an instance from any class that implements the
     * provider, such as {@code Money}.
     * This method simply calls {@link BigMoneyProvider#toBigMoney()} checking for nulls.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to convert, not null
     * @return BigMoney the new instance, never null
     */
    public static function ofProvider(BigMoneyProviderInterface $moneyProvider)
    {
        $money = $moneyProvider->toBigMoney();
        Utils::checkNotNull($money, "BigMoneyProvider must not return null");

        return $money;
    }

    /**
     * @return BigDecimal
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Returns a copy of this monetary value with the specified currency.
     * <p>
     * The returned instance will have the specified currency and the amount
     * from this instance. No currency conversion or alteration to the scale occurs.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param Currency $currency the currency to use, not null
     * @return BigMoney the new instance with the input currency set, never null
     */
    public function withCurrency(Currency $currency)
    {
        Utils::checkNotNull($currency, "CurrencyUnit must not be null");
        if ($this->currency === $currency) {
            return $this;
        }

        return new BigMoney($currency, $this->amount);
    }

    /**
     * Returns a copy of this monetary value with the specified scale,
     * using the specified rounding mode if necessary.
     * <p>
     * The returned instance will have this currency and the new scaled amount.
     * For example, scaling 'USD 43.271' to a scale of 1 with HALF_EVEN rounding
     * will yield 'USD 43.3'.
     * A negative scale may be passed in, but the result will have a minimum scale of zero.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $scale the scale to use
     * @param int $roundingMode the rounding mode to use, not null
     * @return BigMoney the new instance with the input amount set, never null
     * throws ArithmeticException if the rounding fails
     */
    public function withScale($scale, $roundingMode = RoundingMode::UNNECESSARY)
    {
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        if ($scale === $this->amount->scale()) {
            return $this;
        }

        return BigMoney::of($this->currency, $this->amount->toScale($scale, $roundingMode));
    }

    /**
     * Returns a copy of this monetary value with the scale of the currency,
     * using the specified rounding mode if necessary.
     * <p>
     * The returned instance will have this currency and the new scaled amount.
     * For example, scaling 'USD 43.271' will yield 'USD 43.27' as USD has a scale of 2.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $roundingMode the rounding mode to use, not null
     * @return BigMoney the new instance with the input amount set, never null
     * throws ArithmeticException if the rounding fails
     */
    public function withCurrencyScale($roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->withScale($this->currency->getDecimalPlaces(), $roundingMode);
    }

    /**
     * Returns a copy of this monetary value with the specified amount.
     * <p>
     * The returned instance will have this currency and the new amount.
     * The scale of the returned instance will be that of the specified BigDecimal.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|string|int|float $amount the monetary amount to set in the returned instance, not null
     * @return BigMoney the new instance with the input amount set, never null
     */
    public function withAmount($amount)
    {
        Utils::checkNotNull($amount, "Amount must not be null");

        $newAmount = BigDecimal::of($amount);
        if ($this->amount->isEqualTo($newAmount)) {
            return $this;
        }

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->amount->scale();
    }

    /**
     * Checks if this money has the scale of the currency.
     * <p>
     * Each currency has a default scale, such as 2 for USD and 0 for JPY.
     * This method checks if the current scale matches the default scale.
     *
     * @return bool true if the scale equals the current default scale
     */
    public function isCurrencyScale()
    {
        return $this->amount->scale() === $this->currency->getDecimalPlaces();
    }

    /**
     * Checks if this monetary value is equal to another.
     * <p>
     * This ignores the scale of the amount.
     * Thus, 'USD 30.00' and 'USD 30' are equal.
     * <p>
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is greater than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function isEqual(BigMoneyProviderInterface $other)
    {
        return 0 === $this->compareTo($other);
    }


    /**
     * Checks if this monetary value is greater than another.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is greater than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function isGreaterThan(BigMoneyProviderInterface $other)
    {
        return $this->compareTo($other) > 0;
    }

    /**
     * Checks if this monetary value is less than another.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is less than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function isLessThan(BigMoneyProviderInterface $other)
    {
        return $this->compareTo($other) < 0;
    }

    /**
     * Checks if the amount is zero.
     *
     * @return bool if the amount is zero
     */
    public function isZero()
    {
        return 0 === bccomp($this->amount, "0", $this->getScale());
    }

    /**
     * Checks if the amount is greater than zero.
     *
     * @return bool true if the amount is greater than zero
     */
    public function isPositive()
    {
        return 1 === bccomp($this->amount, "0", $this->getScale());
    }

    /**
     * Checks if the amount is zero or greater.
     *
     * @return bool true if the amount is zero or greater
     */
    public function isPositiveOrZero()
    {
        return $this->isPositive() || $this->isZero();
    }

    /**
     * Checks if the amount is less than zero.
     *
     * @return bool true if the amount is less than zero
     */
    public function isNegative()
    {
        return $this->amount->isNegative();
    }

    /**
     * Checks if the amount is zero or less.
     *
     * @return bool true if the amount is zero or less
     */
    public function isNegativeOrZero()
    {
        return $this->isZero() || $this->isNegative();
    }

    /**
     * Checks if this instance and the specified instance have the same currency.
     *
     * @param BigMoneyProviderInterface $money the money to check, not null
     * @return bool true if they have the same currency
     */
    public function isSameCurrency(BigMoneyProviderInterface $money)
    {
        return $this->currency == $money->toBigMoney()->currency;
    }

    /**
     * Returns a copy of this monetary value rounded to the specified scale without
     * changing the current scale.
     * <p>
     * Scale is described in {@link BigDecimal} and represents the point below which
     * the monetary value is zero. Negative scales round increasingly large numbers.
     * Unlike {@link #withScale(int)}, this scale of the result is unchanged.
     * <ul>
     * <li>Rounding 'EUR 45.23' to a scale of -1 returns 40.00 or 50.00 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 0 returns 45.00 or 46.00 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 1 returns 45.20 or 45.30 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 2 has no effect (it already has that scale).
     * <li>Rounding 'EUR 45.23' to a scale of 3 has no effect (the scale is not increased).
     * </ul>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $scale the new scale
     * @param int $roundingMode the rounding mode to use, not null
     * @return BigMoney the new instance with the amount converted to be positive, never null
     */
    public function rounded($scale, $roundingMode)
    {
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        if ($scale >= $this->getScale()) {
            return $this;
        }

        $currentScale = $this->amount->scale();
        $newAmount = $this->amount->toScale($scale, $roundingMode)->toScale($currentScale);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount negated.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @return BigMoney the new instance with the amount negated, never null
     */
    public function negated()
    {
        if ($this->isZero()) {
            return $this;
        }

        return self::of($this->currency, $this->amount->negated());
    }

    /**
     * Returns a copy of this monetary value with a positive amount.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @return BigMoney the new instance with the amount converted to be positive, never null
     */
    public function abs()
    {
        return ($this->isNegative() ? $this->negated() : $this);
    }

    /**
     * Gets the amount in minor units as a {@code BigDecimal} with scale 0.
     * <p>
     * This returns the monetary amount in terms of the minor units of the currency,
     * truncating the amount if necessary.
     * For example, 'EUR 2.35' will return 235, and 'BHD -1.345' will return -1345.
     * <p>
     * This is returned as a {@code BigDecimal} rather than a {@code BigInteger}.
     * This is to allow further calculations to be performed on the result.
     * Should you need a {@code BigInteger}, simply call {@link BigDecimal#toBigInteger()}.
     *
     * @return BigDecimal the minor units part of the amount, never null
     */
    public function getAmountMinor()
    {
        $dp = $this->currency->getDecimalPlaces();

        return $this->amount->toScale($dp, RoundingMode::UNNECESSARY)
            ->withPointMovedRight($dp);
    }

    /**
     * Gets the amount in major units as an {@code int}.
     * <p>
     * This returns the monetary amount in terms of the major units of the currency,
     * truncating the amount if necessary.
     * For example, 'EUR 2.35' will return 2, and 'BHD -1.345' will return -1.
     *
     * @return int the major units part of the amount
     * throws ArithmeticException if the amount is too large for an {@code int}
     */
    public function getAmountMinorInt()
    {
        return $this->getAmountMinor()->toInteger();
    }

    /**
     * Gets the amount in major units as a {@code BigDecimal} with scale 0.
     * <p>
     * This returns the monetary amount in terms of the major units of the currency,
     * truncating the amount if necessary.
     * For example, 'EUR 2.35' will return 2, and 'BHD -1.345' will return -1.
     * <p>
     * This is returned as a {@code BigDecimal} rather than a {@code BigInteger}.
     * This is to allow further calculations to be performed on the result.
     * Should you need a {@code BigInteger}, simply call {@link BigDecimal#toBigInteger()}.
     *
     * @return BigDecimal the major units part of the amount, never null
     */
    public function getAmountMajor()
    {
        return $this->amount->toScale(0, RoundingMode::DOWN);
    }

    /**
     * Gets the amount in major units as an {@code int}.
     * <p>
     * This returns the monetary amount in terms of the major units of the currency,
     * truncating the amount if necessary.
     * For example, 'EUR 2.35' will return 2, and 'BHD -1.345' will return -1.
     *
     * @return int the major units part of the amount
     * throws ArithmeticException if the amount is too large for an {@code int}
     */
    public function getAmountMajorInt()
    {
        return $this->getAmountMajor()->toInteger();
    }

    /**
     * Gets the minor part of the amount.
     * <p>
     * This return the minor unit part of the monetary amount.
     * This is defined as the amount in minor units excluding major units.
     * <p>
     * For example, EUR has a scale of 2, so the minor part is always between 0 and 99
     * for positive amounts, and 0 and -99 for negative amounts.
     * Thus 'EUR 2.35' will return 35, and 'EUR -1.34' will return -34.
     *
     * @return int the minor part of the amount, negative if the amount is negative
     */
    public function getMinorPart()
    {
        $dp = $this->getCurrency()->getDecimalPlaces();

        return $this->amount->toScale($dp, RoundingMode::DOWN)
            ->remainder(BigDecimal::one())
            ->withPointMovedRight($dp)
            ->toInteger();
    }

    /**
     * Returns a copy of this monetary value with the amount added.
     * <p>
     * This adds the specified amount to this monetary amount, returning a new object.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the two scales.
     * For example, 'USD 25.95' plus '3.021' gives 'USD 28.971'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|BigMoneyProviderInterface|string|float|int $amountToAdd the monetary value to add, not null
     * @return BigMoney the new instance with the input amount added, never null
     */
    public function plus($amountToAdd)
    {
        Utils::checkNotNull($amountToAdd, "Amount can not be null.");

        if ($amountToAdd instanceof BigMoneyProviderInterface) {
            $this->checkCurrencyEqual($amountToAdd);

            $plusAmount = $amountToAdd->toBigMoney()->getAmount();
        } else {
            $plusAmount = BigDecimal::of($amountToAdd);
        }

        if ($plusAmount->isZero()) {
            return $this;
        }

        $newAmount = $this->amount->plus($plusAmount);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount subtracted.
     * <p>
     * This subtracts the specified amount from this monetary amount, returning a new object.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the two scales.
     * For example,'USD 25.95' minus '3.021' gives 'USD 22.929'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|BigMoneyProviderInterface|string|float|int $amountToSubtract the monetary value to subtract, not null
     * @return BigMoney the new instance with the input amount subtracted, never null
     */
    public function minus($amountToSubtract)
    {
        Utils::checkNotNull($amountToSubtract, "Amount can not be null.");

        if ($amountToSubtract instanceof BigMoneyProviderInterface) {
            $this->checkCurrencyEqual($amountToSubtract);

            $plusAmount = $amountToSubtract->toBigMoney()->getAmount();
        } else {
            $plusAmount = BigDecimal::of($amountToSubtract);
        }

        if ($plusAmount->isZero()) {
            return $this;
        }

        $newAmount = $this->amount->minus($plusAmount);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount subtracted retaining
     * the scale by rounding the result.
     * <p>
     * The scale of the result will be the same as the scale of this instance.
     * For example,'USD 25.95' minus '3.029' gives 'USD 22.92' with most rounding modes.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|BigMoneyProviderInterface|string|float|int $amountToSubtract the monetary value to add, not null
     * @param int $roundingMode the rounding mode to use to adjust the scale, not null
     * @return BigMoney the new instance with the input amount subtracted, never null
     */
    public function minusRetainScale($amountToSubtract, $roundingMode)
    {
        Utils::checkNotNull($amountToSubtract, "Amount must not be null");
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        if ($amountToSubtract instanceof BigMoneyProviderInterface) {
            $this->checkCurrencyEqual($amountToSubtract);

            $amount = $amountToSubtract->toBigMoney()->getAmount();
        } else {
            $amount = BigDecimal::of($amountToSubtract);
        }

        if (0 === $amount->compareTo(BigDecimal::zero())) {
            return $this;
        }

        $newAmount = $this->amount->minus($amount);
        $newAmount = $newAmount->toScale($this->getScale(), $roundingMode);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount in major units subtracted.
     * <p>
     * This subtracts the specified amount in major units from this monetary amount,
     * returning a new object. The minor units will be untouched in the result.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the current scale and 0.
     * For example, 'USD 23.45' minus '138' gives 'USD -114.55'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToSubtract the monetary value to subtract, not null
     * @return BigMoney the new instance with the input amount subtracted, never null
     */
    public function minusMajor($amountToSubtract)
    {
        if ($amountToSubtract === 0) {
            return $this;
        }

        $newAmount = $this->amount->minus(BigDecimal::of($amountToSubtract));

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount in minor units subtracted.
     * <p>
     * This subtracts the specified amount in minor units from this monetary amount,
     * returning a new object.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the current scale and the default currency scale.
     * For example, USD 23.45 minus '138' gives 'USD 22.07'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToSubtract the monetary value to subtract, not null
     * @return BigMoney the new instance with the input amount subtracted, never null
     */
    public function minusMinor($amountToSubtract)
    {
        if ($amountToSubtract === 0) {
            return $this;
        }

        $newAmount = $this->amount->minus(
            BigDecimal::ofUnscaledValue($amountToSubtract, $this->currency->getDecimalPlaces())
        );

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount in major units added.
     * <p>
     * This adds the specified amount in major units to this monetary amount,
     * returning a new object. The minor units will be untouched in the result.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the current scale and 0.
     * For example, 'USD 23.45' plus '138' gives 'USD 161.45'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToAdd the monetary value to add, not null
     * @return BigMoney the new instance with the input amount added, never null
     */
    public function plusMajor($amountToAdd)
    {
        if (0 === $amountToAdd) {
            return $this;
        }

        $newAmount = $this->amount->plus(BigDecimal::of($amountToAdd));

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value with the amount in minor units added.
     * <p>
     * This adds the specified amount in minor units to this monetary amount,
     * returning a new object.
     * <p>
     * No precision is lost in the result.
     * The scale of the result will be the maximum of the current scale and the default currency scale.
     * For example, 'USD 23.45' plus '138' gives 'USD 24.83'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToAdd the monetary value to add, not null
     * @return BigMoney the new instance with the input amount added, never null
     */
    public function plusMinor($amountToAdd)
    {
        if (0 === $amountToAdd) {
            return $this;
        }

        $newAmount = $this->amount->plus(
            BigDecimal::ofUnscaledValue($amountToAdd, $this->currency->getDecimalPlaces())
        );

        return BigMoney::of($this->currency, $newAmount);
    }


    /**
     * Returns a copy of this monetary value with the amount added retaining
     * the scale by rounding the result.
     * <p>
     * The scale of the result will be the same as the scale of this instance.
     * For example,'USD 25.95' plus '3.021' gives 'USD 28.97' with most rounding modes.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|BigMoneyProviderInterface|string|float|int $amountToAdd the monetary value to add, not null
     * @param int $roundingMode the rounding mode to use to adjust the scale, not null
     * @return BigMoney the new instance with the input amount added, never null
     */
    public function plusRetainScale($amountToAdd, $roundingMode = RoundingMode::UNNECESSARY)
    {
        Utils::checkNotNull($amountToAdd, "Amount must not be null");
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        if ($amountToAdd instanceof BigMoneyProviderInterface) {
            $this->checkCurrencyEqual($amountToAdd);

            $amount = $amountToAdd->toBigMoney()->getAmount();
        } else {
            $amount = BigDecimal::of($amountToAdd);
        }

        if (0 === $amount->compareTo(BigDecimal::zero())) {
            return $this;
        }

        $newAmount = $this->amount->plus($amount);
        $newAmount = $newAmount->toScale($this->getScale(), $roundingMode);

        return BigMoney::of($this->currency, $newAmount);
    }


    /**
     * Returns a copy of this monetary value multiplied by the specified value.
     * <p>
     * No precision is lost in the result.
     * The result has a scale equal to the sum of the two scales.
     * For example, 'USD 1.13' multiplied by '2.5' gives 'USD 2.825'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|string|int|float $valueToMultiplyBy the scalar value to multiply by, not null
     * @return BigMoney the new multiplied instance, never null
     */
    public function multipliedBy($valueToMultiplyBy)
    {
        Utils::checkNotNull($valueToMultiplyBy, "Multiplier must not be null");
        $value = BigDecimal::of($valueToMultiplyBy);

        if (0 === $value->compareTo(BigDecimal::one())) {
            return $this;
        }

        $newAmount = $this->amount->multipliedBy($value);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value multiplied by the specified value
     * using the specified rounding mode to adjust the scale of the result.
     * <p>
     * This multiplies this money by the specified value, retaining the scale of this money.
     * This will frequently lose precision, hence the need for a rounding mode.
     * For example, 'USD 1.13' multiplied by '2.5' and rounding down gives 'USD 2.82'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|float|int $valueToMultiplyBy the scalar value to multiply by, not null
     * @param int $roundingMode the rounding mode to use to bring the decimal places back in line, not null
     * @return BigMoney the new multiplied instance, never null
     */
    public function multiplyRetainScale($valueToMultiplyBy, $roundingMode)
    {
        Utils::checkNotNull($valueToMultiplyBy, "Multiplier must not be null");
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        $value = BigDecimal::of($valueToMultiplyBy);
        if (0 === $value->compareTo(BigDecimal::one())) {
            return $this;
        }

        $newAmount = $this->amount->multipliedBy($value);
        $newAmount = $newAmount->toScale($this->getScale(), $roundingMode);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * Returns a copy of this monetary value divided by the specified value
     * using the specified rounding mode to adjust the scale.
     * <p>
     * The result has the same scale as this instance.
     * For example, 'USD 1.13' divided by '2.5' and rounding down gives 'USD 0.45'
     * (amount rounded down from 0.452).
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|string|int|float $valueToDivideBy the scalar value to divide by, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return BigMoney the new divided instance, never null
     */
    public function dividedBy($valueToDivideBy, $roundingMode = RoundingMode::UNNECESSARY)
    {
        Utils::checkNotNull($valueToDivideBy, "Divisor must not be null");
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        $value = BigDecimal::of($valueToDivideBy);

        if (0 === $value->compareTo(BigDecimal::one())) {
            return $this;
        }

        $newAmount = $this->amount->dividedBy($value, null, $roundingMode);

        return BigMoney::of($this->currency, $newAmount);
    }

    /**
     * @inheritdoc
     */
    public function toBigMoney()
    {
        return $this;
    }

    /**
     * Converts this money to an instance of {@code Money}.
     *
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the money instance, never null
     */
    public function toMoney($roundingMode = RoundingMode::UNNECESSARY)
    {
        return Money::of($this->currency, $this->amount, $roundingMode);
    }

    public function __toString()
    {
        return sprintf("%s %s", $this->currency->getCode(), $this->amount);
    }

    /**
     * Validates that the currency of this money and the specified money match.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, not null
     * @return BigMoney
     * @throws CurrencyMismatchException if the currencies differ
     */
    private function checkCurrencyEqual(BigMoneyProviderInterface $moneyProvider)
    {
        $money = self::ofProvider($moneyProvider);
        if ($this->isSameCurrency($money) == false) {
            throw new CurrencyMismatchException($this->getCurrency(), $money->getCurrency());
        }

        return $money;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(
            [
                'amount' => serialize($this->amount),
                'currency' => serialize($this->currency),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->amount = unserialize($data['amount']);
        $this->currency = unserialize($data['currency']);
    }


    /**
     * Compares this monetary value to another.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return int -1 if this is less than , 0 if equal, 1 if greater than
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function compareTo(BigMoneyProviderInterface $other)
    {
        $otherMoney = self::ofProvider($other);
        if ($this->currency != $otherMoney->currency) {
            throw new CurrencyMismatchException($this->getCurrency(), $otherMoney->getCurrency());
        }

        return $this->amount->compareTo($otherMoney->amount);
    }
}