<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FrameOptions;

use Psr\Http\Message\ResponseInterface;

final class FrameOptions
{
    private const OPTION_DENY = 'deny';

    private const OPTION_SAMEORIGIN = 'sameorigin';

    /**
     * @var string
     */
    private $option = self::OPTION_DENY;

    /**
     * @return FrameOptions
     */
    public function deny(): FrameOptions
    {
        $frameOption = clone $this;
        $frameOption->option = self::OPTION_DENY;

        return $frameOption;
    }

    /**
     * @return FrameOptions
     */
    public function sameOrigin(): FrameOptions
    {
        $frameOption = clone $this;
        $frameOption->option = self::OPTION_SAMEORIGIN;

        return $frameOption;
    }

    /**
     *
     */
    public function send(): void
    {
        \header('X-Frame-Options: ' . $this->option);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('x-frame-options', $this->option);
    }
}
