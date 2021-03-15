<?php

declare(strict_types=1);

namespace Registration\Controller;


use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Registration\Form\UserForm;

class AjaxController extends AbstractActionController
{

    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function discountAction() {
        $discounts = [
            'none' => [
                'label' => $this->translator->translate('None'),
                'price' => '200',
            ]
        ];
        return $this->getAjaxResponse([]);
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