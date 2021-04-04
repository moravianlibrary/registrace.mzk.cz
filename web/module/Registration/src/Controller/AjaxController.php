<?php

declare(strict_types=1);

namespace Registration\Controller;

use Datetime;

use Laminas\Form\Element\DateSelect;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Registration\Form\UserForm;
use Registration\Service\DiscountService;

class AjaxController extends AbstractActionController
{

    protected $translator;

    protected $form;

    protected $discountService;

    public function __construct(Translator $translator, UserForm $form, DiscountService $discountService)
    {
        $this->translator = $translator;
        $this->form = $form;
        $this->discountService = $discountService;
    }

    public function discountAction()
    {
        $this->form->setData($this->params()->fromPost());
        $discounts = $this->discountService->getAvailable($this->form);
        return $this->getAjaxResponse($discounts);
    }

    public function validateAction() {
        if ($this->form->setData($this->params()->fromPost())->isValid()) {
            return $this->getAjaxResponse(['status' => 'ok']);
        }
        $errors = [];
        // errors in elements
        foreach ($this->form->getElements() as $name => $element) {
            $elementErrors = $this->form->getMessages($name);
            if (!empty($elementErrors)) {
                $errors[$name] = $this->form->getMessages($name);
            }
        }
        // errors in field sets
        foreach ($this->form->getFieldsets() as $fieldSetName => $fieldSet) {
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

}