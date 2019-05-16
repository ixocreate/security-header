<?php
declare(strict_types=1);

namespace Ixocreate\Test\SecurityHeader\FeaturePolicy;

use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Accelerometer;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\AmbientLightSensor;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Autoplay;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Camera;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\DocumentDomain;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\EncryptedMedia;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Fullscreen;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Geolocation;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Gyroscope;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\LayoutAnimations;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\LegacyImageFormats;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Magnetometer;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Microphone;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Midi;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\OversizedImages;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Payment;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\PictureInPicture;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\PolicyInterface;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Speaker;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\SyncXhr;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\UnoptimizedImages;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\UnsizedMedia;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Usb;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Vibrate;
use Ixocreate\SecurityHeader\FeaturePolicy\Policy\Vr;
use PHPUnit\Framework\TestCase;

class PolicyTest extends TestCase
{
    private function reflectionPrivateProperty(string $value, PolicyInterface $policy)
    {
        $reflection = new \ReflectionClass($policy);
        $property = $reflection->getParentClass()->getProperty("name");
        $property->setAccessible(true);
        $this->assertSame($value, $property->getValue($policy));
    }

    public function testPolicies()
    {
        $this->reflectionPrivateProperty('accelerometer', new Accelerometer());
        $this->reflectionPrivateProperty('ambient-light-sensor', new AmbientLightSensor());
        $this->reflectionPrivateProperty('autoplay', new Autoplay());
        $this->reflectionPrivateProperty('camera', new Camera());
        $this->reflectionPrivateProperty('document-domain', new DocumentDomain());
        $this->reflectionPrivateProperty('encrypted-media', new EncryptedMedia());
        $this->reflectionPrivateProperty('fullscreen', new Fullscreen());
        $this->reflectionPrivateProperty('geolocation', new Geolocation());
        $this->reflectionPrivateProperty('gyroscope', new Gyroscope());
        $this->reflectionPrivateProperty('layout-animations', new LayoutAnimations());
        $this->reflectionPrivateProperty('legacy-image-formats', new LegacyImageFormats());
        $this->reflectionPrivateProperty('magnetometer', new Magnetometer());
        $this->reflectionPrivateProperty('microphone', new Microphone());
        $this->reflectionPrivateProperty('midi', new Midi());
        $this->reflectionPrivateProperty('oversized-images', new OversizedImages());
        $this->reflectionPrivateProperty('payment', new Payment());
        $this->reflectionPrivateProperty('picture-in-picture', new PictureInPicture());
        $this->reflectionPrivateProperty('speaker', new Speaker());
        $this->reflectionPrivateProperty('sync-xhr', new SyncXhr());
        $this->reflectionPrivateProperty('unoptimized-images', new UnoptimizedImages());
        $this->reflectionPrivateProperty('unsized-media', new UnsizedMedia());
        $this->reflectionPrivateProperty('usb', new Usb());
        $this->reflectionPrivateProperty('vibrate', new Vibrate());
        $this->reflectionPrivateProperty('vr', new Vr());
    }
}
