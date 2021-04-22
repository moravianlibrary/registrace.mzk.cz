<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\SessionManager;
use Registration\Form\UserForm;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\RegistrationService;
use Registration\IdentityProvider\IdentityProviderFactory;
use Registration\Model\User;

class RegistrationController extends AbstractController
{
    use LoggerAwareTrait;

    /** @var UserForm */
    private $form;

    private $config;

    /** @var IdentityProviderFactory */
    private $identityProviderFactory;

    /** @var RegistrationService */
    private $registrationService;

    public function __construct(UserForm $form, $config, IdentityProviderFactory $identityProviderFactory,
                                RegistrationService $registrationService)
    {
        parent::__construct();
        $this->form = $form;
        $this->config = $config;
        $this->identityProviderFactory = $identityProviderFactory;
        $this->registrationService = $registrationService;
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
            $this->getLogger()->info("Data from post:\n" . print_r($request->getPost()->toArray(), true));
            $id = $this->registrationService->register(new User($request->getPost()));
            $this->session->id = $id;
            return $this->redirect()->toRoute('registration-finished');
        }
        $auth = $request->getQuery('idp');
        if ($auth != null) {
            $idp = $this->identityProviderFactory->get($auth);
            if ($idp != null && ($identity = $idp->identify($request)) != null) {
                $this->form->setData($identity);
                if ($identity['valid']) {
                    $this->form->protect();
                }
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
            'login' => $this->session->id,
        ]);
        $view->setTemplate('registration/finished');
        return $view;
    }

}
