<?php

namespace CommissionClaculator\Service;

class WithdrawBusinessHandler extends AbstractHandler
{
    public function handle(string $type, string $value, string $id, string $date, array &$arr): float
    {
        if ($type === "withdrawBusiness") {
            return floatval($value) * 0.5 / 100;
        } else {
            return parent::handle($type, $value, $id, $date, $arr);
        }
    }
}