<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 26.05.2020
 * Time: 00:59
 */

namespace App\Model;

use DomainException;
use Illuminate\Database\Eloquent\Model as BaseEloquentModel;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class Eloquent
 * @package App\Model
 * This is the base class for all the Eloquent models
 * @method \App\ClassComponent\Eloquent\Sluggable createSlugIfEmpty()
 */
abstract class Eloquent extends BaseEloquentModel implements InputFilterAwareInterface
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var InputFilter|InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->toArray();
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return !$this->exists;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface|void
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    /**
     * @return InputFilter|InputFilterInterface
     */
    public function getInputFilter()
    {
        return new InputFilter();
    }

    public function save(array $options = [])
    {
        if ($this->sluggable) {
            $this->createSlugIfEmpty();
        }
        return parent::save($options);
    }


}