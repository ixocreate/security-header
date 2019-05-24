<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class Usb extends AbstractPolicy
{
    public function __construct()
    {
        parent::__construct('usb');
    }
}
