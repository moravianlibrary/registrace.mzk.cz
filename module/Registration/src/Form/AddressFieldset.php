<?php
namespace Registration\Form;

use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;

class AddressFieldset extends Fieldset
{

    public function init()
    {
        parent::init();
        // Street
        $this->add([
            'name'    => 'street',
            'type'    => Text::class,
            'options' => [
                'label' => 'Street',
            ],
        ]);
        // City
        $this->add([
            'name'    => 'city',
            'type'    => Text::class,
            'options' => [
                'label' => 'City',
            ],
        ]);
        // Postcode
        $this->add([
            'name'    => 'postcode',
            'type'    => Text::class,
            'options' => [
                'label' => 'Postcode',
            ],
        ]);
    }

    public function setOptions($options)
    {
        parent::setOptions($options);
        $required = $options['required'] ?? false;
        // Because laminas-forms is worthless shit, we have to set element
        // options here and not in init method (because init is called
        // before setOptions!!!)
        foreach ($this->getElements() as $element) {
            $element->setOption('required', $required);
        }
        return $this;
    }

}