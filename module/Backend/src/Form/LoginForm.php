<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 18:27
 */

namespace Backend\Form;

use Laminas\Form\Form;
use Laminas\Form\FormInterface;

/**
 * Class LoginForm
 * @package Backend\Form
 */
class LoginForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Login',
            ],
        ]);
    }

}