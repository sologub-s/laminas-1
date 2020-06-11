<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 23:15
 */

namespace Backend\Controller;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Helper\QueryString as QueryStringHelper;
use App\Model\Book;
use App\Model\Author;
use App\Model\Genre;
use App\Service\Pagination;
use Backend\Form\BookForm;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\View;

/**
 * Class BookController
 * @package Backend\Controller
 */
class BookController extends BaseBackendController
{
    const ORDER_DEFAULT = 'updated_at_desc';

    const ORDER_SPECIAL_SORTING = [
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

    /**
     * @return array
     * @throws \Exception
     */
    public function listAction()
    {
        $this->layout()->setVariables(['ddd' => 333,]);

        $page = (int)$this->getRequest()->getQuery('page', 1);
        $skip = ($page - 1) * self::PAGINATION_GRID_PERPAGE;
        $filtersValues = $this->getRequest()->getQuery('filtersValues');

        $itemsQuery = (new Book())
            //->where('active', 1)
            ->with(['author','genres',]);

        $itemsQuery = $this->applyOrder($itemsQuery);
        $itemsQuery = $this->applySearch($itemsQuery);
        $itemsQuery = $this->applyFilters($itemsQuery);

        $count = $itemsQuery->count();

        $pagination = new Pagination(self::PAGINATION_GRID_PERPAGE, $count, $page, self::PAGINATION_GRID_RANGE);
        $pagination->setQueryString($this->layout()->getVariable('queryString'));
        $pagination->setQueryStringMode(Pagination::QUERY_STRING_MODE_PARAM);
        $pagination->calculate();

        return [
            'items' => $itemsQuery
                ->skip($skip) // offset
                ->take(self::PAGINATION_GRID_PERPAGE) // limit
                ->get(),
            'paginationHtml' => $this->renderPartial('backend/partial/pagination', [
                'pagination' => $pagination,
            ]),
            'searchFormHtml' => $this->renderPartial('backend/partial/searchForm', [
                'searchTerm' => $this->getRequest()->getQuery('searchTerm'),
                'searchFormAction' => $this->url()->fromRoute('backend/book/list'),
            ]),
            'filtersFormHtml' => count($this->filtersList ?? []) === 0 ? '' : $this->renderPartial('backend/partial/filtersForm', [
                'filtersFormAction' => $this->url()->fromRoute('backend/book/list'),
                'filtersFormResetHref' => QueryStringHelper::queryStringParams($this->layout()->getVariable('queryString'), ['filtersValues',]),
                'filtersList' => $this->filtersList ?? [],
                'filtersValues' => $filtersValues,
            ]),
        ];
    }

    public function formAction()
    {
        // get request
        $request = $this->getRequest();

        // create form
        $form = new BookForm();
        $form->init();

        $id = $this->params()->fromRoute('id');

        $possibleAuthors = (new Author())
            //->where('active', 1)
            ->orderBy('name', 'asc')
            ->get();

        $possibleGenres = (new Genre())
            //->where('active', 1)
            ->orderBy('title', 'asc')
            ->get();

        $viewModel = [
            'form' => $form,
            'errorMessage' => '',
            'entityId' => $id,

            'possibleAuthors' => $possibleAuthors,
            'possibleGenres' => $possibleGenres,
        ];

        // if !$id then create new object else load object from db
        $book = Book::find($id) ?? new Book;

        $authorsValueOptions = [];
        foreach ($possibleAuthors as $possibleAuthor) {
            $authorsValueOptions[$possibleAuthor->id]
                = sprintf('%s (%s) (%d)', $possibleAuthor->name, $possibleAuthor->slug, $possibleAuthor->id);
        }
        $form->get('id_author')->setValueOptions($authorsValueOptions);

        $genresValueOptions = [];
        foreach ($possibleGenres as $possibleGenre) {
            $genresValueOptions[$possibleGenre->id]
                = sprintf('%s (%s) (%d)', $possibleGenre->title, $possibleGenre->slug, $possibleGenre->id);
        }
        $form->get('genres')->setValueOptions($genresValueOptions);

        // set correct input filter to form
        $form->setInputFilter($book->getInputFilter());

        // set data to form
        $form->setData($book->getArrayCopy());

        $selectedGenresIds = (new Capsule)
            ->table('book_to_genre')
            ->where('id_book', '=', $book->getKey())
            ->pluck('id_genre')
            ->toArray();
        $form->get('genres')->setValue($selectedGenresIds);

        // if request is not POST
        if (!$request->isPost()) {
            return $viewModel;
        }

        // set data to form
        $form->setData($request->getPost());

        // if form is not valid
        if (!$form->isValid()) {
            // show form and terminate
            return $viewModel;
        }

        // save object
        $book->fill($form->getData());

        $genresIds = $form->getData()['genres'] ?? [];

        try {
            if (!$book->save()) {
                $message = 'Cannot save entity';
                $viewModel['errorMessage'] = $message;
                return $viewModel;
            }

            if (!$book->genres()->sync($genresIds)) {
                $message = 'Cannot sync genres';
                $viewModel['errorMessage'] = $message;
                return $viewModel;
            }
        } catch (\Exception $e) {
            $message = sprintf('Cannot save entity because: %s', $e->getMessage());
            $viewModel['errorMessage'] = $message;
            return $viewModel;
        }

        return $this->redirect()->toRoute(
            is_null($this->params()->fromPost('submit_and_new')) ? 'backend/book/list' : 'backend/book/add',
        );
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        Book::destroy($id);

        return $this->redirect()->toRoute(
            'backend/book/list'
        );
    }

    ////////////////////////////////////////////////////////////////////
    /// service methods
    ////////////////////////////////////////////////////////////////////

    protected $filtersList = [
        'id' => [
            'type' => 'number',
            'name' => 'id',
            'column' => 'id',
            'label' => 'ID',
            'placeholder' => 'type ID...',
        ],

        'name' => [
            'type' => 'text',
            'name' => 'title',
            'column' => 'title',
            'label' => 'Title',
            'placeholder' => 'type Title...',
        ],

        'author_name' => [
            'special' => true,
            'type' => 'text',
            'name' => 'author_name',
            'relation' => ['author',],
            'label' => 'Author name',
            'placeholder' => 'type Author name...',
        ],
    ];

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

        $queryModel = parent::applyFilters($queryModel, $filtersValues);

        // author_name
        if (array_key_exists('author_name', $filtersValues)) {
            $filterValue = $filtersValues['author_name'];
            if ($filterValue !== '') {
                $authors = (new Author())
                    ->newQuery()
                    ->select('id')
                    ->where('name', 'LIKE', '%' . $filterValue . '%')
                    ->get()
                    ->toArray();

                $authorsIds = array_map(function ($author) {
                    return $author['id'];
                }, $authors);
                $queryModel = $queryModel->whereIn($queryModel->getModel()->getTable().'.id_author', $authorsIds);
            }
        }

        return $queryModel;
    }
}