<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\FeaturePolicy;

use Ixocreate\SecurityHeader\FeaturePolicy\FeaturePolicy;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Autoplay;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Camera;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Fullscreen;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Geolocation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ixocreate\SecurityHeader\FeaturePolicy\FeaturePolicy
 */
class FeaturePolicyTest extends TestCase
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

    private function reflectionPrivateProperty(string $propertyName, FeaturePolicy $featurePolicy)
    {
        $reflection = new \ReflectionClass($featurePolicy);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($featurePolicy);
    }

    public function testDefaults()
    {
        $featurePolicy = new FeaturePolicy();
        $this->assertSame([], $this->reflectionPrivateProperty('policies', $featurePolicy));
    }

    public function testWithPolicy()
    {
        $autoplay = new Autoplay();
        $camera = new Camera();

        $featurePolicy = new FeaturePolicy();
        $newFeaturePolicy = $featurePolicy->withPolicy($autoplay);
        $newFeaturePolicy = $newFeaturePolicy->withPolicy($camera);

        $this->assertNotSame($newFeaturePolicy, $featurePolicy);
        $this->assertSame([$autoplay, $camera], $this->reflectionPrivateProperty('policies', $newFeaturePolicy));
    }

    public function testFromArray()
    {
        $autoplay = new Autoplay();

        $featurePolicy = FeaturePolicy::fromArray([
            'policies' => [$autoplay],
            'dontexist' => 5000,
        ]);
        $this->assertSame([$autoplay], $this->reflectionPrivateProperty('policies', $featurePolicy));

        $featurePolicy = FeaturePolicy::fromArray([
            'policies' => false,
        ]);
        $this->assertSame([], $this->reflectionPrivateProperty('policies', $featurePolicy));

        $featurePolicy = FeaturePolicy::fromArray([]);
        $this->assertSame([], $this->reflectionPrivateProperty('policies', $featurePolicy));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithOnePolicy()
    {
        $featurePolicy = new FeaturePolicy();
        $featurePolicy = $featurePolicy->withPolicy((new Autoplay())->allowNone());

        $featurePolicy->send();
        $this->assertContains(
            "Feature-Policy: autoplay 'none'",
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithMorePolicies()
    {
        $featurePolicy = new FeaturePolicy();
        $featurePolicy = $featurePolicy->withPolicy((new Autoplay())->allowNone());
        $featurePolicy = $featurePolicy->withPolicy((new Camera())->allowAll());
        $featurePolicy = $featurePolicy->withPolicy((new Geolocation())->allowSelf());
        $featurePolicy = $featurePolicy->withPolicy((new Fullscreen())->allowUrlOnly());

        $featurePolicy->send();
        $this->assertContains(
            "Feature-Policy: autoplay 'none'; camera *; geolocation 'self'",
            xdebug_get_headers()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendWithNoPolicies()
    {
        $featurePolicy = new FeaturePolicy();

        $featurePolicy->send();
        $this->assertEmpty(xdebug_get_headers());
    }

    public function testResponseWithOnePolicy()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('feature-policy', "autoplay 'none'");

        $featurePolicy = new FeaturePolicy();
        $featurePolicy = $featurePolicy->withPolicy((new Autoplay())->allowNone());

        $featurePolicy->response($this->responseMock);
    }

    public function testResponseWithMorePolicies()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('feature-policy', "autoplay 'none'; camera *; geolocation 'self'");

        $featurePolicy = new FeaturePolicy();
        $featurePolicy = $featurePolicy->withPolicy((new Autoplay())->allowNone());
        $featurePolicy = $featurePolicy->withPolicy((new Camera())->allowAll());
        $featurePolicy = $featurePolicy->withPolicy((new Geolocation())->allowSelf());
        $featurePolicy = $featurePolicy->withPolicy((new Fullscreen())->allowUrlOnly());

        $featurePolicy->response($this->responseMock);
    }

    public function testResponseWithNoPolicies()
    {
        $this->responseMock
            ->expects($this->never())
            ->method('withHeader');

        $featurePolicy = new FeaturePolicy();

        $featurePolicy->response($this->responseMock);
    }
}
