<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Registration\Form\UserForm;

class IndexController extends AbstractActionController
{

    /** @var UserForm */
    private $form;

    public function __construct(UserForm $form)
    {
        $this->form = $form;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost())->isValid();
        }
        return new ViewModel([
            'form' => $this->form
        ]);
    }

}
