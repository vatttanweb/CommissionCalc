<?php

namespace CommissionClaculator\Service;

class WithdrawPrivateFirstHandler extends AbstractHandler
{
    public function handle(string $type, string $value, string $id, string $date, array &$arr): float
    {
        if ($type === "withdrawPrivate" && empty($arr[$id])) {
            return $this->getFeeForFirstWithdraw($id, $value, $date, $arr);
        } else {
            return parent::handle($type, $value, $id, $date, $arr);
        }
    }

    private function getFeeForFirstWithdraw(string $id, string $value, $formatedDate, array &$arr)
    {
        $customer = new Customer();
        $customer = $this->resetCustomerValues($id, $customer, $value, $formatedDate);
        $arr[$id] = $customer;
        if (floatval($customer->value) > 1000) {
            return (floatval($value) - 1000) * 0.3 / 100;
        } else {
            return 0;
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