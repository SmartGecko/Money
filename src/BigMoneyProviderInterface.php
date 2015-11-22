<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 14:03
 */

namespace SmartGecko\Money;

/**
 * Provides a uniform interface to obtain a {@code BigMoney}.
 * <p>
 * This interface provides an abstraction over {@link Money} and {@link BigMoney}.
 * In general, applications should use the concrete types, not this interface.
 * However, utilities and frameworks may choose to make use of this abstraction.
 * <p>
 * Implementations of {@code BigMoneyProvider} may be mutable.
 * To minimise the risk of the value of the provider changing while processing,
 * any method that takes a {@code BigMoneyProvider} as a parameter should convert
 * it to a {@code BigMoney} immediately and use that directly from then on.
 * The method {@link BigMoney#of(BigMoneyProvider)} performs the conversion
 * safely with null checks and is recommended for this purpose.
 * <p>
 * This interface makes no guarantees about the immutability or
 * thread-safety of implementations.
 */
interface BigMoneyProviderInterface
{
    /**
     * Returns a {@code BigMoney} instance equivalent to the value of this object.
     * <p>
     * It is recommended that {@link BigMoney#of(BigMoneyProvider)} is used in
     * preference to calling this method directly. It is also recommended that the
     * converted {@code BigMoney} is cached in a local variable instead of
     * performing the conversion multiple times.
     *
     * @return BigMoney the converted money instance, never null
     * @throws \RuntimeException if conversion is not possible
     */
    public function toBigMoney();
}