<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class Payment extends AbstractPolicy
{
    public function __construct()
    {
        parent::__construct('payment');
    }
}
