<?php

namespace Registration\Form;

use \Laminas\ServiceManager\Factory\FactoryInterface;

class CodeBookFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        return new $requestedName($configReader);
    }

}