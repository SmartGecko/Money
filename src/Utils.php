<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 13:58
 */

namespace SmartGecko\Money;


final class Utils
{
    private function __construct()
    {

    }

    /**
     * Validates that the object specified is not null.
     *
     * @param mixed $object the object to check, not null
     * @param string $message the message to be displayed on error
     * @throws \InvalidArgumentException if the input value is null
     */
    static function checkNotNull($object, $message)
    {
        if (null === $object) {
            throw new \InvalidArgumentException($message);
        }
    }


    /**
     * Checks if the monetary value is zero, treating null as zero.
     * <p>
     * This method accepts any implementation of {@code BigMoneyProvider}.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, null returns zero
     * @return bool true if the money is null or zero
     */
    public static function isZero(BigMoneyProviderInterface $moneyProvider)
    {
//return (moneyProvider == null || moneyProvider.toBigMoney().isZero());
    }

    /**
     * Checks if the monetary value is positive and non-zero, treating null as zero.
     * <p>
     * This method accepts any implementation of {@code BigMoneyProvider}.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, null returns false
     * @return bool true if the money is non-null and positive
     */
    public static function isPositive(BigMoneyProviderInterface $moneyProvider)
    {
        //return (moneyProvider != null && moneyProvider.toBigMoney().isPositive());
    }

    /**
     * Checks if the monetary value is positive or zero, treating null as zero.
     * <p>
     * This method accepts any implementation of {@code BigMoneyProvider}.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, null returns true
     * @return bool true if the money is null, zero or positive
     */
    public static function isPositiveOrZero(BigMoneyProviderInterface $moneyProvider)
    {
        //return (moneyProvider == null || moneyProvider.toBigMoney().isPositiveOrZero());
    }

    /**
     * Checks if the monetary value is negative and non-zero, treating null as zero.
     * <p>
     * This method accepts any implementation of {@code BigMoneyProvider}.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, null returns false
     * @return bool true if the money is non-null and negative
     */
    public static function isNegative(BigMoneyProviderInterface $moneyProvider)
    {
        //return (moneyProvider != null && moneyProvider.toBigMoney().isNegative());
    }

    /**
     * Checks if the monetary value is negative or zero, treating null as zero.
     * <p>
     * This method accepts any implementation of {@code BigMoneyProvider}.
     *
     * @param BigMoneyProviderInterface $moneyProvider the money to check, null returns true
     * @return bool true if the money is null, zero or negative
     */
    public static function isNegativeOrZero(BigMoneyProviderInterface $moneyProvider)
    {
        //return (moneyProvider == null || moneyProvider.toBigMoney().isNegativeOrZero());
    }


    /**
     * Finds the maximum {@code Money} value, handing null.
     * <p>
     * This returns the greater of money1 or money2 where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param Money $money1 the first money instance, null returns money2
     * @param Money $money2 the first money instance, null returns money1
     * @return Money the maximum value, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    public static function max(Money $money1, Money $money2)
    {
        /*if (money1 == null) {
            return money2;
        }
        if (money2 == null) {
            return money1;
        }

        return money1.compareTo(money2) > 0 ? money1 : money2;*/
    }

    /**
     * Finds the minimum {@code Money} value, handing null.
     * <p>
     * This returns the greater of money1 or money2 where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param Money $money1 the first money instance, null returns money2
     * @param Money $money2 the first money instance, null returns money1
     * @return Money the minimum value, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    public static function min(Money $money1, Money $money2)
    {
        /*if (money1 == null) {
            return money2;
        }
        if (money2 == null) {
            return money1;
        }

        return money1.compareTo(money2) < 0 ? money1 : money2;*/
    }


    /**
     * Adds two {@code Money} objects, handling null.
     * <p>
     * This returns {@code money1 + money2} where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param Money $money1 the first money instance, null returns money2
     * @param Money $money2 the first money instance, null returns money1
     * @return Money the total, where null is ignored, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    public static function add(Money $money1, Money $money2)
    {
        /*if (money1 == null) {
            return money2;
        }
        if (money2 == null) {
            return money1;
        }

        return money1.plus(money2);*/
    }


    /**
     * Subtracts the second {@code Money} from the first, handling null.
     * <p>
     * This returns {@code money1 - money2} where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param Money $money1 the first money instance, null treated as zero
     * @param Money $money2 the first money instance, null returns money1
     * @return Money the total, where null is ignored, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    public static function subtract(Money $money1, Money $money2)
    {
        /*if (money2 == null) {
            return money1;
        }
        if (money1 == null) {
            return money2.negated();
        }

        return money1.minus(money2);*/
    }

    /**
     * Finds the maximum {@code BigMoney} value, handing null.
     * <p>
     * This returns the greater of money1 or money2 where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param BigMoney $money1 the first money instance, null returns money2
     * @param BigMoney $money2 the first money instance, null returns money1
     * @return BigMoney the maximum value, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    //public static function max(BigMoney $money1, BigMoney $money2)
//{
    /*if (money1 == null) {
        return money2;
    }
    if (money2 == null) {
        return money1;
    }

    return money1.compareTo(money2) > 0 ? money1 : money2;*/
//}

    /**
     * Finds the minimum {@code BigMoney} value, handing null.
     * <p>
     * This returns the greater of money1 or money2 where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param BigMoney $money1 the first money instance, null returns money2
     * @param BigMoney $money2 the first money instance, null returns money1
     * @return BigMoney the minimum value, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    //  public static function min(BigMoney $money1, BigMoney $money2)
//{
    /*if (money1 == null) {
        return money2;
    }
    if (money2 == null) {
        return money1;
    }

    return money1.compareTo(money2) < 0 ? money1 : money2;*/
//}


    /**
     * Adds two {@code BigMoney} objects, handling null.
     * <p>
     * This returns {@code money1 + money2} where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param BigMoney $money1 the first money instance, null returns money2
     * @param BigMoney $money2 the first money instance, null returns money1
     * @return BigMoney the total, where null is ignored, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    //public static function add(BigMoney $money1, BigMoney $money2)
//{
    /* if (money1 == null) {
         return money2;
     }
     if (money2 == null) {
         return money1;
     }

     return money1.plus(money2);*/
//}

    /**
     * Subtracts the second {@code BigMoney} from the first, handling null.
     * <p>
     * This returns {@code money1 - money2} where null is ignored.
     * If both input values are null, then null is returned.
     *
     * @param BigMoney $money1 the first money instance, null treated as zero
     * @param BigMoney $money2 the first money instance, null returns money1
     * @return the total, where null is ignored, null if both inputs are null
     * @throws CurrencyMismatchException if the currencies differ
     */
    //public static function subtract(BigMoney $money1, BigMoney $money2)
//{
    /*if (money2 == null) {
        return money1;
    }
    if (money1 == null) {
        return money2.negated();
    }

    return money1.minus(money2);*/
//}

}