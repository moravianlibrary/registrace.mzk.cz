<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\Form\FormElementManager;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Model\ViewModel;
use Laminas\Session\SessionManager;
use Registration\Form\UserForm;
use Registration\Log\LoggerAwareTrait;
use Registration\Service\DiscountService;
use Registration\IdentityProvider\IdentityProviderFactory;
use Registration\Model\User;
use Registration\Service\RegistrationServiceInterface;
use Registration\Service\MailServiceInterface;

class RegistrationController extends AbstractController
{
    use LoggerAwareTrait;

    /** @var \Laminas\Form\FormElementManager */
    private $formElementManager;

    /** @var  UserForm */
    private $userForm;

    private $config;

    /** @var IdentityProviderFactory */
    private $identityProviderFactory;

    /** @var RegistrationServiceInterface */
    private $registrationService;

    /** @var MailServiceInterface */
    private $mailService;

    /** @var Translator */
    private $translator;

    public function __construct(FormElementManager $formElementManager, $config, IdentityProviderFactory $identityProviderFactory,
                                RegistrationServiceInterface $registrationService,
                                MailServiceInterface $mailService, DiscountService $discountService,
                                Translator $translator)
    {
        parent::__construct();
        $this->formElementManager = $formElementManager;
        $this->config = $config;
        $this->identityProviderFactory = $identityProviderFactory;
        $this->registrationService = $registrationService;
        $this->mailService = $mailService;
        $this->discountService = $discountService;
        $this->translator = $translator;
    }

    public function indexAction()
    {
        $mojeid = $this->config['mojeid']['url'];
        $baseUrl = $this->config['application']['url'];
        $brnoIdEnabled = (bool) ($this->config['brnoid']['enabled'] ?? true);
        $eduIdEnabled = (bool) ($this->config['eduid']['enabled'] ?? true);
        $view = new ViewModel([
            'mojeid' => $mojeid,
            'baseUrl' => $baseUrl,
            'brnoIdEnabled' => $brnoIdEnabled,
            'eduIdEnabled'  => $eduIdEnabled,
        ]);
        $view->setTemplate('registration/index');
        return $view;
    }

    public function userFormAction()
    {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
        }
        $data['verified'] = 0;
        $data['discountEntitlement'] = 'none';
        $verified = false;
        $auth = $request->getQuery('idp');
        if ($auth != null) {
            $idp = $this->identityProviderFactory->get($auth);
            if ($idp == null) {
                $auth == null;
            }
            if ($idp != null && ($identity = $idp->identify($request)) != null) {
                // convert birth date as expected by laminas forms
                $birth = $identity['user']['birth'] ?? null;
                if ($birth != null) {
                    $birth = explode('-', $identity['user']['birth']);
                    $identity['user']['birth'] = [
                        'year' => $birth[0],
                        'month' => $birth[1],
                        'day' => $birth[2]
                    ];
                }
                $this->getLogger()->info("Data from IdP:\n" . print_r($identity, true));
                if ($identity['verified']) {
                    $verified = true;
                    $this->getUserForm()->setProtectedData($identity);
                }
                $data = array_replace_recursive($data, $identity);
            }
        }
        $this->getUserForm()->setData($data);
        $discounts = $this->discountService->getAvailable($this->getUserForm());
        $discount = $discounts[$this->getUserForm()->get('user')->get('discount')->getValue()];
        if ($request->isPost() && $discount && $this->getUserForm()->isValid()) {
            $user = new User($data, $discount);
            $id = $this->registrationService->register($user);
            $user->setLogin($id);
            $this->mailService->sendRegistrationInfo($user);

            $expiry = strtotime("+1 year, + 10 days");
            $finished = false;
            if ($verified && $discount['online'] && $discount['price'] == 0) {
                $this->registrationService->updateExpiration($id, $expiry);
                $finished = true;
            }
            $this->session->registration = [
                'id'        => $id,
                'user'      => $user,
                'verified'  => $verified,
                'discount'  => $discount,
                'expiry'    => $expiry,
                'finished'  => $finished,
                'payment'   => false,
            ];
            return $this->redirect()->toRoute('registration-finished');
        }
        $view = new ViewModel([
            'config' => [
                'countries' => $this->config['countries']
            ],
            'translations' => [
                'userForm_passwordConfirmNoMatch' => $this->translator
                    ->translate('userForm_passwordConfirmNoMatch'),
            ],
            'form' => $this->getUserForm(),
            'auth' => $auth,
            'unverified' => ($auth != null) && !$verified,
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
        if ($registration['verified'] && $registration['finished']) {
            return $this->redirect()->toRoute('payment-finished');
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

    protected function getUserForm()
    {
        if ($this->userForm == null) {
            $this->userForm = $this->formElementManager
                ->get(UserForm::class);
        }
        return $this->userForm;
    }

}
