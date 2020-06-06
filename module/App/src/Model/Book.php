<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 23:04
 */

namespace App\Model;

use App\ClassComponent\Eloquent\Fulltextable;
use App\ClassComponent\Eloquent\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator;
use App\Validator as CustomValidator;

/**
 * Class Book
 * @package App\Model
 */
final class Book extends Eloquent
{
    use Sluggable;
    use Fulltextable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id' => null,
        'title' => '',
        'id_author' => null,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        //'id',
        'title',
        'slug',
        'id_author',
    ];

    /**
     * @var string[]
     */
    protected $sluggableOptions = [
        'sourceField' => 'title',
    ];

    /**
     * Author relation *-to-1
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class, 'id_author');
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
            'name' => 'title',
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

        $inputFilter->add([
            'name' => 'id_author',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Db\RecordExists::class,
                    'options' => [
                        'table' => (new Author)->getTable(),
                        'field' => 'id',
                        'adapter' => \App::getDbAdapter(),
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }


}