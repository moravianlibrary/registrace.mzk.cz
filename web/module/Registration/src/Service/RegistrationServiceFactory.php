<?php

namespace Registration\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\I18n\Translator;

class RegistrationServiceFactory
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        return new RegistrationService($configReader->getConfig('config.ini'));
    }

}