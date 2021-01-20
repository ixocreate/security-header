<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\ReferrerPolicy;

use Ixocreate\SecurityHeader\ReferrerPolicy\ReferrerPolicy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\ReferrerPolicy\ReferrerPolicy
 */
class ReferrerPolicyTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $responseMock;

    public function setUp(): void
    {
        $this->responseMock = $this->createMock(ResponseInterface::class);
        $this->responseMock->method('withHeader')->willReturnSelf();
    }

    private function reflectionPrivateProperty(string $propertyName, ReferrerPolicy $referrerPolicy)
    {
        $reflection = new \ReflectionClass($referrerPolicy);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($referrerPolicy);
    }

    public function testDefaults()
    {
        $referrerPolicy = new ReferrerPolicy();
        $this->assertSame('no-referrer', $this->reflectionPrivateProperty('mode', $referrerPolicy));
    }

    public function testImmutable()
    {
        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->defaultMode();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->noReferrer();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->noReferrerWhenDowngrade();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->sameOrigin();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->origin();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->strictOrigin();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->originWhenCrossOrigin();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->strictOriginWhenCrossOrigin();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);

        $referrerPolicy = new ReferrerPolicy();
        $newReferrerPolicy = $referrerPolicy->unsafeUrl();
        $this->assertNotSame($newReferrerPolicy, $referrerPolicy);
    }

    public function testResponseDefaultMode()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', '');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->defaultMode();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseNoReferrer()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'no-referrer');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->noReferrer();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseNoReferrerWhenDowngrade()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'no-referrer-when-downgrade');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->noReferrerWhenDowngrade();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseSameOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'same-origin');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->sameOrigin();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'origin');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->origin();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseStrictOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'strict-origin');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->strictOrigin();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseOriginWhenCrossOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'origin-when-cross-origin');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->originWhenCrossOrigin();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseStrictOriginWhenCrossOrigin()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'strict-origin-when-cross-origin');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->strictOriginWhenCrossOrigin();

        $referrerPolicy->response($this->responseMock);
    }

    public function testResponseUnsafeUrl()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('referrer-policy', 'unsafe-url');
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->unsafeUrl();

        $referrerPolicy->response($this->responseMock);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendDefaultMode()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->defaultMode();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy:',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendNoReferrer()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->noReferrer();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: no-referrer',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendNoReferrerWhenDowngrade()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->noReferrerWhenDowngrade();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: no-referrer-when-downgrade',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendSameOrigin()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->sameOrigin();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: same-origin',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendOrigin()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->origin();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: origin',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendStrictOrigin()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->strictOrigin();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: strict-origin',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendOriginWhenCrossOrigin()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->originWhenCrossOrigin();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: origin-when-cross-origin',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendStrictOriginWhenCrossOrigin()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->strictOriginWhenCrossOrigin();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: strict-origin-when-cross-origin',
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendUnsafeUrl()
    {
        $referrerPolicy = new ReferrerPolicy();
        $referrerPolicy = $referrerPolicy->unsafeUrl();

        $referrerPolicy->send();
        $this->assertContains(
            'Referrer-Policy: unsafe-url',
            xdebug_get_headers()
        );
    }
}
