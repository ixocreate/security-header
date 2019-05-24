<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\XssProtection;

use Ixocreate\SecurityHeader\XssProtection\XssProtection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\XssProtection\XssProtection
 */
class XssProtectionTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $responseMock;

    public function setUp()
    {
        $this->responseMock = $this->createMock(ResponseInterface::class);
        $this->responseMock->method('withHeader')->willReturnSelf();
    }

    private function reflectionPrivateProperty(string $propertyName, XssProtection $xssProtection)
    {
        $reflection = new \ReflectionClass($xssProtection);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($xssProtection);
    }

    public function testDefaults()
    {
        $xssProtection = new XssProtection();
        $this->assertSame('1; mode=block', $this->reflectionPrivateProperty('mode', $xssProtection));
    }

    public function testDisableImmutable()
    {
        $xssProtection = new XssProtection();
        $newXssProtection = $xssProtection->disable();
        $this->assertNotSame($newXssProtection, $xssProtection);
    }

    public function testEnableImmutable()
    {
        $xssProtection = new XssProtection();
        $newXssProtection = $xssProtection->enable();
        $this->assertNotSame($newXssProtection, $xssProtection);
    }

    public function testBlockImmutable()
    {
        $xssProtection = new XssProtection();
        $newXssProtection = $xssProtection->block();
        $this->assertNotSame($newXssProtection, $xssProtection);
    }

    public function testReportImmutable()
    {
        $xssProtection = new XssProtection();
        $newXssProtection = $xssProtection->withReport('https://www.ixocreate.com');
        $this->assertNotSame($newXssProtection, $xssProtection);
    }

    public function testResponseDisable()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-xss-protection', '0');
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->disable();

        $xssProtection->response($this->responseMock);
    }

    public function testResponseEnable()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-xss-protection', '1');
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->enable();

        $xssProtection->response($this->responseMock);
    }

    public function testResponseBlock()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-xss-protection', '1; mode=block');
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->block();

        $xssProtection->response($this->responseMock);
    }

    public function testResponseReport()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-xss-protection', '1; mode=block; report=https://www.ixocreate.com');
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->block()->withReport('https://www.ixocreate.com');

        $xssProtection->response($this->responseMock);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendDisable()
    {
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->disable();

        $xssProtection->send();
        $this->assertContains(
            'X-XSS-Protection: 0',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendEnable()
    {
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->enable();

        $xssProtection->send();
        $this->assertContains(
            'X-XSS-Protection: 1',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendBlock()
    {
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->block();

        $xssProtection->send();
        $this->assertContains(
            'X-XSS-Protection: 1; mode=block',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendReport()
    {
        $xssProtection = new XssProtection();
        $xssProtection = $xssProtection->block()->withReport('https://www.ixocreate.com');

        $xssProtection->send();
        $this->assertContains(
            'X-XSS-Protection: 1; mode=block; report=https://www.ixocreate.com',
            xdebug_get_headers()
        );
    }
}
