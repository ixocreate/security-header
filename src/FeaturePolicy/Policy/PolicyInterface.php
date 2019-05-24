<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

interface PolicyInterface
{
    /**
     * @return PolicyInterface
     */
    public function allowAll(): PolicyInterface;

    /**
     * @return PolicyInterface
     */
    public function allowSelf(): PolicyInterface;

    /**
     * @return PolicyInterface
     */
    public function allowNone(): PolicyInterface;

    /**
     * @param string $url
     * @return PolicyInterface
     */
    public function withUrl(string $url): PolicyInterface;

    /**
     * @return PolicyInterface
     */
    public function allowUrlOnly(): PolicyInterface;

    /**
     * @return string
     */
    public function assemble(): ?string;
}
