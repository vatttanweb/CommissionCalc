<?php

namespace CommissionClaculator\Service;

interface Handler
{
    public function setNext(Handler $handler): Handler;

    public function handle(string $type,string $value,string $id,string $date,array &$arr): float;
}