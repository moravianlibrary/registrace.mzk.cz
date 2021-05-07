<?php
namespace Registration\Form\Validator;

use Laminas\Validator\Exception;
use Laminas\Validator\ValidatorInterface;
use Laminas\Validator\AbstractValidator;

class PasswordValidator extends AbstractValidator
{

    const MIN_LENGTH = 6;

    const VALIDATION_MIN_LENGTH_MESSAGE = "userForm_validation_password_min_length";

    const MAX_LENGTH = 40;

    const VALIDATION_MAX_LENGTH_MESSAGE = "userForm_validation_password_max_length";

    const VALIDATION_CHAR_GROUP_MESSAGE = "userForm_validation_password_group_characters";

    const ALPHABET_CHARS = "abcdefghijklmnopqrstuvwxyz";

    const NUMERIC_CHARS = "0123456789";

    protected $lastMessages = [];

    public function isValid($value)
    {
        $this->lastMessages = [];
        // password length
        if (strlen($value) < self::MIN_LENGTH) {
            $this->lastMessages[] = self::VALIDATION_MIN_LENGTH_MESSAGE;
        }
        if (strlen($value) > self::MAX_LENGTH) {
            $this->lastMessages[] = self::VALIDATION_MAX_LENGTH_MESSAGE;
        }
        // group characters
        $alphabet = false;
        $numeric = false;
        $special = false;
        $count = 0;
        $specialCharacters = self::getSpecialCharacters();
        foreach (str_split($value) as $val) {
            if (!(strpos(self::ALPHABET_CHARS, $val) === false)) {
                $alphabet = true;
                $count++;
                continue;
            }
            if (!(strpos(self::NUMERIC_CHARS, $val) === false)) {
                $numeric = true;
                $count++;
                continue;
            }
            if (!(strpos($specialCharacters, $val) === false)) {
                $special = true;
                $count++;
                continue;
            }
        }
        $valid = ($alphabet && $numeric && $special && ($count == strlen($value)));
        if (!$valid) {
            $this->lastMessages[] = self::VALIDATION_CHAR_GROUP_MESSAGE;
        }
        return empty($this->lastMessages);
    }

    public function getMessages()
    {
        $messages = $this->lastMessages;
        $result = [];
        foreach ($messages as $message) {
            $result[] = $this->getTranslator()->translate($message);
        }
        return $result;
    }

    protected static function getSpecialCharacters()
    {
        $result = '';
        $forbidden = self::ALPHABET_CHARS
            . strtoupper(self::ALPHABET_CHARS)
            . self::NUMERIC_CHARS;
        for ($i = 33; $i <= 126; $i++) {
            $char = chr($i);
            if (strpos($forbidden, $char) === false) {
                $result .= $char;
            }
        }
        return $result;
    }

}