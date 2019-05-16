<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy;

use Ixocreate\SecurityHeader\FeaturePolicy\Policy\PolicyInterface;
use Psr\Http\Message\ResponseInterface;

final class FeaturePolicy
{
    /**
     * @var PolicyInterface[]
     */
    private $policies = [];

    /**
     * @param PolicyInterface $policy
     * @return FeaturePolicy
     */
    public function withPolicy(PolicyInterface $policy): FeaturePolicy
    {
        $featurePolicy = clone $this;
        $featurePolicy->policies[] = $policy;

        return $featurePolicy;
    }

    /**
     * Example array
     * [
     *   'policies' => [new PolicyInterface()],
     * ]
     * @param array $array
     * @return FeaturePolicy
     */
    public static function fromArray(array $array): FeaturePolicy
    {
        $featurePolicy = new FeaturePolicy();

        foreach (['policies'] as $key) {
            if (!\array_key_exists($key, $array)) {
                continue;
            }

            if ($key === 'policies') {
                if (!\is_array($array[$key])) {
                    continue;
                }

                foreach ($array[$key] as $policy) {
                    $featurePolicy = $featurePolicy->withPolicy($policy);
                }
            }
        }

        return $featurePolicy;
    }

    /**
     * @return string
     */
    private function generateHeaderValue(): string
    {
        $assemble = [];
        foreach ($this->policies as $policy) {
            $assembled = $policy->assemble();
            if (empty($assembled)) {
                continue;
            }
            $assemble[] = $assembled;
        }

        if (empty($assemble)) {
            return "";
        }

        return \implode("; ", $assemble);
    }

    /**
     *
     */
    public function send(): void
    {
        $headerValue = $this->generateHeaderValue();
        if (empty($headerValue)) {
            return;
        }

        \header('Feature-Policy: ' . $headerValue);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response): ResponseInterface
    {
        $headerValue = $this->generateHeaderValue();
        if (empty($headerValue)) {
            return $response;
        }

        return $response->withHeader('feature-policy', $headerValue);
    }
}
