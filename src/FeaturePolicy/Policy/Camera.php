<?php
declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class Camera extends AbstractPolicy
{
   public function __construct()
   {
       parent::__construct('camera');
   }
}
