<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 18:27
 */

namespace Backend\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;

/**
 * Class AuthorForm
 * @package Backend\Form
 */
class AuthorForm extends Form
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
            'name' => 'name',
            'options' => [
                'label' => 'Name',
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
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);
    }

}