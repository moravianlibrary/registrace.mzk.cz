<?php

declare(strict_types=1);

namespace Registration\Controller;

use Datetime;

use Laminas\Form\Element\DateSelect;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Registration\Form\UserForm;

class AjaxController extends AbstractActionController
{

    protected $translator;

    protected $form;

    public function __construct(Translator $translator, UserForm $form)
    {
        $this->translator = $translator;
        $this->form = $form;
    }

    public function discountAction()
    {
        $this->form->setData($this->params()->fromPost());
        $birth = $this->form->get('user')->get('birth')->getValue();
        $age = DateTime::createFromFormat('Y-m-d', $birth)->diff(new DateTime('now'))->y;
        $discounts = [
            'none' => [
                'label' => $this->translator->translate('None'),
                'price' => '200',
            ]
        ];
        if ($age <= 18) {
            $discounts['student'] = [
                'label' => $this->translator->translate('Student'),
                'price' => '100',
            ];
        }
        return $this->getAjaxResponse($discounts);
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
        $response->setContent(json_encode($data));
        return $response;
    }

}