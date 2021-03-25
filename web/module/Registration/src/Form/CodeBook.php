<?php

namespace Registration\Form;

use \Registration\Config\ConfigReader;

class CodeBook
{
    /* @var ConfigReader */
    protected $reader;

    protected $preferredCountries = [
        'CZ',
        'SK',
        'AT',
        'PL',
        'DE',
    ];

    public function __construct(ConfigReader $reader)
    {
        $this->reader = $reader;
    }

    public function getUniversities()
    {
        return $this->parse('universities.ini');
    }

    public function getCountries()
    {
        $countries = $this->parseSimple('countries.ini');
        $preferred = [];
        foreach($this->preferredCountries as $code) {
            $preferred[$code] = $countries[$code];
        }
        uasort($countries, "strcasecmp");
        return [
            'TOP' => [
                'label' => 'Most frequent countries',
                'options' => $preferred,
            ],
            'ALL' => [
                'label' => 'All countries',
                'options' => $countries,
            ]
        ];
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

    protected function parseSimple($file)
    {
        $values = $this->reader->getConfig($file);
        $result = [];
        foreach ($values as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }

}