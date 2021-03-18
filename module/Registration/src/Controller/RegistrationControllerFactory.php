<?php
namespace Registration\Controller;

use Laminas\ServiceManager\PluginManagerInterface;
use Psr\Container\ContainerInterface;
use Registration\Form\UserForm;

class RegistrationControllerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationController
    {
        /** @var PluginManagerInterface $formElementManager */
        $formElementManager = $container->get('FormElementManager');
        /** @var UserForm */
        $form = $formElementManager->get(UserForm::class);
        return new RegistrationController($form);
    }

}