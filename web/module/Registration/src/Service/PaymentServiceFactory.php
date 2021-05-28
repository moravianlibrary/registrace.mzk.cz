<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;

class PaymentServiceFactory
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $config = $configReader->getConfig('config/config.ini');
        $demo = $config['demo']['enabled'] ?? false;
        if ($demo) {
            return new PaymentServiceDemo();
        }
        return new PaymentService($config);
    }

}