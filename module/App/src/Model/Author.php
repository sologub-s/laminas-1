<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 26.05.2020
 * Time: 01:09
 */

namespace App\Model;

use App\ClassComponent\Eloquent\Sluggable;
use App\ClassComponent\Eloquent\Fulltextable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator;
use App\Validator as CustomValidator;

/**
 * Class Author
 * @package App\Model
 */
final class Author extends Eloquent
{
    use Sluggable;
    use Fulltextable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'author';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id' => null,
        'name' => '',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        //'id',
        'name',
        'slug',
    ];

    /**
     * @var string[]
     */
    protected $searchSourceColumns = [
        'name',
        'slug',
    ];

    /**
     * @var string[]
     */
    protected $sluggableOptions = [
        'sourceField' => 'name',
    ];

    /**
     * Book relation 1-to-*
     *
     * @return HasMany
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'id_author');
    }

    /**
     * @inheritDoc
     */
    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 188,
                    ],
                ],
                [
                    'name' => Validator\Db\NoRecordExists::class,
                    'options' => [
                        'table' => $this->getTable(),
                        'field' => 'slug',
                        'exclude' => [
                            'field' => 'id',
                            'value' => (int) $this->id,
                        ],
                        'adapter' => \App::getDbAdapter(),
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'slug',
            'required' => false,
            'validators' => [
                [
                    'name' => CustomValidator\Slug::class,
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }


}