<?php
/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 20:52
 */

namespace SmartGecko\Money\Test;


use SmartGecko\Money\CsvCurrencyDataProvider;
use SmartGecko\Money\Currency;

class CsvCurrencyDataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadCurrencies()
    {
        $subject = new CsvCurrencyDataProvider();
        $subject->registerCurrencies();

        $eur = Currency::of('EUR');

        $this->assertEquals('EUR', $eur->getCurrencyCode());
        $this->assertEquals(2, $eur->getDecimalPlaces());
        $this->assertCount(22, $eur->getCountryCodes());
    }
}