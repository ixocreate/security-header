<?php
declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class EncryptedMedia extends AbstractPolicy
{
   public function __construct()
   {
       parent::__construct('encrypted-media');
   }
}
