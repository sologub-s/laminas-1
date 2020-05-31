<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 28.05.2020
 * Time: 18:55
 */

namespace App\ClassComponent\Eloquent;

use App\Helper\Slug;

/**
 * Trait Sluggable
 * @package App\ClassComponent\Eloquent
 */
trait Sluggable
{
    /**
     * @var bool
     */
    protected $sluggable = true;

    protected $baseSluggableOptions = [
        'field' => 'slug',
        'sourceField' => 'title',
        'unique' => true,
    ];

    /**
     * @return string[]
     */
    public function getSluggableOptions()
    {
        return array_merge(
            $this->baseSluggableOptions,
            $this->sluggableOptions ?? [],
        );
    }

    public function createSlugIfEmpty()
    {
        $options = $this->getSluggableOptions();

        //if (!is_null($this->{$options['field']}) && $this->{$options['field']} !== '') {
        if (is_null($this->{$options['field']}) || $this->{$options['field']} === '') {
            //return;
            $this->{$options['field']} = Slug::createSlug($this->{$options['sourceField']}, 'ru');
        }

        //$this->{$options['field']} = Slug::createSlug($this->{$options['sourceField']}, 'ru');

        if ($options['unique']) {
            $baseSlug = $this->{$options['field']};
            $i = 2;
            while(true) {
                $alreadyExisted = (new static)
                    ->where($options['field'], $this->{$options['field']})
                    ->where('id', '!=' , $this->id)
                    ->first();
                if (is_null($alreadyExisted)) {
                    break;
                }
                $this->{$options['field']} = $baseSlug . '-' . $i;
                $i++;
            }
        }
    }
}