<?php
namespace Registration\Form;

use Laminas\Filter;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

class FullAddressFieldset extends AddressFieldset
{

    public function init()
    {
        parent::init();
        // Country
        $this->add([
            'name'    => 'country',
            'type'    => Text::class,
            'options' => [
                'label' => 'label_country',
            ],
            'attributes' => [
                'value' => 'Czech republic ',
                'list' => 'countries',
            ],
        ]);
    }

}