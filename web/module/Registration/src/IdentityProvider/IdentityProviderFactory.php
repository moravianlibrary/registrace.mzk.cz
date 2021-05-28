<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

class IdentityProviderFactory
{

    protected $demo = false;

    public function __construct($config)
    {
        $this->demo = $config['demo']['enabled'] ?? false;
    }

    public function get(string $type)
    {
        if ($type == 'test' && $this->demo) {
            return new Test();
        } else if ($type == 'mojeid') {
            return new MojeId();
        }
        return null;
    }

}
