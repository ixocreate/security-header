<?php
declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class Speaker extends AbstractPolicy
{
   public function __construct()
   {
       parent::__construct('speaker');
   }
}
