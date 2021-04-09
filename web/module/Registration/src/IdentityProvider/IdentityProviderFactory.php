<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

class IdentityProviderFactory
{

    public function get(string $type)
    {
        if ($type == 'test') {
            return new Test();
        } else if ($type == 'mojeid') {
            return new MojeId();
        }
        return null;
    }

}
