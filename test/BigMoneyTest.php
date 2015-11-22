<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 16/11/15
 * Time: 23:20
 */

namespace SmartGecko\Money\Test;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use SmartGecko\Money\Money;
use SmartGecko\Money\BigMoney;
use SmartGecko\Money\CsvCurrencyDataProvider;
use SmartGecko\Money\Currency;

class BigMoneyTest extends \PHPUnit_Framework_TestCase
{
    const BIGDEC_2_34 = "2.34";
    const BIGDEC_2_345 = "2.345";
    const BIGDEC_M5_78 = "-5.78";

    private static $GBP;
    private static $EUR;
    private static $USD;
    private static $JPY;

    /** @var  BigMoney */
    private static $GBP_0_00;
    /** @var  BigMoney */
    private static $GBP_1_23;
    /** @var  BigMoney */
    private static $GBP_2_33;
    /** @var  BigMoney */
    private static $GBP_2_34;
    /** @var  BigMoney */
    private static $GBP_2_35;
    /** @var  BigMoney */
    private static $GBP_2_36;
    /** @var  BigMoney */
    private static $GBP_5_78;
    /** @var  BigMoney */
    private static $GBP_M1_23;
    /** @var  BigMoney */
    private static $GBP_M5_78;
    /** @var  BigMoney */
    private static $JPY_423;
    /** @var  BigMoney */
    private static $USD_1_23;
    /** @var  BigMoney */
    private static $USD_2_34;
    /** @var  BigMoney */
    private static $USD_2_35;

    public static function setUpBeforeClass()
    {
        (new CsvCurrencyDataProvider())->registerCurrencies();

        self::$GBP = Currency::of('GBP');
        self::$EUR = Currency::of('EUR');
        self::$USD = Currency::of('USD');
        self::$JPY = Currency::of('JPY');

        self::$GBP_0_00 = BigMoney::parse("GBP 0.00");
        self::$GBP_1_23 = BigMoney::parse("GBP 1.23");
        self::$GBP_2_33 = BigMoney::parse("GBP 2.33");
        self::$GBP_2_34 = BigMoney::parse("GBP 2.34");
        self::$GBP_2_35 = BigMoney::parse("GBP 2.35");
        self::$GBP_2_36 = BigMoney::parse("GBP 2.36");
        self::$GBP_5_78 = BigMoney::parse("GBP 5.78");
        self::$GBP_M1_23 = BigMoney::parse("GBP -1.23");
        self::$GBP_M5_78 = BigMoney::parse("GBP -5.78");
        self::$JPY_423 = BigMoney::parse("JPY 423");
        self::$USD_1_23 = BigMoney::parse("USD 1.23");
        self::$USD_2_34 = BigMoney::parse("USD 2.34");
        self::$USD_2_35 = BigMoney::parse("USD 2.35");
    }

    /*
    private static final BigMoney GBP_INT_MAX_PLUS1 = BigMoney.ofMinor(GBP, ((long) Integer.MAX_VALUE) + 1);
    private static final BigMoney GBP_INT_MIN_MINUS1 = BigMoney.ofMinor(GBP, ((long) Integer.MIN_VALUE) - 1);
    private static final BigMoney GBP_INT_MAX_MAJOR_PLUS1 = BigMoney.ofMinor(GBP, (((long) Integer.MAX_VALUE) + 1) * 100);
    private static final BigMoney GBP_INT_MIN_MAJOR_MINUS1 = BigMoney.ofMinor(GBP, (((long) Integer.MIN_VALUE) - 1) * 100);
    private static final BigMoney GBP_LONG_MAX_PLUS1 = BigMoney.of(GBP, BigDecimal.valueOf(Long.MAX_VALUE).add(BigDecimal.ONE));
    private static final BigMoney GBP_LONG_MIN_MINUS1 = BigMoney.of(GBP, BigDecimal.valueOf(Long.MIN_VALUE).subtract(BigDecimal.ONE));
    private static final BigMoney GBP_LONG_MAX_MAJOR_PLUS1 = BigMoney.of(GBP,
    BigDecimal.valueOf(Long.MAX_VALUE).add(BigDecimal.ONE).multiply(BigDecimal.valueOf(100)));
    private static final BigMoney GBP_LONG_MIN_MAJOR_MINUS1 = BigMoney.of(GBP,
    BigDecimal.valueOf(Long.MIN_VALUE).subtract(BigDecimal.ONE).multiply(BigDecimal.valueOf(100)));

    private static final BigMoney USD_1_23 = BigMoney.parse("USD 1.23");
    ;*/

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseStringTooShort()
    {
        BigMoney::parse("GBP");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParse_String_badCurrency()
    {
        BigMoney::parse("GBX 2.34");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseNullString()
    {
        BigMoney::parse(null);
    }

    /*

private static final BigMoneyProvider BAD_PROVIDER = new BigMoneyProvider() {
@Override
public BigMoney toBigMoney() {
return null;  // shouldn't return null
}
};
*/

    public function test_factory_of_Currency_BigDecimal()
    {
        $test = BigMoney::of(self::$GBP, self::BIGDEC_2_345);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), self::BIGDEC_2_345);
        $this->assertEquals($test->getScale(), 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_BigDecimal_nullBigDecimal()
    {
        BigMoney::of(self::$GBP, null);
    }

    public function test_factory_of_Currency_double()
    {
        $test = BigMoney::of(self::$GBP, 2.345);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), self::BIGDEC_2_345);
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_factory_of_Currency_double_trailingZero1()
    {
        $test = BigMoney::of(self::$GBP, 1.230);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "1.23");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_trailingZero2()
    {
        $test = BigMoney::of(self::$GBP, 1.20);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "1.2");
        $this->assertEquals($test->getScale(), 1);
    }

    public function test_factory_of_Currency_double_medium()
    {
        $test = BigMoney::of(self::$GBP, (float)2000);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "2000");
        $this->assertEquals($test->getScale(), 0);
    }

    public function test_factory_of_Currency_double_big()
    {
        $test = BigMoney::of(self::$GBP, (float)200000000);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "200000000");
        $this->assertEquals($test->getScale(), 0);
    }

    /*
            public void test_factory_ofScale_Currency_BigDecimal_negativeScale() {
        BigMoney test = BigMoney.ofScale(GBP, BigDecimal.valueOf(23400), -2);
                assertEquals(test.getCurrencyUnit(), GBP);
                assertEquals(test.getAmount(), BigDecimal.valueOf(23400L, 0));
            }

            @Test(expectedExceptions = ArithmeticException.class)
            public void test_factory_ofScale_Currency_BigDecimal_invalidScale() {
        BigMoney.ofScale(GBP, BIGDEC_2_345, 2);
            }

            @Test(expectedExceptions = NullPointerException.class)
            public void test_factory_ofScale_Currency_BigDecimal_nullCurrency() {
        BigMoney.ofScale((CurrencyUnit) null, BIGDEC_2_34, 2);
            }

            @Test(expectedExceptions = NullPointerException.class)
            public void test_factory_ofScale_Currency_BigDecimal_nullBigDecimal() {
        BigMoney.ofScale(GBP, (BigDecimal) null, 2);
            }*/


    /* TODO
    public function test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_DOWN()
    {
        $test = BigMoney::ofScale(self::$GBP, self::BIGDEC_2_34, 1, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(23, 1));
    }

    public function test_factory_ofScale_Currency_BigDecimal_int_JPY_RoundingMode_UP()
    {
        $test = BigMoney::ofScale(self::$JPY, self::BIGDEC_2_34, 0, RoundingMode::UP);
        $this->assertEquals($test->getCurrency(), self::$JPY);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(3, 0));
    }

    public function test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_negativeScale()
    {
        $test = BigMoney::ofScale(self::$GBP, BigDecimal::of(23400), -2, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(23400, 0));
    }*/

    /*
                @Test(expectedExceptions = ArithmeticException.class)
                public void test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_UNNECESSARY() {
            BigMoney.ofScale(JPY, BIGDEC_2_34, 1, RoundingMode.UNNECESSARY);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_nullCurrency() {
            BigMoney.ofScale((CurrencyUnit) null, BIGDEC_2_34, 2, RoundingMode.DOWN);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_nullBigDecimal() {
            BigMoney.ofScale(GBP, (BigDecimal) null, 2, RoundingMode.DOWN);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_ofScale_Currency_BigDecimal_int_RoundingMode_nullRoundingMode() {
            BigMoney.ofScale(GBP, BIGDEC_2_34, 2, (RoundingMode) null);
                }

                //-----------------------------------------------------------------------
                // ofScale(Currency,long, int)
                //-----------------------------------------------------------------------
                public void test_factory_ofScale_Currency_long_int() {
            BigMoney test = BigMoney.ofScale(GBP, 234, 4);
                    assertEquals(test.getCurrencyUnit(), GBP);
                    assertEquals(test.getAmount(), BigDecimal.valueOf(234, 4));
                }

                public void test_factory_ofScale_Currency_long_int_negativeScale() {
            BigMoney test = BigMoney.ofScale(GBP, 234, -4);
                    assertEquals(test.getCurrencyUnit(), GBP);
                    assertEquals(test.getAmount(), BigDecimal.valueOf(2340000, 0));
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_ofScale_Currency_long_int_nullCurrency() {
            BigMoney.ofScale((CurrencyUnit) null, 234, 2);
                }*/


    public function test_factory_ofMajor_Currency_long()
    {
        $test = BigMoney::ofMajor(self::$GBP, 234);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "234");
        $this->assertEquals($test->getScale(), 0);
    }

    public function test_factory_ofMinor_Currency_long()
    {
        $test = BigMoney::ofMinor(self::$GBP, 234);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "2.34");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_zero_Currency()
    {
        $test = BigMoney::zero(self::$GBP);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "0");
        $this->assertEquals($test->getScale(), 0);
    }


    public function test_factory_from_BigMoneyProvider()
    {
        $test = BigMoney::ofProvider(BigMoney::parse("GBP 104.23"));
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), "104.23");
        $this->assertEquals($test->getScale(), 2);
    }

    /*
@Test(expectedExceptions = NullPointerException.class)
public void test_factory_from_BigMoneyProvider_badProvider() {
BigMoney.of(BAD_PROVIDER);
}*/


    /*
     * total()
     */
    public function test_factory_total_varargs_1BigMoney()
    {
        $test = BigMoney::total(self::$GBP_1_23);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 123);
    }

    public function test_factory_total_array_1BigMoney()
    {
        $test = BigMoney::total(...[self::$GBP_1_23]);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 123);
    }

    public function test_factory_total_varargs_3Mixed()
    {
        $test = BigMoney::total(self::$GBP_1_23, self::$GBP_2_33->toMoney(), self::$GBP_2_36);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 592);
    }

    public function test_factory_total_array_3Mixed()
    {
        $test = BigMoney::total(...[self::$GBP_1_23, self::$GBP_2_33->toMoney(), self::$GBP_2_36]);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 592);
    }

    public function test_factory_total_array_3Money()
    {
        $test = BigMoney::total(...[self::$GBP_1_23->toMoney(), self::$GBP_2_33->toMoney(), self::$GBP_2_36->toMoney()]);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 592);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_total_varargs_empty()
    {
        BigMoney::total();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_total_array_empty()
    {
        BigMoney::total(...[]);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_factory_total_varargs_currenciesDiffer()
    {
        BigMoney::total(self::$GBP_2_33, self::$JPY_423);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_factory_total_array_currenciesDiffer()
    {
        BigMoney::total(...[self::$GBP_2_33, self::$JPY_423]);
    }

    /*
        //-----------------------------------------------------------------------
        // total(CurrencyUnit,BigMoneyProvider...)
        //-----------------------------------------------------------------------
        public void test_factory_total_CurrencyUnitVarargs_1() {
        BigMoney test = BigMoney.total(GBP, GBP_1_23);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 123);
        }

        public void test_factory_total_CurrencyUnitArray_1() {
        BigMoney[] array = new BigMoney[] {GBP_1_23};
            BigMoney test = BigMoney.total(GBP, array);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 123);
        }

        public void test_factory_total_CurrencyUnitVarargs_3() {
        BigMoney test = BigMoney.total(GBP, GBP_1_23, GBP_2_33, GBP_2_36);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 592);
        }

        public void test_factory_total_CurrencyUnitArray_3() {
        BigMoney[] array = new BigMoney[] {GBP_1_23, GBP_2_33, GBP_2_36};
            BigMoney test = BigMoney.total(GBP, array);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 592);
        }

        public void test_factory_total_CurrencyUnitVarargs_3Mixed() {
        BigMoney test = BigMoney.total(GBP, GBP_1_23, GBP_2_33.toMoney(), GBP_2_36);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 592);
        }

        public void test_factory_total_CurrencyUnitArray_3Mixed() {
        BigMoneyProvider[] array = new BigMoneyProvider[] {GBP_1_23, GBP_2_33.toMoney(), GBP_2_36};
            BigMoney test = BigMoney.total(GBP, array);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 592);
        }

        public void test_factory_total_CurrencyUnitArray_3Money() {
        Money[] array = new Money[] {GBP_1_23.toMoney(), GBP_2_33.toMoney(), GBP_2_36.toMoney()};
            BigMoney test = BigMoney.total(GBP, array);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 592);
        }

        public void test_factory_total_CurrencyUnitVarargs_empty() {
        BigMoney test = BigMoney.total(GBP);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 0);
        }

        public void test_factory_total_CurrencyUnitArray_empty() {
        BigMoney[] array = new BigMoney[0];
            BigMoney test = BigMoney.total(GBP, array);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 0);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitVarargs_currenciesDiffer() {
        BigMoney.total(GBP, JPY_423);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitArray_currenciesDiffer() {
        BigMoney[] array = new BigMoney[] {JPY_423};
            BigMoney.total(GBP, array);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitVarargs_currenciesDifferInArray() {
        BigMoney.total(GBP, GBP_2_33, JPY_423);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitArray_currenciesDifferInArray() {
        BigMoney[] array = new BigMoney[] {GBP_2_33, JPY_423};
            BigMoney.total(GBP, array);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitVarargs_nullFirst() {
        BigMoney.total(GBP, null, GBP_2_33, GBP_2_36);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitArray_nullFirst() {
        BigMoney[] array = new BigMoney[] {null, GBP_2_33, GBP_2_36};
            BigMoney.total(GBP, array);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitVarargs_nullNotFirst() {
        BigMoney.total(GBP, GBP_2_33, null, GBP_2_36);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitArray_nullNotFirst() {
        BigMoney[] array = new BigMoney[] {GBP_2_33, null, GBP_2_36};
            BigMoney.total(GBP, array);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitVarargs_badProvider() {
        BigMoney.total(GBP, BAD_PROVIDER);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitArray_badProvider() {
        BigMoneyProvider[] array = new BigMoneyProvider[] {BAD_PROVIDER};
            BigMoney.total(GBP, array);
        }

        //-----------------------------------------------------------------------
        // total(CurrencyUnit,Iterable)
        //-----------------------------------------------------------------------
        public void test_factory_total_CurrencyUnitIterable() {
        Iterable<BigMoney> iterable = Arrays.asList(GBP_1_23, GBP_2_33, BigMoney.of(GBP, 2.361d));
            BigMoney test = BigMoney.total(GBP, iterable);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmount(), BigDecimal.valueOf(5921, 3));
        }

        public void test_factory_total_CurrencyUnitIterable_Mixed() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_1_23.toMoney(), GBP_2_33);
            BigMoney test = BigMoney.total(GBP, iterable);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmount(), BigDecimal.valueOf(356, 2));
        }

        public void test_factory_total_CurrencyUnitIterable_empty() {
        Iterable<BigMoney> iterable = Collections.emptyList();
            BigMoney test = BigMoney.total(GBP, iterable);
            assertEquals(test.getCurrencyUnit(), GBP);
            assertEquals(test.getAmountMinorInt(), 0);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitIterable_currenciesDiffer() {
        Iterable<BigMoney> iterable = Arrays.asList(JPY_423);
            BigMoney.total(GBP, iterable);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_factory_total_CurrencyUnitIterable_currenciesDifferInIterable() {
        Iterable<BigMoney> iterable = Arrays.asList(GBP_2_33, JPY_423);
            BigMoney.total(GBP, iterable);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitIterable_nullFirst() {
        Iterable<BigMoney> iterable = Arrays.asList(null, GBP_2_33, GBP_2_36);
            BigMoney.total(GBP, iterable);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitIterable_nullNotFirst() {
        Iterable<BigMoney> iterable = Arrays.asList(GBP_2_33, null, GBP_2_36);
            BigMoney.total(GBP, iterable);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_factory_total_CurrencyUnitIterable_badProvider() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(BAD_PROVIDER);
            BigMoney.total(GBP, iterable);
        }*/

    /**
     * parse()
     */

    public function parseProvider()
    {
        (new CsvCurrencyDataProvider())->registerCurrencies();

        return [
            ["GBP 2.43", Currency::of('GBP'), "2.43", 2],
            ["GBP +12.57", Currency::of('GBP'), "12.57", 2],
            ["GBP -5.87", Currency::of('GBP'), "-5.87", 2],
            ["GBP 0.99", Currency::of('GBP'), "0.99", 2],
            //["GBP .99", Currency::of('GBP'), "0.99", 2],
            //["GBP +.99", Currency::of('GBP'), "0.99", 2],
            ["GBP +0.99", Currency::of('GBP'), "0.99", 2],
            //["GBP -.99", Currency::of('GBP'), "-0.99", 2],
            ["GBP -0.99", Currency::of('GBP'), "-0.99", 2],
            ["GBP 0", Currency::of('GBP'), "0", 0],
            ["GBP 2", Currency::of('GBP'), "2", 0],
            //["GBP 123.", Currency::of('GBP'), "123", 0],
            ["GBP3", Currency::of('GBP'), "3", 0],
            ["GBP3.10", Currency::of('GBP'), "3.10", 2],
            ["GBP  3.10", Currency::of('GBP'), "3.10", 2],
            ["GBP   3.10", Currency::of('GBP'), "3.10", 2],
            ["GBP                           3.10", Currency::of('GBP'), "3.10", 2],
            ["GBP 123.456789", Currency::of('GBP'), "123.456789", 6],
        ];
    }


    /**
     * @param $str
     * @param Currency $currency
     * @param $amountStr
     * @param $scale
     * @dataProvider parseProvider
     */
    public function test_factory_parse($str, Currency $currency, $amountStr, $scale)
    {
        $test = BigMoney::parse($str);
        $this->assertEquals($test->getCurrency(), $currency);
        $this->assertEquals($test->getAmount(), BigDecimal::of($amountStr));
        $this->assertEquals($test->getScale(), $scale);
    }
    /*
        //-----------------------------------------------------------------------
        // nonNull(BigMoney,CurrencyUnit)
        //-----------------------------------------------------------------------
        public void test_nonNull_BigMoneyCurrencyUnit_nonNull() {
        BigMoney test = BigMoney.nonNull(GBP_1_23, GBP);
            assertSame(test, GBP_1_23);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_nonNull_BigMoneyCurrencyUnit_nonNullCurrencyMismatch() {
        BigMoney.nonNull(GBP_1_23, JPY);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_nonNull_BigMoneyCurrencyUnit_nonNull_nullCurrency() {
        BigMoney.nonNull(GBP_1_23, null);
        }

        public void test_nonNull_BigMoneyCurrencyUnit_null() {
        BigMoney test = BigMoney.nonNull(null, GBP);
            assertEquals(test, BigMoney.ofMajor(GBP, 0));
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_nonNull_BigMoneyCurrencyUnit_null_nullCurrency() {
        BigMoney.nonNull(null, null);
        }*/

    /*
        public function test_scaleNormalization1()
        {
            $a = BigMoney::ofScale(self::$GBP, 100, 2);
            $b = BigMoney::ofScale(self::$GBP, 10, 2);
            //$b = BigMoney::ofScale(self::$GBP, 1, -2);
            $this->assertEquals($a->__toString(), "GBP 100");
            $this->assertEquals($b->__toString(), "GBP 100");
            $this->assertEquals($a->equals($a), true);
            $this->assertEquals($b->equals($b), true);
            $this->assertEquals($a->equals($b), true);
            $this->assertEquals($b->equals($a), true);
            //assertEquals(a.hashCode() == b.hashCode(), true);
        }

            public function test_scaleNormalization2() {
            BigMoney a = BigMoney.ofScale(GBP, 1, 1);
                BigMoney b = BigMoney.ofScale(GBP, 10, 2);
                assertEquals(a.toString(), "GBP 0.1");
                assertEquals(b.toString(), "GBP 0.10");
                assertEquals(a.equals(a), true);
                assertEquals(b.equals(b), true);
                assertEquals(a.equals(b), false);
                assertEquals(b.equals(a), false);
                assertEquals(a.hashCode() == b.hashCode(), false);
            }

            public function test_scaleNormalization3() {
            BigMoney a = BigMoney.of(GBP, new BigDecimal("100"));
                BigMoney b = BigMoney.of(GBP, new BigDecimal("1E+2"));
                assertEquals(a.toString(), "GBP 100");
                assertEquals(b.toString(), "GBP 100");
                assertEquals(a.equals(a), true);
                assertEquals(b.equals(b), true);
                assertEquals(a.equals(b), true);
                assertEquals(b.equals(a), true);
                assertEquals(a.hashCode() == b.hashCode(), true);
            }*/

    /*
     * serialize()
     */

    public function test_serialization()
    {
        $a = BigMoney::parse("GBP 2.34");
        $serialized = serialize($a);

        $this->assertEquals($a, unserialize($serialized));
    }

    public function test_getCurrencyUnit_GBP()
    {
        $this->assertEquals(self::$GBP_2_34->getCurrency(), self::$GBP);
    }

    public function test_getCurrencyUnit_EUR()
    {
        $this->assertEquals(BigMoney::parse("EUR -5.78")->getCurrency(), self::$EUR);
    }

    public function test_withCurrencyUnit_Currency()
    {
        $test = self::$GBP_2_34->withCurrency(self::$USD);
        $this->assertEquals($test->__toString(), "USD 2.34");
    }

    public function test_withCurrencyUnit_Currency_same()
    {
        $test = self::$GBP_2_34->withCurrency(self::$GBP);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_withCurrencyUnit_Currency_differentCurrencyScale()
    {
        $test = self::$GBP_2_34->withCurrency(self::$JPY);
        $this->assertEquals($test->__toString(), "JPY 2.34");
    }

    public function test_getScale_GBP()
    {
        $this->assertEquals(self::$GBP_2_34->getScale(), 2);
    }

    public function test_getScale_JPY()
    {
        $this->assertEquals(self::$JPY_423->getScale(), 0);
    }


    public function test_isCurrencyScale_GBP()
    {
        $this->assertEquals(BigMoney::parse("GBP 2")->isCurrencyScale(), false);
        $this->assertEquals(BigMoney::parse("GBP 2.3")->isCurrencyScale(), false);
        $this->assertEquals(BigMoney::parse("GBP 2.34")->isCurrencyScale(), true);
        $this->assertEquals(BigMoney::parse("GBP 2.345")->isCurrencyScale(), false);
    }

    public function test_isCurrencyScale_JPY()
    {
        $this->assertEquals(BigMoney::parse("JPY 2")->isCurrencyScale(), true);
        $this->assertEquals(BigMoney::parse("JPY 2.3")->isCurrencyScale(), false);
        $this->assertEquals(BigMoney::parse("JPY 2.34")->isCurrencyScale(), false);
        $this->assertEquals(BigMoney::parse("JPY 2.345")->isCurrencyScale(), false);
    }

    public function test_withScale_int_same()
    {
        $test = self::$GBP_2_34->withScale(2);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_withScale_int_more()
    {
        $test = self::$GBP_2_34->withScale(3);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.340"));
        $this->assertEquals($test->getScale(), 3);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withScale_int_less()
    {
        BigMoney::parse("GBP 2.345")->withScale(2);
    }

    public function test_withScale_intRoundingMode_less()
    {
        $test = self::$GBP_2_34->withScale(1, RoundingMode::UP);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.4"));
        $this->assertEquals($test->getScale(), 1);
    }

    public function test_withScale_intRoundingMode_more()
    {
        $test = self::$GBP_2_34->withScale(3, RoundingMode::UP);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.340"));
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_withCurrencyScale_int_same()
    {
        $test = self::$GBP_2_34->withCurrencyScale();
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_withCurrencyScale_int_more()
    {
        $test = BigMoney::parse("GBP 2.3")->withCurrencyScale();
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.30"));
        $this->assertEquals($test->getScale(), 2);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withCurrencyScale_int_less()
    {
        BigMoney::parse("GBP 2.345")->withCurrencyScale();
    }

    public function test_withCurrencyScale_intRoundingMode_less()
    {
        $test = BigMoney::parse("GBP 2.345")->withCurrencyScale(RoundingMode::UP);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.35"));
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_withCurrencyScale_intRoundingMode_more()
    {
        $test = BigMoney::parse("GBP 2.3")->withCurrencyScale(RoundingMode::UP);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.30"));
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_withCurrencyScale_intRoundingMode_lessJPY()
    {
        $test = BigMoney::parse("JPY 2.345")->withCurrencyScale(RoundingMode::UP);
        $this->assertEquals($test->getAmount(), BigDecimal::of("3"));
        $this->assertEquals($test->getScale(), 0);
    }

    public function test_getAmount_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmount(), BigDecimal::of(self::BIGDEC_2_34));
    }

    public function test_getAmount_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmount(), BigDecimal::of(self::BIGDEC_M5_78));
    }

    public function test_getAmountMajor_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmountMajor(), BigDecimal::of(2));
    }

    public function test_getAmountMajor_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmountMajor(), BigDecimal::of(-5));
    }

    public function test_getAmountMajorInt_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmountMajorInt(), 2);
    }

    public function test_getAmountMajorInt_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmountMajorInt(), -5);
    }

    /*
        @Test(expectedExceptions = ArithmeticException.class)
        public void test_getAmountMajorInt_tooBigPositive() {
        GBP_INT_MAX_MAJOR_PLUS1.getAmountMajorInt();
        }

        @Test(expectedExceptions = ArithmeticException.class)
        public void test_getAmountMajorInt_tooBigNegative() {
        GBP_INT_MIN_MAJOR_MINUS1.getAmountMajorInt();
        }
*/

    public function test_getAmountMinor_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmountMinor(), BigDecimal::of(234));
    }

    public function test_getAmountMinor_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmountMinor(), BigDecimal::of(-578));
    }

    public function test_getAmountMinorInt_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmountMinorInt(), 234);
    }

    public function test_getAmountMinorInt_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmountMinorInt(), -578);
    }

    /*
        @Test(expectedExceptions = ArithmeticException.class)
        public void test_getAmountMinorInt_tooBigPositive() {
        GBP_INT_MAX_PLUS1.getAmountMinorInt();
        }

        @Test(expectedExceptions = ArithmeticException.class)
        public void test_getAmountMinorInt_tooBigNegative() {
        GBP_INT_MIN_MINUS1.getAmountMinorInt();
        }*/


    public function test_getMinorPart_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getMinorPart(), 34);
    }

    public function test_getMinorPart_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getMinorPart(), -78);
    }


    public function test_isZero()
    {
        $this->assertEquals(self::$GBP_0_00->isZero(), true);
        $this->assertEquals(self::$GBP_2_34->isZero(), false);
        $this->assertEquals(self::$GBP_M5_78->isZero(), false);
    }

    public function test_isPositive()
    {
        $this->assertEquals(self::$GBP_0_00->isPositive(), false);
        $this->assertEquals(self::$GBP_2_34->isPositive(), true);
        $this->assertEquals(self::$GBP_M5_78->isPositive(), false);
    }

    public function test_isPositiveOrZero()
    {
        $this->assertEquals(self::$GBP_0_00->isPositiveOrZero(), true);
        $this->assertEquals(self::$GBP_2_34->isPositiveOrZero(), true);
        $this->assertEquals(self::$GBP_M5_78->isPositiveOrZero(), false);
    }

    public function test_isNegative()
    {
        $this->assertEquals(self::$GBP_0_00->isNegative(), false);
        $this->assertEquals(self::$GBP_2_34->isNegative(), false);
        $this->assertEquals(self::$GBP_M5_78->isNegative(), true);
    }

    public function test_isNegativeOrZero()
    {
        $this->assertEquals(self::$GBP_0_00->isNegativeOrZero(), true);
        $this->assertEquals(self::$GBP_2_34->isNegativeOrZero(), false);
        $this->assertEquals(self::$GBP_M5_78->isNegativeOrZero(), true);
    }

    public function test_withAmount_BigDecimal()
    {
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_2_345);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.345"));
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_withAmount_BigDecimal_same()
    {
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_2_34);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_withAmount_BigDecimal_nullBigDecimal()
    {
        self::$GBP_2_34->withAmount(null);
    }

    public function test_withAmount_double()
    {
        $test = self::$GBP_2_34->withAmount(2.345);
        $this->assertEquals($test->getAmount(), BigDecimal::of("2.345"));
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_withAmount_double_same()
    {
        $test = self::$GBP_2_34->withAmount(2.34);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /*
        //-----------------------------------------------------------------------
        // plus(Iterable)
        //-----------------------------------------------------------------------
        public void test_plus_Iterable_BigMoneyProvider() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, GBP_1_23);
            BigMoney test = GBP_2_34.plus(iterable);
            assertEquals(test.toString(), "GBP 5.90");
        }

        public void test_plus_Iterable_BigMoney() {
        Iterable<BigMoney> iterable = Arrays.<BigMoney>asList(GBP_2_33, GBP_1_23);
            BigMoney test = GBP_2_34.plus(iterable);
            assertEquals(test.toString(), "GBP 5.90");
        }

        public void test_plus_Iterable_Money() {
        Iterable<Money> iterable = Arrays.<Money>asList(GBP_2_33.toMoney(), GBP_1_23.toMoney());
            BigMoney test = GBP_2_34.plus(iterable);
            assertEquals(test.toString(), "GBP 5.90");
        }

        public void test_plus_Iterable_Mixed() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33.toMoney(), new BigMoneyProvider() {
                @Override
                public BigMoney toBigMoney() {
                    return GBP_1_23;
                }
            });
            BigMoney test = GBP_2_34.plus(iterable);
            assertEquals(test.toString(), "GBP 5.90");
        }

        public void test_plus_Iterable_zero() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_0_00);
            BigMoney test = GBP_2_34.plus(iterable);
            assertEquals(test, GBP_2_34);
        }

        @Test(expectedExceptions = CurrencyMismatchException.class)
        public void test_plus_Iterable_currencyMismatch() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, JPY_423);
            GBP_M5_78.plus(iterable);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_plus_Iterable_nullEntry() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, null);
            GBP_M5_78.plus(iterable);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_plus_Iterable_nullIterable() {
        GBP_M5_78.plus((Iterable<BigMoneyProvider>) null);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_plus_Iterable_badProvider() {
        Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(new BigMoneyProvider() {
                @Override
                public BigMoney toBigMoney() {
                    return null;
                }
            });
            GBP_M5_78.plus(iterable);
        }*/

    /*
     * plus()
     */

    public function test_plus_BigMoneyProvider_zero()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_0_00);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_BigMoneyProvider_positive()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_1_23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_BigMoneyProvider_negative()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_M1_23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_BigMoneyProvider_scale()
    {
        $test = self::$GBP_2_34->plus(BigMoney::parse("GBP 1.111"));
        $this->assertEquals($test->__toString(), "GBP 3.451");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_plus_BigMoneyProvider_Money()
    {
        $test = self::$GBP_2_34->plus(BigMoney::ofMinor(self::$GBP, 1));
        $this->assertEquals($test->__toString(), "GBP 2.35");
        $this->assertEquals($test->getScale(), 2);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_plus_BigMoneyProvider_currencyMismatch()
    {
        self::$GBP_M5_78->plus(self::$USD_1_23);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plus_BigMoneyProvider_nullBigMoneyProvider()
    {
        self::$GBP_M5_78->plus(null);
    }

    //   @Test(expectedExceptions = NullPointerException.class)
    /*public function test_plus_BigMoneyProvider_badProvider() {
    GBP_M5_78.plus(new BigMoneyProvider() {
            @Override
            public BigMoney toBigMoney() {
                return null;
            }
        });
    }*/

    public function test_plus_BigDecimal_zero()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::zero());
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_BigDecimal_positive()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("1.23"));
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_BigDecimal_negative()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("-1.23"));
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_BigDecimal_scale()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("1.235"));
        $this->assertEquals($test->__toString(), "GBP 3.575");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_plus_double_zero()
    {
        $test = self::$GBP_2_34->plus(0.0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_double_positive()
    {
        $test = self::$GBP_2_34->plus(1.23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_double_negative()
    {
        $test = self::$GBP_2_34->plus(-1.23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plus_double_scale()
    {
        $test = self::$GBP_2_34->plus(1.234);
        $this->assertEquals($test->__toString(), "GBP 3.574");
        $this->assertEquals($test->getScale(), 3);
    }

    /*
     * plusMajor()
     */
    public function test_plusMajor_zero()
    {
        $test = self::$GBP_2_34->plusMajor(0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plusMajor_positive()
    {
        $test = self::$GBP_2_34->plusMajor(123);
        $this->assertEquals($test->__toString(), "GBP 125.34");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plusMajor_negative()
    {
        $test = self::$GBP_2_34->plusMajor(-123);
        $this->assertEquals($test->__toString(), "GBP -120.66");
        $this->assertEquals($test->getScale(), 2);
    }

    /*
     * plusMinor()
     */
    public function test_plusMinor_zero()
    {
        $test = self::$GBP_2_34->plusMinor(0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plusMinor_positive()
    {
        $test = self::$GBP_2_34->plusMinor(123);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plusMinor_negative()
    {
        $test = self::$GBP_2_34->plusMinor(-123);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_plusMinor_scale()
    {
        $test = BigMoney::parse("GBP 12")->plusMinor(123);
        $this->assertEquals($test->__toString(), "GBP 13.23");
        $this->assertEquals($test->getScale(), 2);
    }

    /*
     * plusRetainScale()
     */

    public function test_plusRetainScale_BigMoneyProviderRoundingMode_zero()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigMoney::zero(self::$GBP), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plusRetainScale_BigMoneyProviderRoundingMode_positive()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigMoney::parse("GBP 1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plusRetainScale_BigMoneyProviderRoundingMode_negative()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigMoney::parse("GBP -1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_plusRetainScale_BigMoneyProviderRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigMoney::parse("GBP 1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plusRetainScale_BigMoneyProviderRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->plusRetainScale(BigMoney::parse("GBP 1.235"), RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plusRetainScale_BigMoneyProviderRoundingMode_nullBigDecimal()
    {
        self::$GBP_M5_78->plusRetainScale(null, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plusRetainScale_BigMoneyProviderRoundingMode_nullRoundingMode()
    {
        self::$GBP_M5_78->plusRetainScale(BigMoney::parse("GBP 1.23"), null);
    }


    public function test_plusRetainScale_BigDecimalRoundingMode_zero()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigDecimal::zero(), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plusRetainScale_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigDecimal::of("1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plusRetainScale_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigDecimal::of("-1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_plusRetainScale_BigDecimalRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->plusRetainScale(BigDecimal::of("1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plusRetainScale_BigDecimalRoundingMode_roundUnecessary()
    {
        self::$GBP_2_34->plusRetainScale(BigDecimal::of("1.235"), RoundingMode::UNNECESSARY);
    }

    public function test_plusRetainScale_doubleRoundingMode_zero()
    {
        $test = self::$GBP_2_34->plusRetainScale(0.0, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plusRetainScale_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_34->plusRetainScale(1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plusRetainScale_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_34->plusRetainScale(-1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_plusRetainScale_doubleRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->plusRetainScale(1.235, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plusRetainScale_doubleRoundingMode_roundUnecessary()
    {
        self::$GBP_2_34->plusRetainScale(1.235, RoundingMode::UNNECESSARY);
    }


    /*
            //-----------------------------------------------------------------------
            // minus(Iterable)
            //-----------------------------------------------------------------------
            public void test_minus_Iterable_BigMoneyProvider() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, GBP_1_23);
                BigMoney test = GBP_2_34.minus(iterable);
                assertEquals(test.toString(), "GBP -1.22");
            }

            public void test_minus_Iterable_BigMoney() {
            Iterable<BigMoney> iterable = Arrays.<BigMoney>asList(GBP_2_33, GBP_1_23);
                BigMoney test = GBP_2_34.minus(iterable);
                assertEquals(test.toString(), "GBP -1.22");
            }

            public void test_minus_Iterable_Money() {
            Iterable<Money> iterable = Arrays.<Money>asList(GBP_2_33.toMoney(), GBP_1_23.toMoney());
                BigMoney test = GBP_2_34.minus(iterable);
                assertEquals(test.toString(), "GBP -1.22");
            }

            public void test_minus_Iterable_Mixed() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33.toMoney(), new BigMoneyProvider() {
                    @Override
                    public BigMoney toBigMoney() {
                        return GBP_1_23;
                    }
                });
                BigMoney test = GBP_2_34.minus(iterable);
                assertEquals(test.toString(), "GBP -1.22");
            }

            public void test_minus_Iterable_zero() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_0_00);
                BigMoney test = GBP_2_34.minus(iterable);
                assertEquals(test, GBP_2_34);
            }

            @Test(expectedExceptions = CurrencyMismatchException.class)
            public void test_minus_Iterable_currencyMismatch() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, JPY_423);
                GBP_M5_78.minus(iterable);
            }

            @Test(expectedExceptions = NullPointerException.class)
            public void test_minus_Iterable_nullEntry() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(GBP_2_33, null);
                GBP_M5_78.minus(iterable);
            }

            @Test(expectedExceptions = NullPointerException.class)
            public void test_minus_Iterable_nullIterable() {
            GBP_M5_78.minus((Iterable<BigMoneyProvider>) null);
            }

            @Test(expectedExceptions = NullPointerException.class)
            public void test_minus_Iterable_badProvider() {
            Iterable<BigMoneyProvider> iterable = Arrays.<BigMoneyProvider>asList(new BigMoneyProvider() {
                    @Override
                    public BigMoney toBigMoney() {
                        return null;
                    }
                });
                GBP_M5_78.minus(iterable);
            }*/


    public function test_minus_BigMoneyProvider_zero()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_0_00);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_BigMoneyProvider_positive()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_1_23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_BigMoneyProvider_negative()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_M1_23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_BigMoneyProvider_scale()
    {
        $test = self::$GBP_2_34->minus(BigMoney::parse("GBP 1.111"));
        $this->assertEquals($test->__toString(), "GBP 1.229");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_minus_BigMoneyProvider_Money()
    {
        $test = self::$GBP_2_34->minus(BigMoney::ofMinor(self::$GBP, 1));
        $this->assertEquals($test->__toString(), "GBP 2.33");
        $this->assertEquals($test->getScale(), 2);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_minus_BigMoneyProvider_currencyMismatch()
    {
        self::$GBP_M5_78->minus(self::$USD_1_23);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minus_BigMoneyProvider_nullBigMoneyProvider()
    {
        self::$GBP_M5_78->minus(null);
    }

    /* TOOO @Test(expectedExceptions = NullPointerException.class)
    public void test_minus_BigMoneyProvider_badProvider() {
    GBP_M5_78.minus(new BigMoneyProvider() {
            @Override
            public BigMoney toBigMoney() {
                return null;
            }
        });
    }*/


    public function test_minus_BigDecimal_zero()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::zero());
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_BigDecimal_positive()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("1.23"));
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_BigDecimal_negative()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("-1.23"));
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_BigDecimal_scale()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("1.235"));
        $this->assertEquals($test->__toString(), "GBP 1.105");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_minus_double_zero()
    {
        $test = self::$GBP_2_34->minus(0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_double_positive()
    {
        $test = self::$GBP_2_34->minus(1.23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_double_negative()
    {
        $test = self::$GBP_2_34->minus(-1.23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minus_double_scale()
    {
        $test = self::$GBP_2_34->minus(1.235);
        $this->assertEquals($test->__toString(), "GBP 1.105");
        $this->assertEquals($test->getScale(), 3);
    }


    public function test_minusMajor_zero()
    {
        $test = self::$GBP_2_34->minusMajor(0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minusMajor_positive()
    {
        $test = self::$GBP_2_34->minusMajor(123);
        $this->assertEquals($test->__toString(), "GBP -120.66");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minusMajor_negative()
    {
        $test = self::$GBP_2_34->minusMajor(-123);
        $this->assertEquals($test->__toString(), "GBP 125.34");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minusMinor_zero()
    {
        $test = self::$GBP_2_34->minusMinor(0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minusMinor_positive()
    {
        $test = self::$GBP_2_34->minusMinor(123);
        $this->assertEquals($test->__toString(), "GBP 1.11");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minusMinor_negative()
    {
        $test = self::$GBP_2_34->minusMinor(-123);
        $this->assertEquals($test->__toString(), "GBP 3.57");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_minusMinor_scale()
    {
        $test = BigMoney::parse("GBP 12")->minusMinor(123);
        $this->assertEquals($test->__toString(), "GBP 10.77");
        $this->assertEquals($test->getScale(), 2);
    }


    public function test_minusRetainScale_BigMoneyProviderRoundingMode_zero()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigMoney::zero(self::$GBP), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minusRetainScale_BigMoneyProviderRoundingMode_positive()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigMoney::parse("GBP 1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minusRetainScale_BigMoneyProviderRoundingMode_negative()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigMoney::parse("GBP -1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_minusRetainScale_BigMoneyProviderRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigMoney::parse("GBP 1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 1.10");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minusRetainScale_BigMoneyProviderRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->minusRetainScale(BigMoney::parse("GBP 1.235"), RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minusRetainScale_BigMoneyProviderRoundingMode_nullBigMoneyProvider()
    {
        self::$GBP_M5_78->minusRetainScale(null, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minusRetainScale_BigMoneyProviderRoundingMode_nullRoundingMode()
    {
        self::$GBP_M5_78->minusRetainScale(BigMoney::parse("GBP 123"), null);
    }


    public function test_minusRetainScale_BigDecimalRoundingMode_zero()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigDecimal::zero(), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minusRetainScale_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigDecimal::of("1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minusRetainScale_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigDecimal::of("-1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_minusRetainScale_BigDecimalRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->minusRetainScale(BigDecimal::of("1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 1.10");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minusRetainScale_BigDecimalRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->minusRetainScale(BigDecimal::of("1.235"), RoundingMode::UNNECESSARY);
    }

    public function test_minusRetainScale_doubleRoundingMode_zero()
    {
        $test = self::$GBP_2_34->minusRetainScale(0.0, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minusRetainScale_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_34->minusRetainScale(1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minusRetainScale_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_34->minusRetainScale(-1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_minusRetainScale_doubleRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->minusRetainScale(1.235, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 1.10");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minusRetainScale_doubleRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->minusRetainScale(1.235, RoundingMode::UNNECESSARY);
    }

    /*
     * multipliedBy()
     */

    public function test_multipliedBy_BigDecimal_one()
    {
        $test = self::$GBP_2_34->multipliedBy(BigDecimal::one());
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multipliedBy_BigDecimal_positive()
    {
        $test = self::$GBP_2_33->multipliedBy(BigDecimal::of("2.5"));
        $this->assertEquals($test->__toString(), "GBP 5.825");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_multipliedBy_BigDecimal_negative()
    {
        $test = self::$GBP_2_33->multipliedBy(BigDecimal::of("-2.5"));
        $this->assertEquals($test->__toString(), "GBP -5.825");
        $this->assertEquals($test->getScale(), 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_multipliedBy_BigDecimal_nullBigDecimal()
    {
        self::$GBP_5_78->multipliedBy(null);
    }

    public function test_multipliedBy_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_33->multipliedBy(2.5);
        $this->assertEquals($test->__toString(), "GBP 5.825");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_multipliedBy_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_33->multipliedBy(-2.5);
        $this->assertEquals($test->__toString(), "GBP -5.825");
        $this->assertEquals($test->getScale(), 3);
    }

    public function test_multipliedBy_long_positive()
    {
        $test = self::$GBP_2_34->multipliedBy(3);
        $this->assertEquals($test->__toString(), "GBP 7.02");
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_multipliedBy_long_negative()
    {
        $test = self::$GBP_2_34->multipliedBy(-3);
        $this->assertEquals($test->__toString(), "GBP -7.02");
        $this->assertEquals($test->getScale(), 2);
    }

    /*
     * multiplyRetainScale()
     */

    public function test_multiplyRetainScale_BigDecimalRoundingMode_one()
    {
        $test = self::$GBP_2_34->multiplyRetainScale(BigDecimal::one(), RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multiplyRetainScale_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(BigDecimal::of("2.5"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 5.82");
    }

    public function test_multiplyRetainScale_BigDecimalRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(BigDecimal::of("2.5"), RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 5.83");
    }

    public function test_multiplyRetainScale_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(BigDecimal::of("-2.5"), RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -5.83");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_multiplyRetainScale_BigDecimalRoundingMode_nullBigDecimal()
    {
        self::$GBP_5_78->multiplyRetainScale(null, RoundingMode::DOWN);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_multiplyRetainScale_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_5_78->multiplyRetainScale(BigDecimal::of("2.5"), null);
    }

    public function test_multiplyRetainScale_doubleRoundingMode_one()
    {
        $test = self::$GBP_2_34->multiplyRetainScale(1.0, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multiplyRetainScale_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(2.5, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 5.82");
    }

    public function test_multiplyRetainScale_doubleRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(2.5, RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 5.83");
    }

    public function test_multiplyRetainScale_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_33->multiplyRetainScale(-2.5, RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -5.83");
    }

    /*
     * dividedBy()
     */

    public function test_dividedBy_BigDecimalRoundingMode_one()
    {
        $test = self::$GBP_2_34->dividedBy(BigDecimal::one(), RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_dividedBy_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_34->dividedBy(BigDecimal::of("2.5"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.93");
    }

    public function test_dividedBy_BigDecimalRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_34->dividedBy(BigDecimal::of("2.5"), RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 0.94");
    }

    public function test_dividedBy_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_34->dividedBy(BigDecimal::of("-2.5"), RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -0.94");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_dividedBy_BigDecimalRoundingMode_nullBigDecimal()
    {
        self::$GBP_5_78->dividedBy(null, RoundingMode::DOWN);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_dividedBy_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_5_78->dividedBy(BigDecimal::of("2.5"), null);
    }

    public function test_dividedBy_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_34->dividedBy(2.5, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.93");
    }

    public function test_dividedBy_doubleRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_34->dividedBy(2.5, RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 0.94");
    }

    public function test_dividedBy_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_34->dividedBy(-2.5, RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -0.94");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_dividedBy_doubleRoundingMode_nullRoundingMode()
    {
        self::$GBP_5_78->dividedBy(2.5, null);
    }

    public function test_dividedBy_long_positive()
    {
        $test = self::$GBP_2_34->dividedBy(3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.78");
    }

    public function test_dividedBy_long_positive_roundDown()
    {
        $test = self::$GBP_2_34->dividedBy(3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.78");
    }


    /* TODO public function test_dividedBy_long_positive_roundUp()
    {
        $test = self::$GBP_2_34->dividedBy(3, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "GBP 0.79");
    }*/

    public function test_dividedBy_long_negative()
    {
        $test = self::$GBP_2_34->dividedBy(-3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP -0.78");
    }

    public function test_negated_zero()
    {
        $test = self::$GBP_0_00->negated();
        $this->assertSame($test, self::$GBP_0_00);
    }

    public function test_negated_positive()
    {
        $test = self::$GBP_2_34->negated();
        $this->assertEquals($test->__toString(), "GBP -2.34");
    }

    public function test_negated_negative()
    {
        $test = BigMoney::parse("GBP -2.34")->negated();
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }


    public function test_abs_positive()
    {
        $test = self::$GBP_2_34->abs();
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_abs_negative()
    {
        $test = BigMoney::parse("GBP -2.34")->abs();
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }


    public function test_round_2down()
    {
        $test = self::$GBP_2_34->rounded(2, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }


    public function test_round_2up()
    {
        $test = self::$GBP_2_34->rounded(2, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_round_1down()
    {
        $test = self::$GBP_2_34->rounded(1, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 2.30");
    }

    public function test_round_1up()
    {
        $test = self::$GBP_2_34->rounded(1, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "GBP 2.40");
    }

    public function test_round_0down()
    {
        $test = self::$GBP_2_34->rounded(0, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 2.00");
    }

    public function test_round_0up()
    {
        $test = self::$GBP_2_34->rounded(0, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "GBP 3.00");
    }

    /* TODO
    public function test_round_M1down()
    {
        $test = BigMoney::parse("GBP 432.34")->rounded(-1, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 430.00");
    }

    public function test_round_M1up()
    {
        $test = BigMoney::parse("GBP 432.34")->rounded(-1, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "GBP 440.00");
    }*/

    public function test_round_3()
    {
        $test = self::$GBP_2_34->rounded(3, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34); // same
    }

    /*
        //-----------------------------------------------------------------------
        // convertedTo(CurrencyUnit,BigDecimal)
        //-----------------------------------------------------------------------
        public void test_convertedTo_CurrencyUnit_BigDecimal_positive() {
        BigMoney test = GBP_2_33.convertedTo(EUR, bd("2.5"));
            assertEquals(test.toString(), "EUR 5.825");
        }

        @Test(expectedExceptions = IllegalArgumentException.class)
        public void test_convertedTo_CurrencyUnit_BigDecimal_negative() {
        GBP_2_33.convertedTo(EUR, bd("-2.5"));
        }

        @Test(expectedExceptions = IllegalArgumentException.class)
        public void test_convertedTo_CurrencyUnit_BigDecimal_sameCurrency() {
        GBP_2_33.convertedTo(GBP, bd("2.5"));
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_convertedTo_CurrencyUnit_BigDecimal_nullCurrency() {
        GBP_5_78.convertedTo((CurrencyUnit) null, bd("2"));
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_convertedTo_CurrencyUnit_BigDecimal_nullBigDecimal() {
        GBP_5_78.convertedTo(EUR, (BigDecimal) null);
        }

        //-----------------------------------------------------------------------
        // convertRetainScale(CurrencyUnit,BigDecimal,RoundingMode)
        //-----------------------------------------------------------------------
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_positive() {
        BigMoney test = BigMoney.parse("GBP 2.2").convertRetainScale(EUR, bd("2.5"), RoundingMode.DOWN);
            assertEquals(test.toString(), "EUR 5.5");
        }

        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_roundHalfUp() {
        BigMoney test = BigMoney.parse("GBP 2.21").convertRetainScale(EUR, bd("2.5"), RoundingMode.HALF_UP);
            assertEquals(test.toString(), "EUR 5.53");
        }

        @Test(expectedExceptions = IllegalArgumentException.class)
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_negative() {
        GBP_2_33.convertRetainScale(EUR, bd("-2.5"), RoundingMode.DOWN);
        }

        @Test(expectedExceptions = IllegalArgumentException.class)
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_sameCurrency() {
        GBP_2_33.convertRetainScale(GBP, bd("2.5"), RoundingMode.DOWN);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_nullCurrency() {
        GBP_5_78.convertRetainScale((CurrencyUnit) null, bd("2"), RoundingMode.DOWN);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_nullBigDecimal() {
        GBP_5_78.convertRetainScale(EUR, (BigDecimal) null, RoundingMode.DOWN);
        }

        @Test(expectedExceptions = NullPointerException.class)
        public void test_convertRetainScale_CurrencyUnit_BigDecimal_RoundingMode_nullRoundingMode() {
        GBP_5_78.convertRetainScale(EUR, bd("2"), (RoundingMode) null);
        }*/


    public function test_toBigMoney()
    {
        $this->assertSame(self::$GBP_2_34->toBigMoney(), self::$GBP_2_34);
    }

    public function test_toMoney()
    {
        $this->assertEquals(self::$GBP_2_34->toMoney(), Money::of(self::$GBP, self::BIGDEC_2_34));
    }


    public function test_toMoney_RoundingMode()
    {
        $this->assertEquals(self::$GBP_2_34->toMoney(RoundingMode::HALF_EVEN), Money::parse("GBP 2.34"));
    }

    public function test_toMoney_RoundingMode_round()
    {
        $money = BigMoney::parse("GBP 2.355");
        $this->assertEquals($money->toMoney(RoundingMode::HALF_EVEN), Money::parse("GBP 2.36"));
    }

    /*
     * isSameCurrency()
     */
    public function test_isSameCurrency_BigMoney_same()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(self::$GBP_2_35), true);
    }

    public function test_isSameCurrency_BigMoney_different()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(self::$USD_2_34), false);
    }

    public function test_isSameCurrency_Money_same()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(Money::parse("GBP 2")), true);
    }

    public function test_isSameCurrency_Money_different()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(Money::parse("USD 2")), false);
    }

    public function test_compareTo_BigMoney()
    {
        $a = self::$GBP_2_34;
        $b = self::$GBP_2_35;
        $c = self::$GBP_2_36;
        $this->assertEquals($a->compareTo($a), 0);
        $this->assertEquals($b->compareTo($b), 0);
        $this->assertEquals($c->compareTo($c), 0);

        $this->assertEquals($a->compareTo($b), -1);
        $this->assertEquals($b->compareTo($a), 1);

        $this->assertEquals($a->compareTo($c), -1);
        $this->assertEquals($c->compareTo($a), 1);

        $this->assertEquals($b->compareTo($c), -1);
        $this->assertEquals($c->compareTo($b), 1);
    }

    public function test_compareTo_Money()
    {
        $t = self::$GBP_2_35;
        $a = Money::ofMinor(self::$GBP, 234);
        $b = Money::ofMinor(self::$GBP, 235);
        $c = Money::ofMinor(self::$GBP, 236);
        $this->assertEquals($t->compareTo($a), 1);
        $this->assertEquals($t->compareTo($b), 0);
        $this->assertEquals($t->compareTo($c), -1);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_compareTo_currenciesDiffer()
    {
        $a = self::$GBP_2_34;
        $b = self::$USD_2_35;
        $a->compareTo($b);
    }

    public function test_isEqual()
    {
        $a = self::$GBP_2_34;
        $b = self::$GBP_2_35;
        $c = self::$GBP_2_36;
        $this->assertTrue($a->isEqual($a));
        $this->assertTrue($b->isEqual($b));
        $this->assertTrue($c->isEqual($c));

        $this->assertFalse($a->isEqual($b));
        $this->assertFalse($b->isEqual($a));

        $this->assertFalse($a->isEqual($c));
        $this->assertFalse($c->isEqual($a));

        $this->assertFalse($b->isEqual($c));
        $this->assertFalse($c->isEqual($b));
    }

    public function test_isEqual_Money()
    {
        $a = self::$GBP_2_34;
        $b = Money::ofMinor(self::$GBP, 234);
        $this->assertTrue($a->isEqual($b));
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_isEqual_currenciesDiffer()
    {
        $a = self::$GBP_2_34;
        $b = self::$USD_2_35;
        $a->isEqual($b);
    }


    public function test_isGreaterThan()
    {
        $a = self::$GBP_2_34;
        $b = self::$GBP_2_35;
        $c = self::$GBP_2_36;
        $this->assertFalse($a->isGreaterThan($a));
        $this->assertFalse($b->isGreaterThan($b));
        $this->assertFalse($c->isGreaterThan($c));

        $this->assertFalse($a->isGreaterThan($b));
        $this->assertTrue($b->isGreaterThan($a));

        $this->assertFalse($a->isGreaterThan($c));
        $this->assertTrue($c->isGreaterThan($a));

        $this->assertFalse($b->isGreaterThan($c));
        $this->assertTrue($c->isGreaterThan($b));
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_isGreaterThan_currenciesDiffer()
    {
        $a = self::$GBP_2_34;
        $b = self::$USD_2_35;
        $a->isGreaterThan($b);
    }


    public function test_isLessThan()
    {
        $a = self::$GBP_2_34;
        $b = self::$GBP_2_35;
        $c = self::$GBP_2_36;

        $this->assertFalse($a->isLessThan($a));
        $this->assertFalse($b->isLessThan($b));
        $this->assertFalse($c->isLessThan($c));

        $this->assertTrue($a->isLessThan($b));
        $this->assertFalse($b->isLessThan($a));

        $this->assertTrue($a->isLessThan($c));
        $this->assertFalse($c->isLessThan($a));

        $this->assertTrue($b->isLessThan($c));
        $this->assertFalse($c->isLessThan($b));
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_isLessThan_currenciesDiffer()
    {
        $a = self::$GBP_2_34;
        $b = self::$USD_2_35;
        $a->isLessThan($b);
    }

    /*
     * equals()
     */
    public function test_equals_hashCode_positive()
    {
        $a = self::$GBP_2_34;
        $b = self::$GBP_2_34;
        $c = self::$GBP_2_35;
        $this->assertTrue($a->equals($a));
        $this->assertTrue($b->equals($b));
        $this->assertTrue($c->equals($c));
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
        $this->assertFalse($a->equals($c));
        $this->assertFalse($b->equals($c));
    }

    public function test_equals_false()
    {
        $a = self::$GBP_2_34;
        $this->assertFalse($a->equals(null));
        $this->assertFalse($a->equals("String"));
    }

    public function test_toString_positive()
    {
        $test = BigMoney::of(self::$GBP, self::BIGDEC_2_34);
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }

    public function test_toString_negative()
    {
        $test = BigMoney::of(self::$EUR, self::BIGDEC_M5_78);
        $this->assertEquals($test->__toString(), "EUR -5.78");
    }
}