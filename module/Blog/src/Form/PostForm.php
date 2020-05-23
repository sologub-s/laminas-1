<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 23:49
 */

namespace Blog\Form;

use Blog\Model\Post;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;

class PostForm extends Form
{
    public function init()
    {
        /*
        $this->add([
            'name' => 'post',
            'type' => PostFieldset::class,
        ]);
        */

        $this->add([
            'name' => 'post',
            'type' => PostFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Insert new Post',
            ],
        ]);
    }

}