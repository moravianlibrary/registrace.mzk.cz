<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

interface IdentityProviderInterface
{

    public function identify(Request $request);

}