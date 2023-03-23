<?php
namespace Registration\Form\Validator;

use Laminas\Validator\AbstractValidator;

class NameValidator extends AbstractValidator
{
    const INVALID  = 'invalidName';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID  => 'Invalid name',
    ];

    public function isValid($value)
    {
        if ($value == null || !preg_match('/^[\w]+$/u', $value)) {
            $this->error(self::INVALID);
            return false;
        }
        return true;
    }
}