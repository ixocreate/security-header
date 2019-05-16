<?php
declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\ContentTypeOptions;

use Ixocreate\SecurityHeader\ContentTypeOptions\ContentTypeOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\ContentTypeOptions\ContentTypeOptions
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

    /**
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $contentTypeOptions = new ContentTypeOptions();

        $contentTypeOptions->send();
        $this->assertContains(
            'X-Content-Type-Options: nosniff', xdebug_get_headers()
        );
    }

    public function testResponse()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('x-content-type-options', 'nosniff');
        $contentTypeOptions = new ContentTypeOptions();

        $contentTypeOptions->response($this->responseMock);
    }
}
