<?php

namespace Registration\Controller;

use Psr\Container\ContainerInterface;
use Registration\Config\ConfigReader;

class PaymentControllerFactory
{

    public function __invoke(ContainerInterface $container) : PaymentController
    {
        $configReader = $container->get(\Registration\Config\ConfigReader::class);
        $paymentService = $container->get(\Registration\Service\PaymentServiceInterface::class);
        $registrationService = $container->get(\Registration\Service\RegistrationServiceInterface::class);
        $mailService = $container->get(\Registration\Service\MailServiceInterface::class);
        return new PaymentController($configReader->getConfig('config/config.ini'),
            $paymentService, $registrationService, $mailService);
    }

}