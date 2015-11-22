<?php

namespace SmartGecko\Money;

/**
 * Created by PhpStorm.
 * User: davidkalosi
 * Date: 17/11/15
 * Time: 13:50
 */
class CsvCurrencyDataProvider extends AbstractCurrencyDataProvider
{
    const REGEX_LINE = "/([A-Z]{3}),(-1|[0-9]{1,3}),(-1|[0-9]),([A-Z]*)#?.*/";

    /**
     * @inheritdoc
     */
    public function registerCurrencies()
    {
        $this->loadCurrenciesFromFile(__DIR__."/data/currencies.csv", true);
    }

    /**
     * Loads Currencies from a file
     *
     * @param string $fileName the file to load, not null
     * @param bool $isNecessary whether or not the file is necessary
     * @throws \RuntimeException if a necessary file is not found
     */
    private function loadCurrenciesFromFile($fileName, $isNecessary)
    {
        $file = new \SplFileObject($fileName);

        if (!$file->isReadable() && $isNecessary) {
            throw new \RuntimeException("Data file ".$fileName." not found");
        } elseif (!$file->isReadable()) {
            return;
        }

        while (!$file->eof()) {
            $line = $file->fgets();

            if (preg_match(self::REGEX_LINE, $line, $matches)) {
                $countryCodes = [];

                if (strlen($matches[4]) % 2 === 1) {
                    continue;
                }

                for ($i = 0; $i < strlen($matches[4]); $i+= 2) {
                    $countryCodes[] = substr($matches[4], $i, 2);
                }

                $this->registerCurrency($matches[1], intval($matches[2]), intval($matches[3]), $countryCodes);
            }
        }
    }

}