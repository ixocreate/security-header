<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

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
        $this->assertTrue($this->reflectionPrivateProperty('includeSubDomains', $hsts));
        $this->assertFalse($this->reflectionPrivateProperty('preload', $hsts));
        $this->assertSame(31536000, $this->reflectionPrivateProperty('maxAge', $hsts));
    }

    public function testWithMaxAgeImmutable()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withMaxAge(1000);
        $this->assertNotSame($newHsts, $hsts);
    }

    public function testWithIncludeSubDomainsImmutable()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withIncludeSubDomains(false);
        $this->assertNotSame($newHsts, $hsts);
    }

    public function testWithPreloadImmutable()
    {
        $hsts = new Hsts();
        $newHsts = $hsts->withPreload(true);
        $this->assertNotSame($newHsts, $hsts);
    }

    public function testFromArray()
    {
        $hsts = Hsts::fromArray([
            'maxAge' => 1000,
            'includeSubDomains' => false,
            'preload' => false,
            'dontexist' => 5000,
        ]);
        $this->assertFalse($this->reflectionPrivateProperty('includeSubDomains', $hsts));
        $this->assertFalse($this->reflectionPrivateProperty('preload', $hsts));
        $this->assertSame(1000, $this->reflectionPrivateProperty('maxAge', $hsts));

        $hsts = Hsts::fromArray([
            'includeSubDomains' => false,
        ]);
        $this->assertFalse($this->reflectionPrivateProperty('includeSubDomains', $hsts));
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
            'Strict-Transport-Security: max-age=1000',
            xdebug_get_headers()
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
            'Strict-Transport-Security: max-age=1000; preload',
            xdebug_get_headers()
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
            'Strict-Transport-Security: max-age=1000; includeSubDomains',
            xdebug_get_headers()
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
            'Strict-Transport-Security: max-age=1000; includeSubDomains; preload',
            xdebug_get_headers()
        );
    }
}
