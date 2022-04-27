<?php

namespace CommissionClaculator\Service;

class DepositeHandler extends AbstractHandler
{
    public function handle(string $type, string $value, string $id, string $date, array &$arr): float
    {
        if ($type === "deposit") {
            return floatval($value) * 0.03 / 100;
        } else {
            return parent::handle($type, $value, $id, $date, $arr);
        }
    }
}