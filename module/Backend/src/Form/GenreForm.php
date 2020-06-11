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
 * Class GenreForm
 * @package Backend\Form
 */
class GenreForm extends Form
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
            'type' => Element\Textarea::class,
            'name' => 'description',
            'options' => [
                'label' => 'Description',
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