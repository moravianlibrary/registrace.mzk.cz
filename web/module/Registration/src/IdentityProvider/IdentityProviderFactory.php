<?php

declare(strict_types=1);

namespace Registration\IdentityProvider;

class IdentityProviderFactory
{

    /** @var bool */
    protected $test = false;

    public function __construct($config)
    {
        $this->test = (bool) $config['mojeid']['test'] ?? false;
    }

    public function get(string $type)
    {
        if ($type == 'test' && $this->test) {
            return new Test();
        } else if ($type == 'mojeid') {
            return new MojeId($this->test);
        }
        return null;
    }

}
