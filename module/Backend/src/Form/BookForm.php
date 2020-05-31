<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 23:11
 */

namespace Backend\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;

/**
 * Class BookForm
 * @package Backend\Form
 */
class BookForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'id',
            'options' => [
                'label' => 'ID',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'title',
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'slug',
            'options' => [
                'label' => 'Slug',
            ],
        ]);

        $this->add([
            'type' => Element\Select::class,
            'name' => 'id_author',
            'options' => [
                'label' => 'Author',
                'empty_option' => 'Please choose an Author',
                'value_options' => [],
            ],
        ]);

        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);

        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit_and_new',
            'attributes' => [
                'value' => 'Submit & Add another',
            ],
        ]);
    }

}