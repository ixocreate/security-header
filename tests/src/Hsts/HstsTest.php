<?php
declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\Hsts;

use Ixocreate\SecurityHeader\Hsts\Hsts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\Hsts\Hsts
 */
class HstsTest extends TestCase
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

    private function reflectionPrivateProperty(string $propertyName, Hsts $hsts)
    {
        $reflection = new \ReflectionClass($hsts);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($hsts);
    }

    public function testDefaults()
    {
        $hsts = new Hsts();
        $this->assertTrue($this->reflectionPrivateProperty('enable', $hsts));
        $this->assertTrue($this->reflectionPrivateProperty('includeSubDomains', $hsts));
        $this->assertFalse($this->reflectionPrivateProperty('preload', $hsts));
        $this->assertSame(31536000, $this->reflectionPrivateProperty('maxAge', $hsts));
    }

    public function testEnable()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->enable();
        $this->assertNotSame($newHsts, $hsts);
        $this->assertTrue($this->reflectionPrivateProperty('enable', $newHsts));
    }

    public function testDisable()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->disable();
        $this->assertNotSame($newHsts, $hsts);
        $this->assertFalse($this->reflectionPrivateProperty('enable', $newHsts));
    }

    public function testWithMaxAge()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withMaxAge(1000);
        $this->assertNotSame($newHsts, $hsts);
        $this->assertSame(1000, $this->reflectionPrivateProperty('maxAge', $newHsts));
    }

    public function testWithIncludeSubDomains()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withIncludeSubDomains(false);
        $this->assertNotSame($newHsts, $hsts);
        $this->assertFalse($this->reflectionPrivateProperty('includeSubDomains', $newHsts));
    }

    public function testWithPreload()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withPreload(true);
        $this->assertNotSame($newHsts, $hsts);
        $this->assertTrue($this->reflectionPrivateProperty('preload', $newHsts));
    }

    public function testFromArray()
    {
        $hsts = Hsts::fromArray([
            'enable' => true,
            'maxAge' => 1000,
            'includeSubDomains' => false,
            'preload' => false,
            'dontexist' => 5000
        ]);
        $this->assertTrue($this->reflectionPrivateProperty('enable', $hsts));
        $this->assertFalse($this->reflectionPrivateProperty('includeSubDomains', $hsts));
        $this->assertFalse($this->reflectionPrivateProperty('preload', $hsts));
        $this->assertSame(1000, $this->reflectionPrivateProperty('maxAge', $hsts));

        $hsts = Hsts::fromArray([
            'enable' => false,
        ]);
        $this->assertFalse($this->reflectionPrivateProperty('enable', $hsts));
    }

    public function testResponseWithOnlyMaxAge()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('strict-transport-security', 'max-age=1000');
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(false);
        $hsts = $hsts->withIncludeSubDomains(false);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->response($this->responseMock);
    }

    public function testResponseWithPreload()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('strict-transport-security', 'max-age=1000; preload');
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(true);
        $hsts = $hsts->withIncludeSubDomains(false);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->response($this->responseMock);
    }

    public function testResponseWithIncludeSubDomains()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('strict-transport-security', 'max-age=1000; includeSubDomains');
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(false);
        $hsts = $hsts->withIncludeSubDomains(true);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->response($this->responseMock);
    }

    public function testResponseWithAll()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('strict-transport-security', 'max-age=1000; includeSubDomains; preload');
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(true);
        $hsts = $hsts->withIncludeSubDomains(true);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->response($this->responseMock);
    }

    public function testResponseDisabled()
    {
        $this->responseMock
            ->expects($this->never())
            ->method('withHeader');
        $hsts = (new Hsts())->disable();

        $hsts->response($this->responseMock);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithOnlyMaxAge()
    {
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(false);
        $hsts = $hsts->withIncludeSubDomains(false);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->send();
        $this->assertContains(
            'Strict-Transport-Security: max-age=1000', xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithPreload()
    {
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(true);
        $hsts = $hsts->withIncludeSubDomains(false);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->send();
        $this->assertContains(
            'Strict-Transport-Security: max-age=1000; preload', xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithIncludeSubDomains()
    {
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(false);
        $hsts = $hsts->withIncludeSubDomains(true);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->send();
        $this->assertContains(
            'Strict-Transport-Security: max-age=1000; includeSubDomains', xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithAll()
    {
        $hsts = new Hsts();
        $hsts = $hsts->withPreload(true);
        $hsts = $hsts->withIncludeSubDomains(true);
        $hsts = $hsts->withMaxAge(1000);

        $hsts->send();
        $this->assertContains(
            'Strict-Transport-Security: max-age=1000; includeSubDomains; preload', xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendDisabled()
    {
        $hsts = (new Hsts())->disable();
        $hsts->send();

        $this->assertEmpty(xdebug_get_headers());
    }
}
