<?php
require "vendor/autoload.php";

use CommissionClaculator\Service\Converter;
use CommissionClaculator\Service\DepositeHandler;
use CommissionClaculator\Service\WithdrawBusinessHandler;
use CommissionClaculator\Service\WithdrawPrivateFirstHandler;
use CommissionClaculator\Service\WithdrawPrivateReptetiveHandler;

$depositHandler = arrangHandlers();
$pathOfFile = $argv[1];
$customerArr = array();
$customerArr = readAndHandleLines($pathOfFile, $depositHandler, $customerArr);

function arrangHandlers(): DepositeHandler
{
    $depositHandler = new DepositeHandler();
    $withdrawBusinessHandler = new WithdrawBusinessHandler();
    $withdrawPrivateFirstHandler = new WithdrawPrivateFirstHandler();
    $withdrawPrivateRepetetiveHandler = new WithdrawPrivateReptetiveHandler();
    $depositHandler->setNext($withdrawBusinessHandler)->setNext($withdrawPrivateFirstHandler)->setNext($withdrawPrivateRepetetiveHandler);
    return $depositHandler;
}

function readAndHandleLines($pathOfFile, DepositeHandler $depositHandler, array &$customerArr): array
{
    if (($open = fopen($pathOfFile, "r")) !== FALSE) {
        while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
            $date = $data[0];
            $id = $data[1];
            $value = $data[4];
            $money = $data[5];
            $converter = new Converter();
            $coefficient = $converter->getCoefficient($money);
            $value = $value / $coefficient;
            $type = $converter->getTypeOfOrder($data);
            $unroundResult = $depositHandler->handle($type, $value, $id, $date, $customerArr);
            $result = $converter->roundCurrency($unroundResult);
            print_r($result);
            print_r("\r\n");
        }
        fclose($open);
    }
    return $customerArr;
}