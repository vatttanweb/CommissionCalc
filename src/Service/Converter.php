<?php

namespace CommissionClaculator\Service;

class Converter
{
    const convertMoney = "https://developers.paysera.com/tasks/api/currency-exchange-rates";

    public function getCoefficient($money)
    {
        $json = file_get_contents(self::convertMoney);
        $decodedJson = json_decode($json);
        $coefficientArray = (array)$decodedJson->rates;
        $coefficient = $coefficientArray[$money];
        return $coefficient;
    }
    public function getTypeOfOrder($item): string
    {
        if ($item[3] == "deposit") {
            return "deposit";
        } else {
            if ($item[2] == "private") {
                return "withdrawPrivate";
            } else {
                return "withdrawBusiness";
            }
        }
    }
    public function roundCurrency(float $result):float{
        return (ceil($result * 100))/100;
    }
}