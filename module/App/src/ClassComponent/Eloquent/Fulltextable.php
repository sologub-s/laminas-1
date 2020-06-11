<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 06.06.2020
 * Time: 01:25
 */

namespace App\ClassComponent\Eloquent;

use App\Model\Searcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait Fulltextable
 * @package App\ClassComponent\Eloquent
 */
trait Fulltextable
{
    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            self::upsertFulltext($model);
        });

        self::updated(function ($model) {
            self::upsertFulltext($model);
        });

        self::deleted(function ($model) {
            self::deleteFulltext($model);
        });
    }

    /**
     * @param static $model
     */
    protected static function upsertFulltext($model)
    {
        if (count($model->searchSourceColumns ?? []) === 0) {
            return;
        }

        $entity_id = $model->getKey();
        if (is_null($entity_id)) {
            return;
        }
        $entity_type = $model->getTable();

        $searcher = Searcher::firstOrNew([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
        ]);

        $search_string = [];
        foreach ($model->searchSourceColumns as $searchSourceColumn) {
            $search_string[] = (string)$model->{$searchSourceColumn};
        }

        $searcher->fill([
            'entity_id' => $entity_id,
            'entity_type' => $entity_type,
            'search_string' => mb_strtolower(implode(' ', $search_string)),
        ]);

        $searcher->save();
    }

    /**
     * @param static $model
     */
    protected static function deleteFulltext($model)
    {
        $entity_id = $model->getKey();
        if (is_null($entity_id)) {
            return;
        }
        $entity_type = $model->getTable();

        Searcher
            ::where('entity_type', '=', $entity_type)
            ->where('entity_id', '=', $entity_id)
            ->delete();
    }

    /**
     * @param static|Builder $query
     * @param string $searchTerm
     * @return mixed
     */
    public function scopeSearchLike($query, string $searchTerm = '')
    {
        $table = $query instanceof Builder ? $query->getModel()->getTable() : $query->getTable();

        if ($searchTerm === '') {
            return $query->whereIn($table . '.id', []);
        }

        $entity_type = $this->getTable();

        $searchTerm = mb_strtolower($searchTerm);

        $searcher = (new Searcher())
            ->where('entity_type', '=', $entity_type)
            ->where('search_string', 'LIKE', '%'.$this->likeWildcards($searchTerm).'%')
            ->get();

        /** @var Collection $searcher */

        $entity_ids = array_map(function ($entity) {
            /** @var static $entity */
            return $entity['entity_id'];
        }, $searcher->toArray());

        return $query->whereIn($table . '.id', $entity_ids);

    }

    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchFulltext($query, $term)
    {
        $columns = implode(',', $this->searchable);

        $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));

        return $query;
    }

    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    protected function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 3) {
                $words[$key] = '+' . $word . '*';
            }
        }

        return implode(' ', $words);
    }

    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    protected function likeWildcards($term)
    {
        // removing symbols used by MySQL like
        $reservedSymbols = ['%', '_',];

        foreach ($reservedSymbols as $reservedSymbol) {
            $term = str_replace($reservedSymbol, '#'.$reservedSymbol, $term);
        }

        return $term;
    }
}