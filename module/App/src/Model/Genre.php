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
 * Class Genre
 * @package App\Model
 */
final class Genre extends Eloquent
{
    use Sluggable;
    use Fulltextable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'genre';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id' => null,
        'title' => '',
        'slug' => '',
        'description' => '',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        //'id',
        'title',
        'slug',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $searchSourceColumns = [
        'title',
        'slug',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $sluggableOptions = [
        'sourceField' => 'title',
    ];

    /**
     * Book relation *-to-*
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_to_genre', 'id_genre', 'id_book');
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
            'name' => 'description',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }


}