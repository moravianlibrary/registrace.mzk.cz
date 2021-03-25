<?php

namespace Registration\Form;

use \Registration\Config\ConfigReader;

class CodeBook
{
    /* @var ConfigReader */
    protected $reader;

    public function __construct(ConfigReader $reader)
    {
        $this->reader = $reader;
    }

    public function getUniversities()
    {
        return $this->parse('universities.ini');
    }

    protected function parse($file)
    {
        $values = $this->reader->getConfig($file);
        $result = [];
        foreach ($values as $category => $elements) {
            $options = [];
            foreach ($elements as $key => $value) {
                $options[$key] = $value;
            }
            $result[$category] = [
                'label' => $elements['category'],
                'options' => $options,
            ];
        }
        return $result;
    }

}