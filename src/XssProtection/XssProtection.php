<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\XssProtection;

use Psr\Http\Message\ResponseInterface;

final class XssProtection
{
    private const MODE_DISABLE = '0';

    private const MODE_ENABLE = '1';

    private const MODE_BLOCK = '1; mode=block';

    /**
     * @var string
     */
    private $mode = self::MODE_BLOCK;

    /**
     * @var string|null
     */
    private $report = null;

    /**
     * @return XssProtection
     */
    public function disable(): XssProtection
    {
        $xssProtection = clone $this;
        $xssProtection->mode = self::MODE_DISABLE;

        return $xssProtection;
    }

    /**
     * @return XssProtection
     */
    public function enable(): XssProtection
    {
        $xssProtection = clone $this;
        $xssProtection->mode = self::MODE_ENABLE;

        return $xssProtection;
    }

    /**
     * @return XssProtection
     */
    public function block(): XssProtection
    {
        $xssProtection = clone $this;
        $xssProtection->mode = self::MODE_BLOCK;

        return $xssProtection;
    }

    /**
     * @param string $url
     * @return XssProtection
     */
    public function withReport(string $url): XssProtection
    {
        $xssProtection = clone $this;
        $xssProtection->report = $url;

        return $xssProtection;
    }

    private function generateHeaderValue(): string
    {
        $headerValue = $this->mode;
        if ($this->report !== null) {
            $headerValue .= '; report=' . $this->report;
        }

        return $headerValue;
    }

    /**
     *
     */
    public function send(): void
    {
        \header('X-XSS-Protection: ' . $this->generateHeaderValue());
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('x-xss-protection', $this->generateHeaderValue());
    }
}
