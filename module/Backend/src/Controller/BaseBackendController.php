<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:55
 */

namespace Backend\Controller;

use App\Helper\QueryString as QueryStringHelper;
use App\Model\Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laminas\Mvc\MvcEvent;
use App\Controller\BaseActionController;

/**
 * Class BaseController
 * @package Backend\Controller
 */
abstract class BaseBackendController extends BaseActionController
{
    const DEFAULT_LAYOUT = 'layout/backend';
    const PAGINATION_GRID_PERPAGE = 10;
    const PAGINATION_GRID_RANGE = 5;

    const ORDER_DEFAULT = 'id_desc';
    const ORDER_SPECIAL_SORTING = [];

    /*
    const ORDER_SPECIAL_SORTING = [
        // by count of relation
        'books_count' => [
            'withCount' => ['books',],
        ],
        // by relation name
        'author_name' => [
            'join' => [
                'table' => 'author',
                'local' => 'book.id_author',
                'sign' => '=',
                'foreign' => 'author.id',
            ],
            'relationColumn' => 'name',
            'continue' => true,
        ],
    ];
    */


    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $e)
    {
        // setting layout to self::DEFAULT_LAYOUT
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate($this->getDefaultLayout());

        $routeMatch = $e->getRouteMatch();

        if ($routeMatch && $routeMatch->getParam('action') !== 'login' && !$this->getSessionService()->isAdminLogged()) {
            $this->redirect()->toRoute('backend/login');
        }

        $uri = $this->getRequest()->getUri();

        $this->layout()->setVariables([
            // whether admin is logged or not
            'isAdminLogged' => $this->getSessionService()->isAdminLogged(),
            'admin' => $this->getSessionService()->getAdmin(),

            // query
            'uri' => $uri,
            'queryString' => $uri->getPath() . ($uri->getQuery() === '' ? '' : '?' . $uri->getQuery()),
            'pathString' => $uri->getPath(),
            'parametricalString' => $uri->getQuery(),

            // ordering
            'orderBy' => $this->getRequest()->getQuery('orderBy', static::ORDER_DEFAULT),
            'defaultOrderBy' => static::ORDER_DEFAULT,
        ]);

        return parent::onDispatch($e);
    }

    /**
     * @param Builder $queryModel
     * @param string $orderString
     * @return Builder
     * @throws Exception
     */
    protected function applyOrder(Builder $queryModel, string $orderString = null): Builder
    {
        $orderString = $orderString ?? $this->layout()->getVariable('orderBy', static::ORDER_DEFAULT);

        $orderArray = QueryStringHelper::parseOrderString($orderString);

        foreach ($orderArray as $column => $direction) {
            if (array_key_exists($column, static::ORDER_SPECIAL_SORTING)) {

                $withCount = static::ORDER_SPECIAL_SORTING[$column]['withCount'] ?? [];
                if (count($withCount) > 0) {
                    $queryModel = $queryModel->withCount($withCount);
                }

                $join = static::ORDER_SPECIAL_SORTING[$column]['join'] ?? null;
                if (!is_null($join)) {
                    $relationColumn = static::ORDER_SPECIAL_SORTING[$column]['relationColumn'] ?? 'id';
                    $queryModel = $queryModel
                        ->select($queryModel->getModel()->getTable() . '.*')
                        ->join($join['table'], $join['local'], $join['sign'], $join['foreign'])
                        ->orderBy($join['table'].'.'.$relationColumn, $direction);
                }

                if (static::ORDER_SPECIAL_SORTING[$column]['continue'] ?? false) {
                    continue;
                }
            }
            $queryModel->orderBy($column, $direction);
        }
        return $queryModel;
    }

    /**
     * @param Builder $queryModel
     * @param string|null $searchTerm
     * @return Builder
     * @throws Exception
     */
    protected function applySearch(Builder $queryModel, string $searchTerm = null): Builder
    {
        $searchTerm = $searchTerm ?? $this->getRequest()->getQuery('searchTerm');

        if (is_null($searchTerm)) {
            return $queryModel; // no search request
        }

        if (!is_string($searchTerm)) {
            $message = 'Param searchTerm must be of type string';
            throw new Exception($message);
        }

        $queryModel->searchLike($searchTerm);

        return $queryModel;
    }

    /**
     * @param Builder $queryModel
     * @param array|null $filtersValues
     * @return Builder
     * @throws Exception
     */
    protected function applyFilters(Builder $queryModel, array $filtersValues = null): Builder
    {
        $filtersValues = $filtersValues ?? $this->getRequest()->getQuery('filtersValues');

        if (is_null($filtersValues) || count($filtersValues) === 0) {
            return $queryModel; // no search request
        }

        if (!is_array($filtersValues)) {
            $message = 'Param filtersValue must be of type string';
            throw new Exception($message);
        }

        foreach ($this->filtersList ?? [] as $filterCode => $filterItem) {

            if ($filterItem['special'] ?? false) {
                continue;
            }

            if ($filterItem['type'] === 'number') {

                if (!array_key_exists($filterItem['name'], $filtersValues)) {
                    continue;
                }

                $filterValue = $filtersValues[$filterItem['name']];

                if ($filterValue === '') {
                    continue;
                }

                //$queryModel->where(sprintf('`%s`.%s', $queryModel->getQuery()->from, $filterItem['column']), '=', $filterValue);
                $queryModel->whereRaw(sprintf('`%s`.%s = ?', $queryModel->getQuery()->from, $filterItem['column']), [$filterValue,]);
            }

            if ($filterItem['type'] === 'text') {

                if (!array_key_exists($filterItem['name'], $filtersValues)) {
                    continue;
                }

                $filterValue = $filtersValues[$filterItem['name']];

                if ($filterValue === '') {
                    continue;
                }

                //$queryModel->where(sprintf('`%s`.%s', $queryModel->getQuery()->from, $filterItem['column']), 'LIKE', '%'.$filterValue.'%');
                $queryModel->whereRaw(sprintf('`%s`.%s LIKE ?', $queryModel->getQuery()->from, $filterItem['column']), ['%'.$filterValue.'%',]);
            }
        }

        return $queryModel;
    }



}