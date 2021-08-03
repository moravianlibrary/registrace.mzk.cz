<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;

class MailServiceFactory
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $config = $configReader->getConfig('config/config.ini');
        $demo = $config['demo']['enabled'] ?? false;
        if ($demo) {
            return new MailServiceDemo();
        }
        return new MailServiceAleph();
    }

}