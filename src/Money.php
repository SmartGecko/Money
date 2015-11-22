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

final class Money implements BigMoneyProviderInterface, \Serializable
{

    /**
     * @var BigMoney
     */
    private $money;

    /**
     * Constructor, creating a new monetary instance.
     *
     * @param BigMoney $money the underlying money, not null
     */
    private function __construct(BigMoney $money)
    {
        Utils::checkNotNull($money, "Money bug: BigMoney must not be null");

        if (!$money->isCurrencyScale()) {
            throw new \InvalidArgumentException("Money bug: Only currency scale is valid for Money");
        }

        $this->money = $money;
    }

    /**
     * Obtains an instance of {@code Money} from a {@code BigDecimal}, rounding as necessary.
     * <p>
     * This allows you to create an instance with a specific currency and amount.
     * If the amount has a scale in excess of the scale of the currency then the excess
     * fractional digits are rounded using the rounding mode.
     *
     * @param Currency $currency the currency, not null
     * @param BigDecimal|string|int|float|string $amount the amount of money, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new instance, never null
     */
    public static function of(Currency $currency, $amount, $roundingMode = RoundingMode::UNNECESSARY)
    {
        Utils::checkNotNull($currency, "CurrencyUnit must not be null");
        Utils::checkNotNull($amount, "Amount must not be null");
        Utils::checkNotNull($roundingMode, "RoundingMode must not be null");

        $amount = BigDecimal::of($amount)->toScale($currency->getDecimalPlaces(), $roundingMode);

        return new Money(BigMoney::of($currency, $amount));
    }

    /**
     * Obtains an instance of {@code Money} as the total value of an array.
     * <p>
     * The array must contain at least one monetary value.
     * Subsequent amounts are added as though using {@link #plus(Money)}.
     * All amounts must be in the same currency.
     *
     * @param Money $monies the monetary values to total, not empty, no null elements, not null
     * @return Money the total, never null
     * @throws \InvalidArgumentException if the array is empty
     * @throws CurrencyMismatchException if the currencies differ
     */
    public static function total(Money...$monies)
    {
        Utils::checkNotNull($monies, "Money array must not be null");

        if (0 === count($monies)) {
            throw new \InvalidArgumentException("Money array must not be empty");
        }

        $total = $monies[0];

        for ($i = 1; $i < count($monies); $i++) {
            $total = $total->plus($monies[$i]);
        }

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function toBigMoney()
    {
        return $this->money;
    }

    /**
     * Obtains an instance of {@code Money} from a provider, rounding as necessary.
     * <p>
     * This allows you to create an instance from any class that implements the
     * provider, such as {@code BigMoney}.
     * The rounding mode is used to adjust the scale to the scale of the currency.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to convert, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new instance, never null
     */
    public static function ofProvider(
        BigMoneyProviderInterface $moneyProvider,
        $roundingMode = RoundingMode::UNNECESSARY
    ) {
        return new Money(BigMoney::ofProvider($moneyProvider)->withCurrencyScale($roundingMode));
    }

    /**
     * Parses an instance of {@code Money} from a string.
     * <p>
     * The string format is '$currencyCode $amount' where there may be
     * zero to many spaces between the two parts.
     * The currency code must be a valid three letter currency.
     * The amount must match the regular expression {@code [+-]?[0-9]*[.]?[0-9]*}.
     * The spaces and numbers must be ASCII characters.
     * This matches the output from {@link #toString()}.
     * <p>
     * For example, {@code parse("USD 25")} creates the instance {@code USD 25.00}
     * while {@code parse("USD 25.95")} creates the instance {@code USD 25.95}.
     *
     * @param string $moneyStr the money string to parse, not null
     * @return Money the parsed instance, never null
     */

    public static function parse($moneyStr)
    {
        return Money::ofProvider(BigMoney::parse($moneyStr));
    }


    /**
     * Returns a copy of this monetary value with the amount added.
     * <p>
     * This adds the specified amount to this monetary amount, returning a new object.
     * If the amount to add exceeds the scale of the currency, then the
     * rounding mode will be used to adjust the result.
     * <p>
     * The amount is converted via {@link BigDecimal#valueOf(double)} which yields
     * the most expected answer for most programming scenarios.
     * Any {@code double} literal in code will be converted to
     * exactly the same BigDecimal with the same scale.
     * For example, the literal '1.45d' will be converted to '1.45'.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param Money|string|float|int $amountToAdd the monetary value to add, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new instance with the input amount added, never null
     */
    public function plus($amountToAdd, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->plusRetainScale($amountToAdd, $roundingMode));
    }

    /**
     * Returns a copy of this monetary value with the amount in major units added.
     * <p>
     * This adds an amount in major units, leaving the minor units untouched.
     * For example, USD 23.45 plus 138 gives USD 161.45.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToAdd the monetary value to add, not null
     * @return Money the new instance with the input amount added, never null
     */
    public function plusMajor($amountToAdd)
    {
        return $this->with($this->money->plusMajor($amountToAdd));
    }

    /**
     * Returns a copy of this monetary value with the amount in minor units added.
     * <p>
     * This adds an amount in minor units.
     * For example, USD 23.45 plus 138 gives USD 24.83.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToAdd the monetary value to add, not null
     * @return Money the new instance with the input amount added, never null
     */
    public function plusMinor($amountToAdd)
    {
        return $this->with($this->money->plusMinor($amountToAdd));
    }

    /**
     * Returns a copy of this monetary value rounded to the specified scale without
     * changing the current scale.
     * <p>
     * Scale has the same meaning as in {@link BigDecimal}.
     * A scale of 2 means round to 2 decimal places.
     * <ul>
     * <li>Rounding 'EUR 45.23' to a scale of -1 returns 40.00 or 50.00 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 0 returns 45.00 or 46.00 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 1 returns 45.20 or 45.30 depending on the rounding mode.
     * <li>Rounding 'EUR 45.23' to a scale of 2 has no effect (it already has that scale).
     * <li>Rounding 'EUR 45.23' to a scale of 3 has no effect (the scale is not increased).
     * </ul>
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $scale the new scale
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new instance with the amount converted to be positive, never null
     */
    public function rounded($scale, $roundingMode)
    {
        return $this->with($this->money->rounded($scale, $roundingMode));
    }

    /**
     * Returns a copy of this monetary value with the amount subtracted.
     * <p>
     * This subtracts the specified amount from this monetary amount, returning a new object.
     * If the amount to subtract exceeds the scale of the currency, then the
     * rounding mode will be used to adjust the result.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param Money|string|float|int $amountToSubtract the monetary value to subtract, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new instance with the input amount subtracted, never null
     */
    public function minus($amountToSubtract, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->minusRetainScale($amountToSubtract, $roundingMode));
    }

    /**
     * Returns a copy of this monetary value with the amount in major units subtracted.
     * <p>
     * This subtracts an amount in major units, leaving the minor units untouched.
     * For example, USD 23.45 minus 138 gives USD -114.55.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToSubtract the monetary value to subtract, not null
     * @return Money the new instance with the input amount subtracted, never null
     */
    public function minusMajor($amountToSubtract)
    {
        return $this->with($this->money->minusMajor($amountToSubtract));
    }

    /**
     * Returns a copy of this monetary value with the amount in minor units subtracted.
     * <p>
     * This subtracts an amount in minor units.
     * For example, USD 23.45 minus 138 gives USD 22.07.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param int $amountToSubtract the monetary value to subtract, not null
     * @return Money the new instance with the input amount subtracted, never null
     */
    public function minusMinor($amountToSubtract)
    {
        return $this->with($this->money->minusMinor($amountToSubtract));
    }

    /**
     * Returns a copy of this monetary value multiplied by the specified value.
     * <p>
     * This takes this amount and multiplies it by the specified value, rounding
     * the result is rounded as specified.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|int|float $valueToMultiplyBy the scalar value to multiply by, not null
     * @param int $roundingMode the rounding mode to use to bring the decimal places back in line, not null
     * @return Money the new multiplied instance, never null
     */
    public function multipliedBy($valueToMultiplyBy, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->multiplyRetainScale($valueToMultiplyBy, $roundingMode));
    }

    /**
     * Returns a copy of this monetary value divided by the specified value.
     * <p>
     * This takes this amount and divides it by the specified value, rounding
     * the result is rounded as specified.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|int|float $valueToDivideBy the scalar value to divide by, not null
     * @param int $roundingMode the rounding mode to use, not null
     * @return Money the new divided instance, never null
     */
    public function dividedBy($valueToDivideBy, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->dividedBy($valueToDivideBy, $roundingMode));
    }

    /**
     * Gets the amount.
     * <p>
     * This returns the value of the money as a {@code BigDecimal}.
     * The scale will be the scale of this money.
     *
     * @return BigDecimal the amount, never null
     */
    public function getAmount()
    {
        return $this->money->getAmount();
    }

    /**
     * Gets the currency.
     *
     * @return Currency the currency, never null
     */
    public function getCurrency()
    {
        return $this->money->getCurrency();
    }

    /**
     * Gets the scale of the {@code BigDecimal} amount.
     * <p>
     * The scale has the same meaning as in {@link BigDecimal}.
     * Positive values represent the number of decimal places in use.
     * For example, a scale of 2 means that the money will have two decimal places
     * such as 'USD 43.25'.
     * <p>
     * For {@code Money}, the scale is fixed and always matches that of the currency.
     *
     * @return int the scale in use, typically 2 but could be 0, 1 and 3
     */
    public function getScale()
    {
        return $this->money->getScale();
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
        return $this->money->getAmountMinor();
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
        return $this->money->getAmountMinorInt();
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
        return $this->money->getAmountMajor();
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
        return $this->money->getAmountMajorInt();
    }

    /**
     * Checks if the amount is zero.
     *
     * @return bool true if the amount is zero
     */
    public function isZero()
    {
        return $this->money->isZero();
    }

    /**
     * Checks if the amount is greater than zero.
     *
     * @return bool true if the amount is greater than zero
     */
    public function isPositive()
    {
        return $this->money->isPositive();
    }

    /**
     * Checks if the amount is zero or greater.
     *
     * @return bool true if the amount is zero or greater
     */
    public function isPositiveOrZero()
    {
        return $this->money->isPositiveOrZero();
    }

    /**
     * Checks if the amount is less than zero.
     *
     * @return bool true if the amount is less than zero
     */
    public function isNegative()
    {
        return $this->money->isNegative();
    }

    /**
     * Checks if the amount is zero or less.
     *
     * @return bool true if the amount is zero or less
     */
    public function isNegativeOrZero()
    {
        return $this->money->isNegativeOrZero();
    }

    /**
     * Checks if this instance and the specified instance have the same currency.
     *
     * @param BigMoneyProviderInterface $other the money to check, not null
     * @return bool true if they have the same currency
     */
    public function isSameCurrency(BigMoneyProviderInterface $other)
    {
        return $this->money->isSameCurrency($other);
    }

    /**
     * Compares this monetary value to another.
     * <p>
     * This allows {@code Money} to be compared to any {@code BigMoneyProvider}.
     * Scale is ignored in the comparison.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return int -1 if this is less than , 0 if equal, 1 if greater than
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function compareTo(BigMoneyProviderInterface $other)
    {
        return $this->money->compareTo($other);
    }

    /**
     * Checks if this monetary value is equal to another.
     * <p>
     * This allows {@code Money} to be compared to any {@code BigMoneyProvider}.
     * Scale is ignored, so 'USD 30.00' and 'USD 30' are equal.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is greater than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     * @see #equals(Object)
     */
    public function isEqual(BigMoneyProviderInterface $other)
    {
        return $this->money->isEqual($other);
    }

    /**
     * Checks if this monetary value is greater than another.
     * <p>
     * This allows {@code Money} to be compared to any {@code BigMoneyProvider}.
     * Scale is ignored in the comparison.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is greater than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function isGreaterThan(BigMoneyProviderInterface $other)
    {
        return $this->money->isGreaterThan($other);
    }

    /**
     * Checks if this monetary value is less than another.
     * <p>
     * This allows {@code Money} to be compared to any {@code BigMoneyProvider}.
     * Scale is ignored in the comparison.
     * The compared values must be in the same currency.
     *
     * @param BigMoneyProviderInterface $other the other monetary value, not null
     * @return bool true is this is less than the specified monetary value
     * @throws CurrencyMismatchException if the currencies differ
     */
    public function isLessThan(BigMoneyProviderInterface $other)
    {
        return $this->money->isLessThan($other);
    }

    /**
     * Obtains an instance of {@code Money} representing zero.
     * <p>
     * For example, {@code zero(USD)} creates the instance {@code USD 0.00}.
     *
     * @param Currency $currency the currency, not null
     * @return Money the instance representing zero, never null
     */
    public static function zero(Currency $currency)
    {
        Utils::checkNotNull($currency, "Currency must not be null");
        $bd = BigDecimal::ofUnscaledValue(0, $currency->getDecimalPlaces());

        return new Money(BigMoney::of($currency, $bd));
    }

    /**
     * Obtains an instance of {@code Money} from an amount in major units.
     * <p>
     * This allows you to create an instance with a specific currency and amount.
     * The amount is a whole number only. Thus you can initialise the value
     * 'USD 20', but not the value 'USD 20.32'.
     * For example, {@code ofMajor(USD, 25)} creates the instance {@code USD 25.00}.
     *
     * @param Currency $currency the currency, not null
     * @param int $amountMajor the amount of money in the major division of the currency
     * @return Money the new instance, never null
     */
    public static function ofMajor(Currency $currency, $amountMajor)
    {
        return Money::of($currency, BigDecimal::of($amountMajor), RoundingMode::UNNECESSARY);
    }

    /**
     * Obtains an instance of {@code Money} from an amount in minor units.
     * <p>
     * This allows you to create an instance with a specific currency and amount
     * expressed in terms of the minor unit.
     * For example, if constructing US Dollars, the input to this method represents cents.
     * Note that when a currency has zero decimal places, the major and minor units are the same.
     * For example, {@code ofMinor(USD, 2595)} creates the instance {@code USD 25.95}.
     *
     * @param Currency $currency the currency, not null
     * @param int $amountMinor the amount of money in the minor division of the currency
     * @return Money the new instance, never null
     */
    public static function ofMinor(Currency $currency, $amountMinor)
    {
        return new Money(BigMoney::ofMinor($currency, $amountMinor));
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
        return $this->money->getMinorPart();
    }

    /**
     * Returns a copy of this monetary value with the amount negated.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @return Money the new instance with the amount negated, never null
     */
    public function negated()
    {
        return $this->with($this->money->negated());
    }

    /**
     * Returns a copy of this monetary value with a positive amount.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @return Money the new instance with the amount converted to be positive, never null
     */
    public function abs()
    {
        return ($this->isNegative() ? $this->negated() : $this);
    }


    /**
     * Returns a copy of this monetary value with the specified currency.
     * <p>
     * The returned instance will have the specified currency and the amount
     * from this instance. If the number of decimal places differs between the
     * currencies, then the amount may be rounded.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param Currency $currency the currency to use, not null
     * @param int $roundingMode the rounding mode to use to bring the decimal places back in line, not null
     * @return Money the new instance with the input currency set, never null
     */
    public function withCurrency(Currency $currency, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->withCurrency($currency)->withCurrencyScale($roundingMode));
    }

    /**
     * Returns a new {@code Money}, returning {@code this} if possible.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigMoney $newInstance the new money to use, not null
     * @return Money the new instance, never null
     */
    private function with(BigMoney $newInstance)
    {
        if ($this->money == $newInstance) {
            return $this;
        }

        return new Money($newInstance);
    }

    /**
     * Returns a copy of this monetary value with the specified amount.
     * <p>
     * The returned instance will have this currency and the new amount.
     * If the scale of the {@code BigDecimal} needs to be adjusted, then
     * it will be rounded using the specified mode.
     * <p>
     * This instance is immutable and unaffected by this method.
     *
     * @param BigDecimal|string|int|float $amount the monetary amount to set in the returned instance, not null
     * @param int $roundingMode the rounding mode to adjust the scale, not null
     * @return Money the new instance with the input amount set, never null
     */
    public function withAmount($amount, $roundingMode = RoundingMode::UNNECESSARY)
    {
        return $this->with($this->money->withAmount($amount)->withCurrencyScale($roundingMode));
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->money->__toString();
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->money);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->money = unserialize($serialized);
    }

    /**
     * Checks if this monetary value equals another.
     * <p>
     * The comparison takes into account the scale.
     * The compared values must be in the same currency.
     *
     * @param mixed $other the other object to compare to, not null
     * @return bool true if this instance equals the other instance
     */
    public function equals($other)
    {
        if ($this === $other) {
            return true;
        }

        if ($other instanceof Money) {
            return $this->money->equals($other->money);
        }

        return false;
    }
}