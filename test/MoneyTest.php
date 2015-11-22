<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 21/11/15
 * Time: 14:17
 */

namespace SmartGecko\Money\Test;

use SmartGecko\Money\BigMoney;
use SmartGecko\Money\CsvCurrencyDataProvider;
use SmartGecko\Money\Currency;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use SmartGecko\Money\CurrencyMismatchException;
use SmartGecko\Money\Money;

class MoneyTest extends \PHPUnit_Framework_TestCase
{

    const BIGDEC_2_34 = "2.34";
    const BIGDEC_2_3 = "2.3";
    const BIGDEC_2_345 = "2.345";
    const BIGDEC_M5_78 = "-5.78";

    private static $GBP;
    private static $EUR;
    private static $USD;
    private static $JPY;

    /** @var  Money */
    private static $GBP_0_00;
    /** @var  Money */
    private static $GBP_1_23;
    /** @var  Money */
    private static $GBP_2_33;
    /** @var  Money */
    private static $GBP_2_34;
    /** @var  Money */
    private static $GBP_2_35;
    /** @var  Money */
    private static $GBP_2_36;
    /** @var  Money */
    private static $GBP_5_78;
    /** @var  Money */
    private static $GBP_M1_23;
    /** @var  Money */
    private static $GBP_M5_78;
    /** @var  Money */
    private static $JPY_423;
    /** @var  Money */
    private static $USD_1_23;
    /** @var  Money */
    private static $USD_2_34;
    /** @var  Money */
    private static $USD_2_35;

    public static function setUpBeforeClass()
    {
        (new CsvCurrencyDataProvider())->registerCurrencies();

        self::$GBP = Currency::of('GBP');
        self::$EUR = Currency::of('EUR');
        self::$USD = Currency::of('USD');
        self::$JPY = Currency::of('JPY');

        self::$GBP_0_00 = Money::parse("GBP 0.00");
        self::$GBP_1_23 = Money::parse("GBP 1.23");
        self::$GBP_2_33 = Money::parse("GBP 2.33");
        self::$GBP_2_34 = Money::parse("GBP 2.34");
        self::$GBP_2_35 = Money::parse("GBP 2.35");
        self::$GBP_2_36 = Money::parse("GBP 2.36");
        self::$GBP_5_78 = Money::parse("GBP 5.78");
        self::$GBP_M1_23 = Money::parse("GBP -1.23");
        self::$GBP_M5_78 = Money::parse("GBP -5.78");
        self::$JPY_423 = Money::parse("JPY 423");
        self::$USD_1_23 = Money::parse("USD 1.23");
        self::$USD_2_34 = Money::parse("USD 2.34");
        self::$USD_2_35 = Money::parse("USD 2.35");
    }


    public function test_factory_of_Currency_BigDecimal()
    {
        $test = Money::of(self::$GBP, self::BIGDEC_2_34);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 234);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }

    public function test_factory_of_Currency_BigDecimal_correctScale()
    {
        $test = Money::of(self::$GBP, self::BIGDEC_2_3);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 230);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_BigDecimal_invalidScaleGBP()
    {
        Money::of(self::$GBP, self::BIGDEC_2_345);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_BigDecimal_invalidScaleJPY()
    {
        Money::of(self::$JPY, self::BIGDEC_2_3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_BigDecimal_nullBigDecimal()
    {
        Money::of(self::$GBP, null);
    }

    public function test_factory_of_Currency_BigDecimal_GBP_RoundingMode_DOWN()
    {
        $test = Money::of(self::$GBP, self::BIGDEC_2_34, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 234);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }

    public function test_factory_of_Currency_BigDecimal_JPY_RoundingMode_DOWN()
    {
        $test = Money::of(self::$JPY, self::BIGDEC_2_34, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$JPY);
        $this->assertEquals($test->getAmountMinorInt(), 2);
        $this->assertEquals($test->getAmount()->scale(), 0);
    }

    public function test_factory_of_Currency_BigDecimal_JPY_RoundingMode_UP()
    {
        $test = Money::of(self::$JPY, self::BIGDEC_2_34, RoundingMode::UP);
        $this->assertEquals($test->getCurrency(), self::$JPY);
        $this->assertEquals($test->getAmountMinorInt(), 3);
        $this->assertEquals($test->getAmount()->scale(), 0);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_BigDecimal_RoundingMode_UNNECESSARY()
    {
        Money::of(self::$JPY, self::BIGDEC_2_34, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_BigDecimal_RoundingMode_nullBigDecimal()
    {
        Money::of(self::$GBP, null, RoundingMode::DOWN);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_BigDecimal_RoundingMode_nullRoundingMode()
    {
        Money::of(self::$GBP, self::BIGDEC_2_34, null);
    }

    public function test_factory_of_Currency_double()
    {
        $test = Money::of(self::$GBP, 2.34);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 234);
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_correctScale()
    {
        $test = Money::of(self::$GBP, 2.3);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 230);
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_trailingZero1()
    {
        $test = Money::of(self::$GBP, 1.230);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(123, 2));
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_trailingZero2()
    {
        $test = Money::of(self::$GBP, 1.20);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(120, 2));
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_medium()
    {
        $test = Money::of(self::$GBP, 2000);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(200000, 2));
        $this->assertEquals($test->getScale(), 2);
    }

    public function test_factory_of_Currency_double_big()
    {
        $test = Money::of(self::$GBP, 200000000.0);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmount(), BigDecimal::ofUnscaledValue(20000000000, 2));
        $this->assertEquals($test->getScale(), 2);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_double_invalidScaleGBP()
    {
        Money::of(self::$GBP, 2.345);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_double_invalidScaleJPY()
    {
        Money::of(self::$JPY, 2.3);
    }

    public function test_factory_of_Currency_double_GBP_RoundingMode_DOWN()
    {
        $test = Money::of(self::$GBP, 2.34, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 234);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }

    public function test_factory_of_Currency_double_JPY_RoundingMode_DOWN()
    {
        $test = Money::of(self::$JPY, 2.34, RoundingMode::DOWN);
        $this->assertEquals($test->getCurrency(), self::$JPY);
        $this->assertEquals($test->getAmountMinorInt(), 2);
        $this->assertEquals($test->getAmount()->scale(), 0);
    }

    public function test_factory_of_Currency_double_JPY_RoundingMode_UP()
    {
        $test = Money::of(self::$JPY, 2.34, RoundingMode::UP);
        $this->assertEquals($test->getCurrency(), self::$JPY);
        $this->assertEquals($test->getAmountMinorInt(), 3);
        $this->assertEquals($test->getAmount()->scale(), 0);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_factory_of_Currency_double_RoundingMode_UNNECESSARY()
    {
        Money::of(self::$JPY, 2.34, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_double_RoundingMode_nullRoundingMode()
    {
        Money::of(self::$GBP, 2.34, null);
    }

    public function test_factory_ofMajor_Currency_long()
    {
        $test = Money::ofMajor(self::$GBP, 234);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 23400);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }


    public function test_factory_ofMinor_Currency_long()
    {
        $test = Money::ofMinor(self::$GBP, 234);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 234);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }


    public function test_factory_zero_Currency()
    {
        $test = Money::zero(self::$GBP);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 0);
        $this->assertEquals($test->getAmount()->scale(), 2);
    }

    /*
                //-----------------------------------------------------------------------
                // from(BigMoneyProvider)
                //-----------------------------------------------------------------------
                public void test_factory_from_BigMoneyProvider() {
            Money test = Money.of(BigMoney.parse("GBP 104.23"));
                    assertEquals(test.getCurrencyUnit(), GBP);
                    assertEquals(test.getAmountMinorInt(), 10423);
                    assertEquals(test.getAmount().scale(), 2);
                }

                public void test_factory_from_BigMoneyProvider_fixScale() {
            Money test = Money.of(BigMoney.parse("GBP 104.2"));
                    assertEquals(test.getCurrencyUnit(), GBP);
                    assertEquals(test.getAmountMinorInt(), 10420);
                    assertEquals(test.getAmount().scale(), 2);
                }

                @Test(expectedExceptions = ArithmeticException.class)
                public void test_factory_from_BigMoneyProvider_invalidCurrencyScale() {
            Money.of(BigMoney.parse("GBP 104.235"));
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_from_BigMoneyProvider_nullBigMoneyProvider() {
            Money.of((BigMoneyProvider) null);
                }

                //-----------------------------------------------------------------------
                // from(BigMoneyProvider,RoundingMode)
                //-----------------------------------------------------------------------
                public void test_factory_from_BigMoneyProvider_RoundingMode() {
            Money test = Money.of(BigMoney.parse("GBP 104.235"), RoundingMode.HALF_EVEN);
                    assertEquals(test.getCurrencyUnit(), GBP);
                    assertEquals(test.getAmountMinorInt(), 10424);
                    assertEquals(test.getAmount().scale(), 2);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_from_BigMoneyProvider_RoundingMode_nullBigMoneyProvider() {
            Money.of((BigMoneyProvider) null, RoundingMode.DOWN);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_factory_from_BigMoneyProvider_RoundingMode_nullRoundingMode() {
            Money.of(BigMoney.parse("GBP 104.235"), (RoundingMode) null);
                }*/


    /*
     * total()
     */
    public function test_factory_total_varargs_1()
    {
        $test = Money::total(self::$GBP_1_23);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 123);
    }

    public function test_factory_total_array_1()
    {
        $test = Money::total(...[self::$GBP_1_23]);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 123);
    }

    public function test_factory_total_varargs_3()
    {
        $test = Money::total(self::$GBP_1_23, self::$GBP_2_33, self::$GBP_2_36);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 592);
    }

    public function test_factory_total_array_3()
    {
        $test = Money::total(...[self::$GBP_1_23, self::$GBP_2_33, self::$GBP_2_36]);
        $this->assertEquals($test->getCurrency(), self::$GBP);
        $this->assertEquals($test->getAmountMinorInt(), 592);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_total_varargs_empty()
    {
        Money::total();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_total_array_empty()
    {
        Money::total(...[]);
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_factory_total_varargs_currenciesDiffer()
    {
        try {
            Money::total(self::$GBP_2_33, self::$JPY_423);
        } catch (CurrencyMismatchException $e) {
            $this->assertEquals($e->getFirstCurrency(), self::$GBP);
            $this->assertEquals($e->getSecondCurrency(), self::$JPY);
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_factory_total_array_currenciesDiffer()
    {
        try {
            Money::total(...[self::$GBP_2_33, self::$JPY_423]);
        } catch (CurrencyMismatchException $e) {
            $this->assertEquals($e->getFirstCurrency(), self::$GBP);
            $this->assertEquals($e->getSecondCurrency(), self::$JPY);
            throw $e;
        }
    }

    /*
       //-----------------------------------------------------------------------
       // total(CurrencyUnit,Money...)
       //-----------------------------------------------------------------------
       public void test_factory_total_CurrencyUnitVarargs_1() {
   Money test = Money.total(GBP, GBP_1_23);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 123);
       }

       public void test_factory_total_CurrencyUnitArray_1() {
   Money[] array = new Money[] {GBP_1_23};
           Money test = Money.total(GBP, array);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 123);
       }

       public void test_factory_total_CurrencyUnitVarargs_3() {
   Money test = Money.total(GBP, GBP_1_23, GBP_2_33, GBP_2_36);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 592);
       }

       public void test_factory_total_CurrencyUnitArray_3() {
   Money[] array = new Money[] {GBP_1_23, GBP_2_33, GBP_2_36};
           Money test = Money.total(GBP, array);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 592);
       }

       public void test_factory_total_CurrencyUnitVarargs_empty() {
   Money test = Money.total(GBP);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 0);
       }

       public void test_factory_total_CurrencyUnitArray_empty() {
   Money[] array = new Money[0];
           Money test = Money.total(GBP, array);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 0);
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitVarargs_currenciesDiffer() {
           try {
               Money.total(GBP, JPY_423);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitArray_currenciesDiffer() {
           try {
               Money[] array = new Money[] {JPY_423};
               Money.total(GBP, array);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitVarargs_currenciesDifferInArray() {
           try {
               Money.total(GBP, GBP_2_33, JPY_423);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitArray_currenciesDifferInArray() {
           try {
               Money[] array = new Money[] {GBP_2_33, JPY_423};
               Money.total(GBP, array);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitVarargs_nullFirst() {
   Money.total(GBP, null, GBP_2_33, GBP_2_36);
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitArray_nullFirst() {
   Money[] array = new Money[] {null, GBP_2_33, GBP_2_36};
           Money.total(GBP, array);
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitVarargs_nullNotFirst() {
   Money.total(GBP, GBP_2_33, null, GBP_2_36);
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitArray_nullNotFirst() {
   Money[] array = new Money[] {GBP_2_33, null, GBP_2_36};
           Money.total(GBP, array);
       }

       //-----------------------------------------------------------------------
       // total(CurrencyUnit,Iterable)
       //-----------------------------------------------------------------------
       public void test_factory_total_CurrencyUnitIterable() {
   Iterable<Money> iterable = Arrays.asList(GBP_1_23, GBP_2_33, GBP_2_36);
           Money test = Money.total(GBP, iterable);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 592);
       }

       public void test_factory_total_CurrencyUnitIterable_empty() {
   Iterable<Money> iterable = Collections.emptyList();
           Money test = Money.total(GBP, iterable);
           assertEquals(test.getCurrencyUnit(), GBP);
           assertEquals(test.getAmountMinorInt(), 0);
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitIterable_currenciesDiffer() {
           try {
               Iterable<Money> iterable = Arrays.asList(JPY_423);
               Money.total(GBP, iterable);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = CurrencyMismatchException.class)
       public void test_factory_total_CurrencyUnitIterable_currenciesDifferInIterable() {
           try {
               Iterable<Money> iterable = Arrays.asList(GBP_2_33, JPY_423);
               Money.total(GBP, iterable);
           } catch (CurrencyMismatchException ex) {
       assertEquals(ex.getFirstCurrency(), GBP);
       assertEquals(ex.getSecondCurrency(), JPY);
       throw ex;
   }
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitIterable_nullFirst() {
   Iterable<Money> iterable = Arrays.asList(null, GBP_2_33, GBP_2_36);
           Money.total(GBP, iterable);
       }

       @Test(expectedExceptions = NullPointerException.class)
       public void test_factory_total_CurrencyUnitIterable_nullNotFirst() {
   Iterable<Money> iterable = Arrays.asList(GBP_2_33, null, GBP_2_36);
           Money.total(GBP, iterable);
       }
*/

    /*
     * parse()
     */

    public function parseProvider()
    {
        (new CsvCurrencyDataProvider())->registerCurrencies();

        return [
            ["GBP 2.43", Currency::of('GBP'), 243],
            ["GBP +12.57", Currency::of('GBP'), 1257],
            ["GBP -5.87", Currency::of('GBP'), -587],
            ["GBP 0.99", Currency::of('GBP'), 99],
            // TODO ["GBP .99", Currency::of('GBP'), 99],
            //["GBP +.99", Currency::of('GBP'), 99],
            ["GBP +0.99", Currency::of('GBP'), 99],
            //["GBP -.99", Currency::of('GBP'), -99],
            ["GBP -0.99", Currency::of('GBP'), -99],
            ["GBP 0", Currency::of('GBP'), 0],
            ["GBP 2", Currency::of('GBP'), 200],
            //["GBP 123.", Currency::of('GBP'), 12300],
            ["GBP3", Currency::of('GBP'), 300],
            ["GBP3.10", Currency::of('GBP'), 310],
            ["GBP  3.10", Currency::of('GBP'), 310],
            ["GBP   3.10", Currency::of('GBP'), 310],
            ["GBP                           3.10", Currency::of('GBP'), 310],
        ];
    }


    /**
     * @param $str
     * @param Currency $currency
     * @param $amount
     * @dataProvider parseProvider
     */
    public function test_factory_parse($str, Currency $currency, $amount)
    {
        $test = Money::parse($str);
        $this->assertEquals($test->getCurrency(), $currency);
        $this->assertEquals($test->getAmountMinorInt(), $amount);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_parse_String_tooShort()
    {
        Money::parse("GBP ");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_parse_String_badCurrency()
    {
        Money::parse("GBX 2.34");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_parse_String_nullString()
    {
        Money::parse(null);
    }

    /*
                //-----------------------------------------------------------------------
                // nonNull(Money,CurrencyUnit)
                //-----------------------------------------------------------------------
                public void test_nonNull_MoneyCurrencyUnit_nonNull() {
            Money test = Money.nonNull(GBP_1_23, GBP);
                    assertSame(test, GBP_1_23);
                }

                @Test(expectedExceptions = CurrencyMismatchException.class)
                public void test_nonNull_MoneyCurrencyUnit_nonNullCurrencyMismatch() {
                    try {
                        Money.nonNull(GBP_1_23, JPY);
                    } catch (CurrencyMismatchException ex) {
                assertEquals(ex.getFirstCurrency(), GBP);
                assertEquals(ex.getSecondCurrency(), JPY);
                throw ex;
            }
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_nonNull_MoneyCurrencyUnit_nonNull_nullCurrency() {
            Money.nonNull(GBP_1_23, null);
                }

                public void test_nonNull_MoneyCurrencyUnit_null() {
            Money test = Money.nonNull(null, GBP);
                    assertEquals(test, GBP_0_00);
                }

                @Test(expectedExceptions = NullPointerException.class)
                public void test_nonNull_MoneyCurrencyUnit_null_nullCurrency() {
            Money.nonNull(null, null);
                }
*/

    /*
     * serialize()
     */
    public function test_serialization()
    {
        $a = self::$GBP_2_34;
        $serialized = serialize($a);

        $this->assertEquals($a, unserialize($serialized));
    }

    /*
     * getCurrency()
     */
    public function test_getCurrency_GBP()
    {
        $this->assertEquals(self::$GBP_2_34->getCurrency(), self::$GBP);
    }

    public function test_getCurrency_EUR()
    {
        $this->assertEquals(Money::parse("EUR -5.78")->getCurrency(), self::$EUR);
    }


    public function test_withCurrency_Currency()
    {
        $test = self::$GBP_2_34->withCurrency(self::$USD);
        $this->assertEquals($test->__toString(), "USD 2.34");
    }

    public function test_withCurrency_Currency_same()
    {
        $test = self::$GBP_2_34->withCurrency(self::$GBP);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withCurrency_Currency_scaleProblem()
    {
        self::$GBP_2_34->withCurrency(self::$JPY);
    }

    public function test_withCurrency_CurrencyRoundingMode_DOWN()
    {
        $test = self::$GBP_2_34->withCurrency(self::$JPY, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "JPY 2");
    }

    public function test_withCurrency_CurrencyRoundingMode_UP()
    {
        $test = self::$GBP_2_34->withCurrency(self::$JPY, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "JPY 3");
    }

    public function test_withCurrency_CurrencyRoundingMode_same()
    {
        $test = self::$GBP_2_34->withCurrency(self::$GBP, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withCurrency_CurrencyRoundingMode_UNECESSARY()
    {
        self::$GBP_2_34->withCurrency(self::$JPY, RoundingMode::UNNECESSARY);
    }

    /*
     * getScale()
     */

    public function test_getScale_GBP()
    {
        $this->assertEquals(self::$GBP_2_34->getScale(), 2);
    }

    public function test_getScale_JPY()
    {
        $this->assertEquals(self::$JPY_423->getScale(), 0);
    }

    public function test_getAmount_positive()
    {
        $this->assertEquals(self::$GBP_2_34->getAmount(), self::BIGDEC_2_34);
    }

    public function test_getAmount_negative()
    {
        $this->assertEquals(self::$GBP_M5_78->getAmount(), self::BIGDEC_M5_78);
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

    /* TODO
            //@Test(expectedExceptions = ArithmeticException.class)
            public function test_getAmountMajorInt_tooBigPositive() {
        GBP_INT_MAX_MAJOR_PLUS1.getAmountMajorInt();
            }

            //@Test(expectedExceptions = ArithmeticException.class)
            public function test_getAmountMajorInt_tooBigNegative() {
        GBP_INT_MIN_MAJOR_MINUS1.getAmountMajorInt();
            }*/


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

    /* TODO
            //@Test(expectedExceptions = ArithmeticException.class)
            public function test_getAmountMinorInt_tooBigPositive() {
        GBP_INT_MAX_PLUS1.getAmountMinorInt();
            }

            //@Test(expectedExceptions = ArithmeticException.class)
            public function test_getAmountMinorInt_tooBigNegative() {
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
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_M5_78);
        $this->assertEquals($test->__toString(), "GBP -5.78");
    }

    public function test_withAmount_BigDecimal_same()
    {
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_2_34);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withAmount_BigDecimal_invalidScale()
    {
        self::$GBP_2_34->withAmount(BigDecimal::of("2.345"));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_withAmount_BigDecimal_nullBigDecimal()
    {
        self::$GBP_2_34->withAmount(null);
    }

    public function test_withAmount_BigDecimalRoundingMode()
    {
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_M5_78, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP -5.78");
    }

    public function test_withAmount_BigDecimalRoundingMode_same()
    {
        $test = self::$GBP_2_34->withAmount(self::BIGDEC_2_34, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_withAmount_BigDecimalRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->withAmount(BigDecimal::of("2.355"), RoundingMode::DOWN);
        $this->assertEquals($test, self::$GBP_2_35);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withAmount_BigDecimalRoundingMode_roundUnecessary()
    {
        self::$GBP_2_34->withAmount(BigDecimal::of("2.345"), RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_withAmount_BigDecimalRoundingMode_nullBigDecimal()
    {
        self::$GBP_2_34->withAmount(null, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_withAmount_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_2_34->withAmount(self::BIGDEC_2_34, null);
    }


    public function test_withAmount_double()
    {
        $test = self::$GBP_2_34->withAmount(-5.78);
        $this->assertEquals($test->__toString(), "GBP -5.78");
    }

    public function test_withAmount_double_same()
    {
        $test = self::$GBP_2_34->withAmount(2.34);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withAmount_double_invalidScale()
    {
        self::$GBP_2_34->withAmount(2.345);
    }

    public function test_withAmount_doubleRoundingMode()
    {
        $test = self::$GBP_2_34->withAmount(-5.78, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP -5.78");
    }

    public function test_withAmount_doubleRoundingMode_same()
    {
        $test = self::$GBP_2_34->withAmount(2.34, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_withAmount_doubleRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->withAmount(2.355, RoundingMode::DOWN);
        $this->assertEquals($test, self::$GBP_2_35);
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_withAmount_doubleRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->withAmount(2.345, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_withAmount_doubleRoundingMode_nullRoundingMode()
    {
        self::$GBP_2_34->withAmount(self::BIGDEC_2_34, null);
    }

    /*
                        //-----------------------------------------------------------------------
                        // plus(Iterable)
                        //-----------------------------------------------------------------------
                        public void test_plus_Iterable() {
                    Iterable<Money> iterable = Arrays.asList(GBP_2_33, GBP_1_23);
                            Money test = GBP_2_34.plus(iterable);
                            assertEquals(test.toString(), "GBP 5.90");
                        }

                        public void test_plus_Iterable_zero() {
                    Iterable<Money> iterable = Arrays.asList(GBP_0_00);
                            Money test = GBP_2_34.plus(iterable);
                            assertSame(test, GBP_2_34);
                        }

                        @Test(expectedExceptions = CurrencyMismatchException.class)
                        public void test_plus_Iterable_currencyMismatch() {
                            try {
                                Iterable<Money> iterable = Arrays.asList(GBP_2_33, JPY_423);
                                GBP_M5_78.plus(iterable);
                            } catch (CurrencyMismatchException ex) {
                        assertEquals(ex.getFirstCurrency(), GBP);
                        assertEquals(ex.getSecondCurrency(), JPY);
                        throw ex;
                    }
                        }

                        @Test(expectedExceptions = NullPointerException.class)
                        public void test_plus_Iterable_nullEntry() {
                    Iterable<Money> iterable = Arrays.asList(GBP_2_33, null);
                            GBP_M5_78.plus(iterable);
                        }

                        @Test(expectedExceptions = NullPointerException.class)
                        public void test_plus_Iterable_nullIterable() {
                    GBP_M5_78.plus((Iterable<Money>) null);
                        }*/


    /*
     * plus()
     */

    public function test_plus_Money_zero()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_0_00);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_Money_positive()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_1_23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plus_Money_negative()
    {
        $test = self::$GBP_2_34->plus(self::$GBP_M1_23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }


    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_plus_Money_currencyMismatch()
    {
        try {
            self::$GBP_M5_78->plus(self::$USD_1_23);
        } catch (CurrencyMismatchException $e) {
            $this->assertEquals($e->getFirstCurrency(), self::$GBP);
            $this->assertEquals($e->getSecondCurrency(), self::$USD);
            throw $e;
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plus_Money_nullMoney()
    {
        self::$GBP_M5_78->plus(null);
    }


    public function test_plus_BigDecimal_zero()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::zero());
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_BigDecimal_positive()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("1.23"));
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plus_BigDecimal_negative()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("-1.23"));
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plus_BigDecimal_invalidScale()
    {
        self::$GBP_2_34->plus(BigDecimal::of("1.235"));
    }

    public function test_plus_BigDecimalRoundingMode_zero()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::zero(), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plus_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("-1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_plus_BigDecimalRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->plus(BigDecimal::of("1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }


    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plus_BigDecimalRoundingMode_roundUnecessary()
    {
        self::$GBP_2_34->plus(BigDecimal::of("1.235"), RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plus_BigDecimalRoundingMode_nullBigDecimal()
    {
        self::$GBP_M5_78->plus(null, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_plus_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_M5_78->plus(self::BIGDEC_2_34, null);
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
    }

    public function test_plus_double_negative()
    {
        $test = self::$GBP_2_34->plus(-1.23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plus_double_invalidScale()
    {
        self::$GBP_2_34->plus(1.235);
    }


    public function test_plus_doubleRoundingMode_zero()
    {
        $test = self::$GBP_2_34->plus(0, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_plus_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_34->plus(1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_plus_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_34->plus(-1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_plus_doubleRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->plus(1.235, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_plus_doubleRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->plus(1.235, RoundingMode::UNNECESSARY);
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
    }

    public function test_plusMajor_negative()
    {
        $test = self::$GBP_2_34->plusMajor(-123);
        $this->assertEquals($test->__toString(), "GBP -120.66");
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
    }

    public function test_plusMinor_negative()
    {
        $test = self::$GBP_2_34->plusMinor(-123);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    /*
                            //-----------------------------------------------------------------------
                            // minus(Iterable)
                            //-----------------------------------------------------------------------
                            public void test_minus_Iterable() {
                        Iterable<Money> iterable = Arrays.asList(GBP_2_33, GBP_1_23);
                                Money test = GBP_2_34.minus(iterable);
                                assertEquals(test.toString(), "GBP -1.22");
                            }

                            public void test_minus_Iterable_zero() {
                        Iterable<Money> iterable = Arrays.asList(GBP_0_00);
                                Money test = GBP_2_34.minus(iterable);
                                assertSame(test, GBP_2_34);
                            }

                            @Test(expectedExceptions = CurrencyMismatchException.class)
                            public void test_minus_Iterable_currencyMismatch() {
                                try {
                                    Iterable<Money> iterable = Arrays.asList(GBP_2_33, JPY_423);
                                    GBP_M5_78.minus(iterable);
                                } catch (CurrencyMismatchException ex) {
                            assertEquals(ex.getFirstCurrency(), GBP);
                            assertEquals(ex.getSecondCurrency(), JPY);
                            throw ex;
                        }
                            }

                            @Test(expectedExceptions = NullPointerException.class)
                            public void test_minus_Iterable_nullEntry() {
                        Iterable<Money> iterable = Arrays.asList(GBP_2_33, null);
                                GBP_M5_78.minus(iterable);
                            }

                            @Test(expectedExceptions = NullPointerException.class)
                            public void test_minus_Iterable_nullIterable() {
                        GBP_M5_78.minus((Iterable<Money>) null);
                            }*/

    /*
     * minus()
     */

    public function test_minus_Money_zero()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_0_00);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_Money_positive()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_1_23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minus_Money_negative()
    {
        $test = self::$GBP_2_34->minus(self::$GBP_M1_23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \SmartGecko\Money\CurrencyMismatchException
     */
    public function test_minus_Money_currencyMismatch()
    {
        try {
            self::$GBP_M5_78->minus(self::$USD_1_23);
        } catch (CurrencyMismatchException $e) {
            $this->assertEquals($e->getFirstCurrency(), self::$GBP);
            $this->assertEquals($e->getSecondCurrency(), self::$USD);
            throw $e;
        }
    }

    public function test_minus_BigDecimal_zero()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::zero());
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_BigDecimal_positive()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("1.23"));
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minus_BigDecimal_negative()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("-1.23"));
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minus_BigDecimal_invalidScale()
    {
        self::$GBP_2_34->minus(BigDecimal::of("1.235"));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minus_BigDecimal_nullBigDecimal()
    {
        self::$GBP_M5_78->minus(null);
    }

    public function test_minus_BigDecimalRoundingMode_zero()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::zero(), RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minus_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("-1.23"), RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_minus_BigDecimalRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->minus(BigDecimal::of("1.235"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 1.10");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minus_BigDecimalRoundingMode_roundUnnecessary()
    {
        self::$GBP_2_34->minus(BigDecimal::of("1.235"), RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minus_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_M5_78->minus(self::BIGDEC_2_34, null);
    }


    public function test_minus_double_zero()
    {
        $test = self::$GBP_2_34->minus(0.0);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_double_positive()
    {
        $test = self::$GBP_2_34->minus(1.23);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minus_double_negative()
    {
        $test = self::$GBP_2_34->minus(-1.23);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minus_double_invalidScale()
    {
        self::$GBP_2_34->minus(1.235);
    }


    public function test_minus_doubleRoundingMode_zero()
    {
        $test = self::$GBP_2_34->minus(0.0, RoundingMode::UNNECESSARY);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_minus_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_34->minus(1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 1.11");
    }

    public function test_minus_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_34->minus(-1.23, RoundingMode::UNNECESSARY);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    public function test_minus_doubleRoundingMode_roundDown()
    {
        $test = self::$GBP_2_34->minus(1.235, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 1.10");
    }

    /**
     * @expectedException \Brick\Math\Exception\RoundingNecessaryException
     */
    public function test_minus_doubleRoundingMode_roundUnecessary()
    {
        self::$GBP_2_34->minus(1.235, RoundingMode::UNNECESSARY);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_minus_doubleRoundingMode_nullRoundingMode()
    {
        self::$GBP_M5_78->minus(2.34, null);
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
    }

    public function test_minusMajor_negative()
    {
        $test = self::$GBP_2_34->minusMajor(-123);
        $this->assertEquals($test->__toString(), "GBP 125.34");
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
    }

    public function test_minusMinor_negative()
    {
        $test = self::$GBP_2_34->minusMinor(-123);
        $this->assertEquals($test->__toString(), "GBP 3.57");
    }

    /*
     * multipliedBy()
     */

    public function test_multipliedBy_BigDecimalRoundingMode_one()
    {
        $test = self::$GBP_2_34->multipliedBy(BigDecimal::one(), RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multipliedBy_BigDecimalRoundingMode_positive()
    {
        $test = self::$GBP_2_33->multipliedBy(BigDecimal::of("2.5"), RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 5.82");
    }

    public function test_multipliedBy_BigDecimalRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_33->multipliedBy(BigDecimal::of("2.5"), RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 5.83");
    }

    public function test_multipliedBy_BigDecimalRoundingMode_negative()
    {
        $test = self::$GBP_2_33->multipliedBy(BigDecimal::of("-2.5"), RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -5.83");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_multipliedBy_BigDecimalRoundingMode_nullBigDecimal()
    {
        self::$GBP_5_78->multipliedBy(null, RoundingMode::DOWN);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_multipliedBy_BigDecimalRoundingMode_nullRoundingMode()
    {
        self::$GBP_5_78->multipliedBy(BigDecimal::of("2.5"), null);
    }

    public function test_multipliedBy_doubleRoundingMode_one()
    {
        $test = self::$GBP_2_34->multipliedBy(1.0, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multipliedBy_doubleRoundingMode_positive()
    {
        $test = self::$GBP_2_33->multipliedBy(2.5, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 5.82");
    }

    public function test_multipliedBy_doubleRoundingMode_positive_halfUp()
    {
        $test = self::$GBP_2_33->multipliedBy(2.5, RoundingMode::HALF_UP);
        $this->assertEquals($test->__toString(), "GBP 5.83");
    }

    public function test_multipliedBy_doubleRoundingMode_negative()
    {
        $test = self::$GBP_2_33->multipliedBy(-2.5, RoundingMode::FLOOR);
        $this->assertEquals($test->__toString(), "GBP -5.83");
    }


    public function test_multipliedBy_long_one()
    {
        $test = self::$GBP_2_34->multipliedBy(1);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_multipliedBy_long_positive()
    {
        $test = self::$GBP_2_34->multipliedBy(3);
        $this->assertEquals($test->__toString(), "GBP 7.02");
    }

    public function test_multipliedBy_long_negative()
    {
        $test = self::$GBP_2_34->multipliedBy(-3);
        $this->assertEquals($test->__toString(), "GBP -7.02");
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

    public function test_dividedBy_doubleRoundingMode_one()
    {
        $test = self::$GBP_2_34->dividedBy(1.0, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
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


    public function test_dividedBy_long_one()
    {
        $test = self::$GBP_2_34->dividedBy(1, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_dividedBy_long_positive()
    {
        $test = self::$GBP_2_34->dividedBy(3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.78");
    }

    public function test_dividedBy_long_positive_roundDown()
    {
        $test = self::$GBP_2_35->dividedBy(3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 0.78");
    }

    public function test_dividedBy_long_positive_roundUp()
    {
        $test = self::$GBP_2_35->dividedBy(3, RoundingMode::UP);
        $this->assertEquals($test->__toString(), "GBP 0.79");
    }

    public function test_dividedBy_long_negative()
    {
        $test = self::$GBP_2_34->dividedBy(-3, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP -0.78");
    }

    /*
     * negated()
     */

    public function test_negated_positive()
    {
        $test = self::$GBP_2_34->negated();
        $this->assertEquals($test->__toString(), "GBP -2.34");
    }

    public function test_negated_negative()
    {
        $test = Money::parse("GBP -2.34")->negated();
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }

    /*
     * abs()
     */

    public function test_abs_positive()
    {
        $test = self::$GBP_2_34->abs();
        $this->assertSame($test, self::$GBP_2_34);
    }

    public function test_abs_negative()
    {
        $test = Money::parse("GBP -2.34")->abs();
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }

    /*
     * rounded()
     */

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

    /* TODO public function test_round_M1down()
    {
        $test = Money::parse("GBP 432.34")->rounded(-1, RoundingMode::DOWN);
        $this->assertEquals($test->__toString(), "GBP 430.00");
    }

    public function test_round_M1up()
    {
        $test = Money::parse("GBP 432.34")->rounded(-1, RoundingMode::UP);
        assertEquals($test->__toString(), "GBP 440.00");
    }*/

    public function test_round_3()
    {
        $test = self::$GBP_2_34->rounded(3, RoundingMode::DOWN);
        $this->assertSame($test, self::$GBP_2_34);
    }

    /*
                                    //-----------------------------------------------------------------------
                                    // convertedTo(BigDecimal,RoundingMode)
                                    //-----------------------------------------------------------------------
                                    public void test_convertedTo_BigDecimalRoundingMode_positive() {
                                Money test = GBP_2_33.convertedTo(EUR, new BigDecimal("2.5"), RoundingMode.DOWN);
                                        assertEquals(test.toString(), "EUR 5.82");
                                    }

                                    public void test_convertedTo_BigDecimalRoundingMode_positive_halfUp() {
                                Money test = GBP_2_33.convertedTo(EUR, new BigDecimal("2.5"), RoundingMode.HALF_UP);
                                        assertEquals(test.toString(), "EUR 5.83");
                                    }

                                    @Test(expectedExceptions = IllegalArgumentException.class)
                                    public void test_convertedTo_BigDecimalRoundingMode_negative() {
                                GBP_2_33.convertedTo(EUR, new BigDecimal("-2.5"), RoundingMode.FLOOR);
                                    }

                                    @Test(expectedExceptions = IllegalArgumentException.class)
                                    public void test_convertedTo_BigDecimalRoundingMode_sameCurrency() {
                                GBP_2_33.convertedTo(GBP, new BigDecimal("2.5"), RoundingMode.DOWN);
                                    }

                                    @Test(expectedExceptions = NullPointerException.class)
                                    public void test_convertedTo_BigDecimalRoundingMode_nullCurrency() {
                                GBP_5_78.convertedTo((CurrencyUnit) null, new BigDecimal("2"), RoundingMode.DOWN);
                                    }

                                    @Test(expectedExceptions = NullPointerException.class)
                                    public void test_convertedTo_BigDecimalRoundingMode_nullBigDecimal() {
                                GBP_5_78.convertedTo(EUR, (BigDecimal) null, RoundingMode.DOWN);
                                    }

                                    @Test(expectedExceptions = NullPointerException.class)
                                    public void test_convertedTo_BigDecimalRoundingMode_nullRoundingMode() {
                                GBP_5_78.convertedTo(EUR, new BigDecimal("2.5"), (RoundingMode) null);
                                    }*/

    /*
     * toMoney()
     */

    public function test_toBigMoney()
    {
        $this->assertEquals(self::$GBP_2_34->toBigMoney(), BigMoney::ofMinor(self::$GBP, 234));
    }

    /*
     * isSameCurrency()
     */
    public function test_isSameCurrency_Money_same()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(self::$GBP_2_35), true);
    }

    public function test_isSameCurrency_Money_different()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(self::$USD_2_34), false);
    }

    public function test_isSameCurrency_BigMoney_same()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(BigMoney::parse("GBP 2")), true);
    }

    public function test_isSameCurrency_BigMoney_different()
    {
        $this->assertEquals(self::$GBP_2_34->isSameCurrency(BigMoney::parse("USD 2")), false);
    }

    /*
     * compareTo()
     */
    public function test_compareTo_Money()
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

    public function test_compareTo_BigMoney()
    {
        $t = self::$GBP_2_35;
        $a = BigMoney::ofMinor(self::$GBP, 234);
        $b = BigMoney::ofMinor(self::$GBP, 235);
        $c = BigMoney::ofMinor(self::$GBP, 236);
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

    /*
     * isEqual()
     */
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
        $b = BigMoney::ofMinor(self::$GBP, 234);
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

    /*
     * isGreaterThan()
     */
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

    /*
     * isLessThan()
     */
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
        $test = Money::of(self::$GBP, self::BIGDEC_2_34);
        $this->assertEquals($test->__toString(), "GBP 2.34");
    }

    public function test_toString_negative()
    {
        $test = Money::of(self::$EUR, self::BIGDEC_M5_78);
        $this->assertEquals($test->__toString(), "EUR -5.78");
    }
}