<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\ContentTypeOptions;

use Psr\Http\Message\ResponseInterface;

final class ContentTypeOptions
{
    /**
     *
     */
    public function send(): void
    {
        \header('X-Content-Type-Options: nosniff');
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('x-content-type-options', 'nosniff');
    }
}
