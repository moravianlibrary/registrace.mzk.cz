<?php

declare(strict_types=1);

namespace Registration\Controller;

use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Registration\Form\UserForm;

class RegistrationController extends AbstractController
{

    /** @var UserForm */
    private $form;

    public function __construct(UserForm $form)
    {
        $this->form = $form;
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
        $view = new ViewModel([
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
