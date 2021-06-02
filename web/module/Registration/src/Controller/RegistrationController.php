<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\SessionManager;
use Registration\Form\UserForm;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\DiscountService;
use Registration\IdentityProvider\IdentityProviderFactory;
use Registration\Model\User;
use Registration\Service\RegistrationServiceInterface;

class RegistrationController extends AbstractController
{
    use LoggerAwareTrait;

    /** @var UserForm */
    private $form;

    private $config;

    /** @var IdentityProviderFactory */
    private $identityProviderFactory;

    /** @var RegistrationServiceInterface */
    private $registrationService;

    public function __construct(UserForm $form, $config, IdentityProviderFactory $identityProviderFactory,
                                RegistrationServiceInterface $registrationService)
    {
        parent::__construct();
        $this->form = $form;
        $this->config = $config;
        $this->identityProviderFactory = $identityProviderFactory;
        $this->registrationService = $registrationService;
    }

    public function indexAction()
    {
        $mojeid = $this->config['mojeid']['url'];
        $baseUrl = $this->config['application']['url'];
        $view = new ViewModel([
            'mojeid' => $mojeid,
            'baseUrl' => $baseUrl,
        ]);
        $view->setTemplate('registration/index');
        $this->layout()->showRegistrationButton = true;
        return $view;
    }

    public function userFormAction()
    {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $this->getLogger()->info("Data from post:\n" . print_r($request->getPost()->toArray(), true));
            $data = $request->getPost()->toArray();
        }
        $verified = false;
        $auth = $request->getQuery('idp');
        if ($auth != null) {
            $idp = $this->identityProviderFactory->get($auth);
            if ($idp != null && ($identity = $idp->identify($request)) != null) {
                // convert birth date as expected by laminas forms
                $birth = explode('-', $identity['user']['birth']);
                $identity['user']['birth'] = [
                    'year' => $birth[0],
                    'month' => $birth[1],
                    'day' => $birth[2]
                ];
                $this->getLogger()->info("Data from IdP:\n" . print_r($identity, true));
                if ($identity['verified']) {
                    $verified = true;
                    $this->form->setProtectedData($identity);
                }
                $data = array_replace_recursive($data, $identity);
            }
        }
        $this->getLogger()->info("Data after merge:\n" . print_r($data, true));
        $this->form->setData($data);
        if ($request->isPost() && $this->form->isValid()) {
            $id = $this->registrationService->register(new User($data));
            $this->session->registration = [
                'id'       => $id,
                'verified' => $verified,
                'discount' => $this->form->get('user')->getDiscount(),
                'expiry'   => strtotime("+1 year, + 10 days"),
                'finished' => false,
                'payment'  => false,
            ];
            return $this->redirect()->toRoute('registration-finished');
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
        $registration = &$this->session->registration;
        if ($registration == null) {
            $this->flashMessenger()->addMessage('You are not registered.');
            return $this->redirect()->toRoute('registration-index');
        }
        $view = new ViewModel([
            'login'    => $registration['id'],
            'verified' => $registration['verified'],
            'discount' => $registration['discount'],
            'finished' => $registration['finished'],
        ]);
        $view->setTemplate('registration/finished');
        return $view;
    }

}
