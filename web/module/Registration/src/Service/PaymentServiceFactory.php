<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;

class PaymentServiceFactory
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        return new PaymentService($configReader->getConfig('config/config.ini'));
    }

}