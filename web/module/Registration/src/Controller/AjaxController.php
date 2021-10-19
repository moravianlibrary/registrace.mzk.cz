<?php

declare(strict_types=1);

namespace Registration\Controller;

use Datetime;

use Laminas\Form\Element\DateSelect;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Registration\Form\UserForm;
use Registration\Form\Validator\PasswordValidator;
use Registration\Service\DiscountService;
use Registration\I18n\LanguageListener;

class AjaxController extends AbstractActionController
{

    /** @var \Laminas\Form\FormElementManager */
    protected $formElementManager;

    protected $translator;

    protected $userForm;

    protected $discountService;

    /** @var PasswordValidator */
    protected $passwordValidator;

    /**
     * @var Container
     */
    protected $session;

    public function __construct(Translator $translator, FormElementManager $formElementManager,
                                DiscountService $discountService, PasswordValidator $passwordValidator)
    {
        $this->translator = $translator;
        $this->formElementManager = $formElementManager;
        $this->discountService = $discountService;
        $this->passwordValidator = $passwordValidator;
        $this->session = new Container('registration');
    }

    public function onDispatch(MvcEvent $event)
    {
        $locale = $this->session->locale ?? LanguageListener::DEFAULT_LOCALE;
        $this->translator->setLocale($locale);
        $this->translator->setFallbackLocale($locale . '.UTF-8');
        return parent::onDispatch($event);
    }

    public function discountAction()
    {
        $this->getUserForm()->setData($this->params()->fromPost());
        $discounts = $this->discountService->getAvailable($this->getUserForm());
        foreach ($discounts as $code => &$discount) {
            $discount['label'] = $this->translator
                ->translate($discount['label']);
        }
        return $this->getAjaxResponse($discounts);
    }

    public function validateAction() {
        if ($this->getUserForm()->setData($this->params()->fromPost())->isValid()) {
            return $this->getAjaxResponse(['status' => 'ok']);
        }
        $errors = [];
        // errors in elements
        foreach ($this->getUserForm()->getElements() as $name => $element) {
            $elementErrors = $this->getUserForm()->getMessages($name);
            if (!empty($elementErrors)) {
                $errors[$name] = $this->getUserForm()->getMessages($name);
            }
        }
        // errors in field sets
        foreach ($this->getUserForm()->getFieldsets() as $fieldSetName => $fieldSet) {
            foreach ($fieldSet->getElements() as $elementName => $element) {
                $name = $fieldSetName . '[' . $elementName . ']';
                $elementErrors = $fieldSet->getMessages($elementName);
                if (!empty($elementErrors)) {
                    $errors[$name] = $elementErrors;
                }
            }
        }
        return $this->getAjaxResponse($errors);
    }

    public function validatePasswordAction()
    {
        $password = $this->params()->fromPost('password');
        $valid = $this->passwordValidator->isValid($password);
        $result = [];
        if ($valid) {
            $result = [ 'status' => 'ok'];
        } else {
            $messages = $this->passwordValidator->getMessages();
            $result = [
                'status' => 'error',
                'messages' => $messages,
            ];
        }
        return $this->getAjaxResponse($result);
    }

    /**
     * Send output data and exit.
     *
     * @param mixed  $data     The response data
     * @param int    $httpCode A custom HTTP Status Code
     *
     * @return \Laminas\Http\Response
     */
    protected function getAjaxResponse($data, $httpCode = null)
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-type', 'application/json');
        $headers->addHeaderLine('Cache-Control', 'no-cache, must-revalidate');
        $headers->addHeaderLine('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        if ($httpCode !== null) {
            $response->setStatusCode($httpCode);
        }
        $response->setContent(json_encode($data, JSON_PRETTY_PRINT));
        return $response;
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