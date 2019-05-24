<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\ReferrerPolicy;

use Psr\Http\Message\ResponseInterface;

final class ReferrerPolicy
{
    private const MODE_DEFAULT = "";

    private const MODE_NOREFERRER = "no-referrer";

    private const MODE_NOREFERRERWHENDOWNGRADE = "no-referrer-when-downgrade";

    private const MODE_SAMEORIGIN = "same-origin";

    private const MODE_ORIGIN = "origin";

    private const MODE_STRICTORIGIN = "strict-origin";

    private const MODE_ORIGINWHENCROSSORIGIN = "origin-when-cross-origin";

    private const MODE_STRICTORIGINWHENCROSSORIGIN = "strict-origin-when-cross-origin";

    private const MODE_UNSAFEURL = "unsafe-url";

    /**
     * @var string
     */
    private $mode = self::MODE_NOREFERRER;

    /**
     * @return ReferrerPolicy
     */
    public function defaultMode(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_DEFAULT;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function noReferrer(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_NOREFERRER;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function noReferrerWhenDowngrade(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_NOREFERRERWHENDOWNGRADE;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function sameOrigin(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_SAMEORIGIN;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function origin(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_ORIGIN;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function strictOrigin(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_STRICTORIGIN;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function originWhenCrossOrigin(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_ORIGINWHENCROSSORIGIN;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function strictOriginWhenCrossOrigin(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_STRICTORIGINWHENCROSSORIGIN;

        return $referrerPolicy;
    }

    /**
     * @return ReferrerPolicy
     */
    public function unsafeUrl(): ReferrerPolicy
    {
        $referrerPolicy = clone $this;
        $referrerPolicy->mode = self::MODE_UNSAFEURL;

        return $referrerPolicy;
    }

    /**
     *
     */
    public function send(): void
    {
        \header('Referrer-Policy: ' . $this->mode);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('referrer-policy', $this->mode);
    }
}
