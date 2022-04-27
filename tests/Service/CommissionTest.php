<?php

namespace Tests\Service;

use CommissionClaculator\Service\Converter;
use CommissionClaculator\Service\Customer;
use CommissionClaculator\Service\DepositeHandler;
use CommissionClaculator\Service\WithdrawBusinessHandler;
use CommissionClaculator\Service\WithdrawPrivateFirstHandler;
use CommissionClaculator\Service\WithdrawPrivateReptetiveHandler;
use PHPUnit\Framework\TestCase;

class CommissionTest extends TestCase
{
    /** @test */
    public function resultShouldBeInCorrectFormat()
    {
        $converter = new Converter();
        $result = $converter->roundCurrency(0.023);
        $this->assertEquals($result, 0.03);
    }

    /** @test
     * @dataProvider typeProvider
     */
    public function testShouldExtractCorrectType($data, $result)
    {
        $converter = new Converter();
        $type = $converter->getTypeOfOrder($data);
        $this->assertEquals($result, $type);
    }

    /** @test */
    public function testShouldGetCoefficientCorrect()
    {
        $converter = new Converter();
        $result = $converter->getCoefficient("JPY");
        $this->assertEquals($result, 130.869977);
    }

    /** @test */
    public function depositHandlerShouldPass()
    {
        $handler = new DepositeHandler();
        $arr = array();
        $result = $handler->handle("deposit", "1200", "1", "2014-12-31", $arr);
        $this->assertEquals($result, 0.36);
    }

    /** @test */
    public function withdrawBusinessHandlerShouldPass()
    {
        $handler = new WithdrawBusinessHandler();
        $arr = array();
        $result = $handler->handle("withdrawBusiness", "1000", "1", "2014-12-31", $arr);
        $this->assertEquals($result, 5);
    }

    /** @test */
    public function withdrawPrivateFirstHandler_ForLessThan1000_ShouldPass()
    {
        $handler = new WithdrawPrivateFirstHandler();
        $arr = array();
        $result = $handler->handle("withdrawPrivate", "200", "1", "2014-12-31", $arr);
        $this->assertEquals($result, 0);
    }

    /** @test */
    public function withdrawPrivateRepetetiveHandler_ForMoreThan1000Remain_ShouldPass()
    {
        $handler = new WithdrawPrivateReptetiveHandler();
        $customer = new Customer();
        $customer->id=4;
        $customer->count=1;
        $customer->value=1200;
        $customer->date = "2014-12-31";
        $arr = array("4" =>$customer);
        $result = $handler->handle("withdrawPrivate", "1200", "4", "2014-12-31", $arr);
        $this->assertEquals($result, 3.6,'',0.1);
    }
    /** @test */
    public function withdrawPrivateRepetetiveHandler_ForLessThan1000Remain_ShouldPass()
    {
        $handler = new WithdrawPrivateReptetiveHandler();
        $customer = new Customer();
        $customer->id=4;
        $customer->count=1;
        $customer->value=200;
        $customer->date = "2014-12-31";
        $arr = array("4" =>$customer);
        $result = $handler->handle("withdrawPrivate", "1100", "4", "2014-12-31", $arr);
        $this->assertEquals($result, 0.9,'',0.1);
    }
    /** @test */
    public function withdrawPrivateRepetetiveHandler_ForMoreThan3Times_ShouldPass()
    {
        $handler = new WithdrawPrivateReptetiveHandler();
        $customer = new Customer();
        $customer->id=4;
        $customer->count=4;
        $customer->value=200;
        $customer->date = "2014-12-31";
        $arr = array("4" =>$customer);
        $result = $handler->handle("withdrawPrivate", "1100", "4", "2014-12-31", $arr);
        $this->assertEquals($result, 3.3,'',0.1);
    }

    public function typeProvider()
    {
        return array(
            array(["", "", "private", "deposit"], "deposit"),
            array(["", "", "private", "withdraw"], "withdrawPrivate"),
            array(["", "", "business", "withdraw"], "withdrawBusiness")
        );
    }
}