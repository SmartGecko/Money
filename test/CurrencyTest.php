<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 21:18
 */

namespace SmartGecko\Money\Test;

use SmartGecko\Money\Currency;
use SmartGecko\Money\InvalidCurrencyException;
use SmartGecko\Money\CsvCurrencyDataProvider;

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        (new CsvCurrencyDataProvider())->registerCurrencies();
    }

    public function testRegisteredCurrencies()
    {
        $currencies = Currency::registeredCurrencies();
        $found = false;
        foreach ($currencies as $currency) {
            if ($currency->getCode() === "GBP") {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /*

    public void test_registeredCurrencies_sorted() {
    List<CurrencyUnit> curList1 = CurrencyUnit.registeredCurrencies();
            List<CurrencyUnit> curList2 = CurrencyUnit.registeredCurrencies();
            Collections.sort(curList2);
            assertEquals(curList1, curList2);
        }*/

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisteredCurrencyNullCode()
    {
        Currency::registerCurrency(null, 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisteredCurrencyInvalidStringCodeEmpty()
    {
        Currency::registerCurrency("", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_1letter()
    {
        Currency::registerCurrency("A", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_2letters()
    {
        Currency::registerCurrency("AB", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_4letters()
    {
        Currency::registerCurrency("ABCD", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_lowerCase()
    {
        Currency::registerCurrency("xxA", 991, 2, ["xx"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_number()
    {
        Currency::registerCurrency("123", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidStringCode_dash()
    {
        Currency::registerCurrency("A-", 991, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidNumericCode_small()
    {
        Currency::registerCurrency("TST", -2, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidNumericCode_big()
    {
        Currency::registerCurrency("TST", 1000, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidDP_small()
    {
        Currency::registerCurrency("TST", 991, -2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_invalidDP_big()
    {
        Currency::registerCurrency("TST", 991, 10, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_nullCountry()
    {
        Currency::registerCurrency("TST", 991, 2, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_alreadyRegisteredCode()
    {
        Currency::registerCurrency("GBP", 991, 2, ["GB"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_alreadyRegisteredNumericCode()
    {
        Currency::registerCurrency("TST", 826, 2, ["TS"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_registeredCurrency_alreadyRegisteredCountry()
    {
        Currency::registerCurrency("GBX", 991, 2, ["GB"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_of_Currency_nullCurrency()
    {
        Currency::of(null);
    }


    public function test_factory_of_String()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->getCode(), "GBP");
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_of_String_unknownCurrency()
    {
        try {
            Currency::of("ABC");
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"ABC\"");
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_of_String_empty()
    {
        Currency::of("");
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_of_String_tooShort_unknown()
    {
        Currency::of("AB");
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_of_String_tooLong_unknown()
    {
        Currency::of("ABCD");
    }


    public function test_factory_ofNumericCode_String()
    {
        $test = Currency::ofNumericCode("826");
        $this->assertEquals($test->getCode(), "GBP");
    }

    public function test_factory_ofNumericCode_String_2char()
    {
        $test = Currency::ofNumericCode("051");
        $this->assertEquals($test->getCode(), "AMD");
    }

    public function test_factory_ofNumericCode_String_2charNoPad()
    {
        $test = Currency::ofNumericCode("51");
        $this->assertEquals($test->getCode(), "AMD");
    }


    public function test_factory_ofNumericCode_String_1char()
    {
        $test = Currency::ofNumericCode("008");
        $this->assertEquals($test->getCode(), "ALL");
    }

    public function test_factory_ofNumericCode_String_1charNoPad()
    {
        $test = Currency::ofNumericCode("8");
        $this->assertEquals($test->getCode(), "ALL");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_ofNumericCode_String_nullString()
    {
        Currency::ofNumericCode(null);
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_String_unknownCurrency()
    {
        try {
            Currency::ofNumericCode("111");
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"111\"");
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_String_negative()
    {
        Currency::ofNumericCode("-1");
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_String_empty()
    {
        try {
            Currency::ofNumericCode("");
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"\"");
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_String_tooLong()
    {
        try {
            Currency::ofNumericCode("1234");
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"1234\"");
            throw $e;
        }
    }


    public function test_factory_ofNumericCode_int()
    {
        $test = Currency::ofNumericCode(826);
        $this->assertEquals($test->getCode(), "GBP");
    }

    public function test_factory_ofNumericCode_int_2char()
    {
        $test = Currency::ofNumericCode(51);
        $this->assertEquals($test->getCode(), "AMD");
    }

    public function test_factory_ofNumericCode_int_1char()
    {
        $test = Currency::ofNumericCode(8);
        $this->assertEquals($test->getCode(), "ALL");
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_int_unknownCurrency()
    {
        try {
            Currency::ofNumericCode(111);
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"111\"");
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_int_negative()
    {
        try {
            Currency::ofNumericCode(-1);
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"-1\"");
            throw $e;
        }
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofNumericCode_int_tooLong()
    {
        try {
            Currency::ofNumericCode(1234);
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency \"1234\"");
            throw $e;
        }
    }


    public function test_factory_ofCountry_String()
    {
        $test = Currency::ofCountry("GB");
        $this->assertEquals($test->getCode(), "GBP");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_factory_ofCountry_String_nullString()
    {
        Currency::ofCountry(null);
    }

    /**
     * @expectedException \SmartGecko\Money\InvalidCurrencyException
     */
    public function test_factory_ofCountry_String_unknownCurrency()
    {
        try {
            Currency::ofCountry("gb");
        } catch (InvalidCurrencyException $e) {
            $this->assertEquals($e->getMessage(), "Unknown currency for country \"gb\"");
            throw $e;
        }
    }


    public function test_serialization()
    {
        $cu = Currency::of("GBP");
        $serialized = serialize($cu);

        $this->assertEquals(unserialize($serialized), $cu);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_serialization_invalidNumericCode()
    {
        $reflection = new \ReflectionClass(Currency::class);
        $cu = $reflection->newInstanceWithoutConstructor();

        $reflectionProp1 = new \ReflectionProperty($cu, 'code');
        $reflectionProp2 = new \ReflectionProperty($cu, 'numericCode');
        $reflectionProp3 = new \ReflectionProperty($cu, 'decimalPlaces');

        $reflectionProp1->setAccessible(true);
        $reflectionProp2->setAccessible(true);
        $reflectionProp3->setAccessible(true);

        $reflectionProp1->setValue($cu, 'GBP');
        $reflectionProp2->setValue($cu, 234);
        $reflectionProp3->setValue($cu, 2);

        try {
            unserialize(serialize($cu));
        } catch (\RuntimeException $e) {
            $this->assertEquals(
                $e->getMessage(),
                "Deserialization found a mismatch in the numeric code for currency GBP"
            );
            throw $e;
        }
    }


    /**
     * @expectedException \RuntimeException
     */
    public function test_serialization_invalidDecimalPlaces()
    {
        $reflection = new \ReflectionClass(Currency::class);
        $cu = $reflection->newInstanceWithoutConstructor();

        $reflectionProp1 = new \ReflectionProperty($cu, 'code');
        $reflectionProp2 = new \ReflectionProperty($cu, 'numericCode');
        $reflectionProp3 = new \ReflectionProperty($cu, 'decimalPlaces');

        $reflectionProp1->setAccessible(true);
        $reflectionProp2->setAccessible(true);
        $reflectionProp3->setAccessible(true);

        $reflectionProp1->setValue($cu, 'GBP');
        $reflectionProp2->setValue($cu, 826);
        $reflectionProp3->setValue($cu, 1);

        try {
            unserialize(serialize($cu));
        } catch (\RuntimeException $e) {
            $this->assertEquals(
                $e->getMessage(),
                "Deserialization found a mismatch in the decimal places for currency GBP"
            );
            throw $e;
        }
    }

    public function test_getCurrencyCode_GBP()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->getCode(), "GBP");
        $this->assertEquals($test->getCurrencyCode(), "GBP");
    }

    /*
                            //-----------------------------------------------------------------------
                            // getNumeric3Code()
                            //-----------------------------------------------------------------------
                            public void test_getNumeric3Code_GBP() {
                        CurrencyUnit test = CurrencyUnit.of("GBP");
                                assertEquals(test.getNumeric3Code(), "826");
                            }

                            public void test_getNumeric3Code_ALL() {
                        CurrencyUnit test = CurrencyUnit.of("ALL");
                                assertEquals(test.getNumeric3Code(), "008");
                            }

                            public void test_getNumeric3Code_AMD() {
                        CurrencyUnit test = CurrencyUnit.of("AMD");
                                assertEquals(test.getNumeric3Code(), "051");
                            }

                            public void test_getNumeric3Code_XFU() {
                        CurrencyUnit test = CurrencyUnit.of("XFU");
                                assertEquals(test.getNumeric3Code(), "");
                            }
*/

    public function test_getNumericCode_GBP()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->getNumericCode(), 826);
    }


    public function test_getCurrencyCodes_GBP()
    {
        $test = Currency::of("GBP")->getCountryCodes();

        $this->assertContains('GB', $test);
        $this->assertContains('IM', $test);
        $this->assertContains('JE', $test);
        $this->assertContains('GG', $test);
        $this->assertContains('GS', $test);
        $this->assertContains('IO', $test);
    }

    public function test_toString()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->__toString(), "GBP");
    }

    public function test_getDecimalPlaces_GBP()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->getDecimalPlaces(), 2);
    }

    public function test_getDecimalPlaces_JPY()
    {
        $test = Currency::of("JPY");
        $this->assertEquals($test->getDecimalPlaces(), 0);
    }

    public function test_getDecimalPlaces_XXX()
    {
        $test = Currency::of("XXX");
        $this->assertEquals($test->getDecimalPlaces(), 0);
    }


    public function test_isPseudoCurrency_GBP()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->isPseudoCurrency(), false);
    }

    public function test_isPseudoCurrency_JPY()
    {
        $test = Currency::of("JPY");
        $this->assertEquals($test->isPseudoCurrency(), false);
    }

    public function test_isPseudoCurrency_XXX()
    {
        $test = Currency::of("XXX");
        $this->assertEquals($test->isPseudoCurrency(), true);
    }

    public function test_getDefaultFractionDigits_GBP()
    {
        $test = Currency::of("GBP");
        $this->assertEquals($test->getDefaultFractionDigits(), 2);
    }

    public function test_getDefaultFractionDigits_JPY()
    {
        $test = Currency::of("JPY");
        $this->assertEquals($test->getDefaultFractionDigits(), 0);
    }

    public function test_getDefaultFractionDigits_XXX()
    {
        $test = Currency::of("XXX");
        $this->assertEquals($test->getDefaultFractionDigits(), -1);
    }

    /*
                                        //-----------------------------------------------------------------------
                                        // compareTo()
                                        //-----------------------------------------------------------------------
                                        public void test_compareTo() {
                                    CurrencyUnit a = CurrencyUnit.of("EUR");
                                            CurrencyUnit b = CurrencyUnit.of("GBP");
                                            CurrencyUnit c = CurrencyUnit.of("JPY");
                                            assertEquals(a.compareTo(a), 0);
                                            assertEquals(b.compareTo(b), 0);
                                            assertEquals(c.compareTo(c), 0);

                                            assertTrue(a.compareTo(b) < 0);
                                            assertTrue(b.compareTo(a) > 0);

                                            assertTrue(a.compareTo(c) < 0);
                                            assertTrue(c.compareTo(a) > 0);

                                            assertTrue(b.compareTo(c) < 0);
                                            assertTrue(c.compareTo(b) > 0);
                                        }

                                        @Test(expectedExceptions = NullPointerException.class)
                                        public void test_compareTo_null() {
                                    CurrencyUnit.of("EUR").compareTo(null);
                                        }

                            */
}