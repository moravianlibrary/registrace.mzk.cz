<?php
namespace Registration\Form;

use Laminas\Filter;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\Validator;

class FullAddressFieldset extends AddressFieldset
{

    /** @var CodeBook */
    protected $codeBook;

    /** @var Translator */
    protected $translator;

    public function __construct(CodeBook $codeBook, Translator $translator)
    {
        $this->codeBook = $codeBook;
        $this->translator = $translator;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        // Country
        $this->add([
            'name'    => 'country',
            'type'    => Select::class,
            'options' => [
                'label' => 'label_country',
                'value_options' => $this->codeBook->getCountries(),
            ],
        ]);
    }

}