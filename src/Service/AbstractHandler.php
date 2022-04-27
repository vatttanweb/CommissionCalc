<?php

namespace CommissionClaculator\Service;

class AbstractHandler implements Handler
{
    private $nextHandler;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(string $type, string $value, string $id, string $date, array &$arr): float
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($type, $value, $id, $date, $arr);
        }
        return 0;
    }
}