<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

abstract class AbstractPolicy implements PolicyInterface
{
    private const ALLOW_ALL = "*";

    private const ALLOW_SELF = "'self'";

    private const ALLOW_NONE = "'none'";

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $urls = [];

    /**
     * @var string|null
     */
    private $allow = null;

    /**
     * AbstractPolicy constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return PolicyInterface
     */
    final public function allowAll(): PolicyInterface
    {
        $policy = clone $this;
        $policy->allow = self::ALLOW_ALL;

        return $policy;
    }

    /**
     * @return PolicyInterface
     */
    final public function allowSelf(): PolicyInterface
    {
        $policy = clone $this;
        $policy->allow = self::ALLOW_SELF;

        return $policy;
    }

    /**
     * @return PolicyInterface
     */
    final public function allowNone(): PolicyInterface
    {
        $policy = clone $this;
        $policy->allow = self::ALLOW_NONE;

        return $policy;
    }

    /**
     * @return PolicyInterface
     */
    final public function allowUrlOnly(): PolicyInterface
    {
        $policy = clone $this;
        $policy->allow = null;

        return $policy;
    }

    /**
     * @param string $url
     * @return PolicyInterface
     */
    final public function withUrl(string $url): PolicyInterface
    {
        $policy = clone $this;
        $policy->urls[] = $url;

        return $policy;
    }

    /**
     * @return string
     */
    final public function assemble(): ?string
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException("Policy name can't be empty");
        }

        if (\in_array($this->allow, [self::ALLOW_NONE, self::ALLOW_ALL])) {
            return \sprintf("%s %s", $this->name, $this->allow);
        }

        $urls = $this->urls;
        if ($this->allow !== null) {
            \array_unshift($urls, \sprintf("%s", $this->allow));
        }

        if (empty($urls)) {
            return null;
        }

        return \sprintf("%s %s", $this->name, \implode(' ', $urls));
    }
}
