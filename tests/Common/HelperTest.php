<?php

namespace League\Omnipay\Common;

use Mockery as m;
use League\Omnipay\Tests\TestCase;

class HelperTest extends TestCase
{
    public function testCamelCase()
    {
        $result = Helper::camelCase('test_case');
        $this->assertEquals('testCase', $result);
    }

    public function testCamelCaseAlreadyCorrect()
    {
        $result = Helper::camelCase('testCase');
        $this->assertEquals('testCase', $result);
    }

    public function testCamelCaseWithUppercaseValue()
    {
        $result = Helper::camelCase('TEST_CASE');
        $this->assertEquals('testCase', $result);
    }

    public function testValidateLuhnValid()
    {
        $result = Helper::validateLuhn('4111111111111111');
        $this->assertTrue($result);
    }

    public function testValidateLuhnInvalid()
    {
        $result = Helper::validateLuhn('4111111111111110');
        $this->assertFalse($result);
    }

    public function testValidateLuhnNull()
    {
        $result = Helper::validateLuhn(null);
        $this->assertTrue($result);
    }

    public function testInitializeCallsSetters()
    {
        $target = m::mock('\League\Omnipay\Common\CreditCard');
        $target->shouldReceive('setNumber')->once()->with('1234');

        Helper::initializeParameters($target, ['number' => '1234']);
    }

    public function testGetGatewayShortNameSimple()
    {
        $shortName = Helper::getGatewayShortName('League\\Omnipay\\Stripe\\Gateway');
        $this->assertSame('Stripe', $shortName);
    }

    public function testGetGatewayShortNameSimpleLeadingSlash()
    {
        $shortName = Helper::getGatewayShortName('\\League\\Omnipay\\Stripe\\Gateway');
        $this->assertSame('Stripe', $shortName);
    }

    public function testGetGatewayShortNameUnderscore()
    {
        $shortName = Helper::getGatewayShortName('League\\Omnipay\\PayPal\\ExpressGateway');
        $this->assertSame('PayPal_Express', $shortName);
    }

    public function testGetGatewayShortNameUnderscoreLeadingSlash()
    {
        $shortName = Helper::getGatewayShortName('\\League\\Omnipay\\PayPal\\ExpressGateway');
        $this->assertSame('PayPal_Express', $shortName);
    }

    public function testGetGatewayShortNameCustomGateway()
    {
        $shortName = Helper::getGatewayShortName('\\Custom\\Gateway');
        $this->assertSame('\\Custom\\Gateway', $shortName);
    }

    /**
     * Type with namespace should simply be returned as is
     */
    public function testGetGatewayClassNameExistingNamespace()
    {
        $class = Helper::getGatewayClassName('\\Custom\\Gateway');
        $this->assertEquals('\\Custom\\Gateway', $class);
    }

    /**
     * Type with namespace marker should be left intact, even if it contains an underscore
     */
    public function testGetGatewayClassNameExistingNamespaceUnderscore()
    {
        $class = Helper::getGatewayClassName('\\Custom_Gateway');
        $this->assertEquals('\\Custom_Gateway', $class);
    }

    public function testGetGatewayClassNameSimple()
    {
        $class = Helper::getGatewayClassName('Stripe');
        $this->assertEquals('\\League\\Omnipay\\Stripe\\Gateway', $class);
    }

    public function testGetGatewayClassNamePartialNamespace()
    {
        $class = Helper::getGatewayClassName('PayPal\\Express');
        $this->assertEquals('\\League\\Omnipay\\PayPal\\ExpressGateway', $class);
    }

    /**
     * Underscored types should be resolved in a PSR-0 fashion
     */
    public function testGetGatewayClassNameUnderscoreNamespace()
    {
        $class = Helper::getGatewayClassName('PayPal_Express');
        $this->assertEquals('\\League\\Omnipay\\PayPal\\ExpressGateway', $class);
    }

    /**
     * Some valid toFloat() inputs.
     */
    public function testToFloatFromFloat()
    {
        $shortName = Helper::toFloat(1.99);
        $this->assertSame(1.99, $shortName);
    }

    public function testToFloatFromInt()
    {
        $shortName = Helper::toFloat(199);
        $this->assertSame(199.0, $shortName);
    }

    public function testToFloatFromStringDecimal()
    {
        $shortName = Helper::toFloat("1.99");
        $this->assertSame(1.99, $shortName);
    }

    public function testToFloatFromStringRedunantZeroes()
    {
        $shortName = Helper::toFloat("000009.99900000000");
        $this->assertSame(9.999, $shortName);
    }

    public function testToFloatFromStringEmptyDecimal()
    {
        $shortName = Helper::toFloat("1.");
        $this->assertSame(1.0, $shortName);
    }

    public function testToFloatFromStringInt()
    {
        $shortName = Helper::toFloat("199");
        $this->assertSame(199.0, $shortName);
    }

    public function testToFloatFromStringIntNegative()
    {
        $shortName = Helper::toFloat("-199");
        $this->assertSame(-199.0, $shortName);
    }

    /**
     * Some invalid toFloat() inputs.
     */

    /**
     * The number MUST always start with a digit.
     * This is arguably an arbitrary rule that perhaps does not need
     * to be enforced.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String is not a valid decimal number
     */
    public function testToFloatFromStringEmptyIntegerPart()
    {
        $shortName = Helper::toFloat(".99");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String is not a valid decimal number
     */
    public function testToFloatFromStringTwoDecimalPoints()
    {
        $shortName = Helper::toFloat("1.99.");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String is not a valid decimal number
     */
    public function testToFloatFromStringWrongDecimalPoints()
    {
        $shortName = Helper::toFloat("1,99");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Data type is not a valid decimal number
     */
    public function testToFloatFromBoolean()
    {
        $shortName = Helper::toFloat(false);
    }
}
