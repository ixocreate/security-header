<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\FrameOptions;

use Ixocreate\SecurityHeader\FrameOptions\FrameOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\FrameOptions\FrameOptions
 */
class FrameOptionsTest extends TestCase
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

    private function reflectionPrivateProperty(string $propertyName, FrameOptions $frameOptions)
    {
        $reflection = new \ReflectionClass($frameOptions);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($frameOptions);
    }

    public function testDefaults()
    {
        $frameOptions = new FrameOptions();
        $this->assertSame('deny', $this->reflectionPrivateProperty('option', $frameOptions));
    }

    public function testDenyImmutable()
    {
        $frameOptions = new FrameOptions();
        $newFrameOptions = $frameOptions->deny();
        $this->assertNotSame($newFrameOptions, $frameOptions);
    }

    public function testSameOriginImmutable()
    {
        $frameOptions = new FrameOptions();
        $newFrameOptions = $frameOptions->sameOrigin();
        $this->assertNotSame($newFrameOptions, $frameOptions);
    }

    public function testResponseDeny()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-frame-options', 'deny');
        $frameOptions = new FrameOptions();
        $frameOptions = $frameOptions->deny();

        $frameOptions->response($this->responseMock);
    }

    public function testResponseSameOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-frame-options', 'sameorigin');
        $frameOptions = new FrameOptions();
        $frameOptions = $frameOptions->sameOrigin();

        $frameOptions->response($this->responseMock);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendDeny()
    {
        $frameOptions = new FrameOptions();
        $frameOptions = $frameOptions->deny();

        $frameOptions->send();
        $this->assertContains(
            'X-Frame-Options: deny',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendSameOrigin()
    {
        $frameOptions = new FrameOptions();
        $frameOptions = $frameOptions->sameOrigin();

        $frameOptions->send();
        $this->assertContains(
            'X-Frame-Options: sameorigin',
            xdebug_get_headers()
        );
    }
}
