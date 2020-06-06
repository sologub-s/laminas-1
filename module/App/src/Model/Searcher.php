<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 06.06.2020
 * Time: 01:56
 */

namespace App\Model;

use App\ClassComponent\Eloquent\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator;
use App\Validator as CustomValidator;

/**
 * Class Searcher
 * @package App\Model
 */
final class Searcher extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'searcher';

    /**
     * @var bool 
     */
    public $timestamps = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id' => null,
        'entity_type' => '',
        'entity_id' => 0,
        'search_string' => '',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        //'id',
        'entity_type',
        'entity_id',
        'search_string',
    ];
}