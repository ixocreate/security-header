<?php
declare(strict_types=1);

namespace Ixocreate\SecurityHeader\Hsts;

use Psr\Http\Message\ResponseInterface;

final class Hsts
{
    /**
     * @var bool
     */
    private $enable = true;

    /**
     * @var int
     */
    private $maxAge = 31536000;

    /**
     * @var bool
     */
    private $includeSubDomains = true;

    /**
     * @var bool
     */
    private $preload = false;

    /**
     * @return Hsts
     */
    public function enable(): Hsts
    {
        $hsts = clone $this;
        $hsts->enable = true;

        return $hsts;
    }

    /**
     * @return Hsts
     */
    public function disable(): Hsts
    {
        $hsts = clone $this;
        $hsts->enable = false;

        return $hsts;
    }

    /**
     * @param int $maxAge
     * @return Hsts
     */
    public function withMaxAge(int $maxAge): Hsts
    {
        $hsts = clone $this;
        $hsts->maxAge = $maxAge;

        return $hsts;
    }

    /**
     * @param bool $includeSubDomains
     * @return Hsts
     */
    public function withIncludeSubDomains(bool $includeSubDomains): Hsts
    {
        $hsts = clone $this;
        $hsts->includeSubDomains = $includeSubDomains;

        return $hsts;
    }

    /**
     * @param bool $preload
     * @return Hsts
     */
    public function withPreload(bool $preload): Hsts
    {
        $hsts = clone $this;
        $hsts->preload = $preload;

        return $hsts;
    }

    /**
     * Example array
     * [
     *   'enable' => true,
     *   'maxAge' => 1000,
     *   'includeSubDomains' => false,
     *   'preload' => false,
     * ]
     * @param array $array
     * @return Hsts
     */
    public static function fromArray(array $array): Hsts
    {
        $hsts = new Hsts();

        foreach (['enable', 'maxAge', 'includeSubDomains', 'preload'] as $key) {
            if (!\array_key_exists($key, $array)) {
                continue;
            }

            if ($key === 'enable') {
                $hsts = ($array[$key] === true) ? $hsts->enable() : $hsts->disable();
                continue;
            }

            $method = 'with' . \ucfirst($key);

            $hsts = $hsts->{$method}($array[$key]);
        }

        return $hsts;
    }

    private function generateHeaderValue(): string
    {
        $headerValue = \sprintf('max-age=%d', $this->maxAge);
        if ($this->includeSubDomains === true) {
            $headerValue .= '; includeSubDomains';
        }
        if ($this->preload === true) {
            $headerValue .= '; preload';
        }

        return $headerValue;
    }

    /**
     *
     */
    public function send(): void
    {
        if ($this->enable === false) {
            return;
        }

        \header('Strict-Transport-Security: '. $this->generateHeaderValue());
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        if ($this->enable === false) {
            return $response;
        }

        return $response->withHeader('strict-transport-security', $this->generateHeaderValue());
    }
}
