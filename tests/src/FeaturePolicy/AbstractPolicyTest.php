<?php
declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\FeaturePolicy;

use Ixocreate\SecurityHeader\FeaturePolicy\Policy\AbstractPolicy;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\PolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\SecurityHeader\FeaturePolicy\Policy\AbstractPolicy
 */
class AbstractPolicyTest extends TestCase
{
    /**
     * @var PolicyInterface
     */
    private $policy;

    public function setUp()
    {
        $this->policy = new class extends AbstractPolicy
        {
            public function __construct()
            {
                parent::__construct("policy");
            }
        };
    }

    private function reflectionPrivateProperty(string $property, PolicyInterface $policy)
    {
        $reflection = new \ReflectionClass($policy);
        $property = $reflection->getParentClass()->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($policy);
    }

    public function testAllowAllImmutable()
    {
        $policy = $this->policy->allowAll();

        $this->assertNotSame($this->policy, $policy);
    }

    public function testAllowSelfImmutable()
    {
        $policy = $this->policy->allowSelf();

        $this->assertNotSame($this->policy, $policy);
    }

    public function testAllowNoneImmutable()
    {
        $policy = $this->policy->allowNone();

        $this->assertNotSame($this->policy, $policy);
    }

    public function testAllowUrlOnlyImmutable()
    {
        $policy = $this->policy->allowUrlOnly();

        $this->assertNotSame($this->policy, $policy);
    }

    public function testWithUrlImmutable()
    {
        $policy = $this->policy->withUrl('https://www.ixocreate.com');

        $this->assertNotSame($this->policy, $policy);
    }

    public function testAllowAll()
    {
        $policy = $this->policy->allowAll()->withUrl('https://www.ixocreate.com');

        $this->assertSame("policy *", $policy->assemble());
    }

    public function testAllowNone()
    {
        $policy = $this->policy->allowNone()->withUrl('https://www.ixocreate.com');

        $this->assertSame("policy 'none'", $policy->assemble());
    }

    public function testUrls()
    {
        $policy = $this->policy->withUrl('https://www.ixocreate.com')->withUrl('https://www.ixolit.com');

        $this->assertSame("policy https://www.ixocreate.com https://www.ixolit.com", $policy->assemble());
    }

    public function testEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $policy = new class extends AbstractPolicy{
            public function __construct()
            {
            }
        };

        $policy->assemble();
    }

    public function testEmptyUrls()
    {
        $this->assertNull($this->policy->assemble());
    }

    public function testSelfWithUrls()
    {
        $policy = $this->policy->allowSelf()->withUrl('https://www.ixocreate.com')->withUrl('https://www.ixolit.com');

        $this->assertSame("policy 'self' https://www.ixocreate.com https://www.ixolit.com", $policy->assemble());

    }

    public function testSelfWithoutUrls()
    {
        $policy = $this->policy->allowSelf();
        $this->assertSame("policy 'self'", $policy->assemble());

    }
}
