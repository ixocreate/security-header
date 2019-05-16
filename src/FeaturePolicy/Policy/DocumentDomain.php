<?php
declare(strict_types=1);

namespace Ixocreate\SecurityHeader\FeaturePolicy\Policy;

final class DocumentDomain extends AbstractPolicy
{
   public function __construct()
   {
       parent::__construct('document-domain');
   }
}
