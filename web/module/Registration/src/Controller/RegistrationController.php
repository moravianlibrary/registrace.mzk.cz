<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Registration\Form\UserForm;
use Registration\IdentityProvider\IdentityProviderFactory;

class RegistrationController extends AbstractController
{

    /** @var UserForm */
    private $form;

    private $config;

    private $identityProviderFactory;

    public function __construct(UserForm $form, $config, IdentityProviderFactory $identityProviderFactory)
    {
        $this->form = $form;
        $this->config = $config;
        $this->identityProviderFactory = $identityProviderFactory;
    }

    public function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate('registration/index');
        return $view;
    }

    public function userFormAction()
    {
        $request = $this->getRequest();
        if ($request->isPost() && $this->form->setData($request->getPost())->isValid()) {
            return $this->redirect()->toRoute('registration-finished');
        }
        $auth = $request->getQuery('idp');
        if ($auth != null) {
            $idp = $this->identityProviderFactory->get($auth);
            if ($idp != null && ($identity = $idp->identify($request)) != null) {
                $this->form->setData($identity);
            }
        }
        $view = new ViewModel([
            'config' => $this->config,
            'form' => $this->form
        ]);
        $view->setTemplate('registration/userForm');
        return $view;
    }

    public function finishedAction()
    {
        $view = new ViewModel([
            'login' => '123456789',
        ]);
        $view->setTemplate('registration/finished');
        return $view;
    }

    public function paymentAction()
    {
        $view = new ViewModel();
        $view->setTemplate('registration/payment');
        return $view;
    }

}
