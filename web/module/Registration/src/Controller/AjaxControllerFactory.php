<?php
declare(strict_types=1);

namespace Registration\Controller;

use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\PluginManagerInterface;
use Psr\Container\ContainerInterface;
use Registration\Form\UserForm;

class AjaxControllerFactory
{
    public function __invoke(ContainerInterface $container) : AjaxController
    {
        $translator = $container->get(Translator::class);
        /** @var PluginManagerInterface $formElementManager */
        $formElementManager = $container->get('FormElementManager');
        /** @var UserForm */
        $form = $formElementManager->get(UserForm::class);
        $discountService = $container->get(\Registration\Service\DiscountService::class);
        $passwordValidator = $container->get(\Registration\Form\Validator\PasswordValidator::class);
        return new AjaxController($translator, $form, $discountService, $passwordValidator);
    }
}