<?php

namespace CommissionClaculator\Service;

class WithdrawPrivateReptetiveHandler extends AbstractHandler
{
    public function handle(string $type, string $value, string $id, string $date, array &$arr): float
    {
        if ($type === "withdrawPrivate"&& !empty($arr[$id])) {
            return $this->getFeeForRepetetiveWithdraw($arr, $id, $date, $value);
        } else {
            return parent::handle($type, $value, $id, $date, $arr);
        }
    }

    private function getFeeForRepetetiveWithdraw(array &$arr, string $id, $formatedDate, string $value)
    {
        $customer = $arr[$id];
        $oneWeekDate = date('Y-m-d', strtotime('+7 days', strtotime($customer->date)));
        $remain = 1000 - $customer->value;
        $customer->value = $customer->value + floatval($value);
        $customer->count++;
        if ($oneWeekDate < $formatedDate) {
            $customer = $this->resetCustomerValues($id, $customer, $value, $formatedDate);
            $remain = 1000;
        }
        $arr[$id] = $customer;
//      Again in here we should use "Chain of Responsible" pattern but because I don't have time and I showed this
//         approach in multiple place I avoided to implement again.
        if ($customer->count > 3) {
            return (floatval($value)) * 0.3 / 100;
        } else {
            if ($remain < 0) {
                return floatval($value) * 0.3 / 100;
            }
            if (($remain - floatval($value)) < 0) {
                return (floatval($value) - $remain) * 0.3 / 100;
            } else {
                return 0;
            }
        }
    }

    private function resetCustomerValues(string $id, Customer $customer, string $value, $formatedDate): Customer
    {
        $customer->id = $id;
        $customer->count = 1;
        $customer->value = floatval($value);
        $customer->date = $formatedDate;
        return $customer;
    }

}