<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 23:15
 */

namespace Backend\Controller;

use App\Model\Book;
use App\Model\Author;
use App\Service\Pagination;
use Backend\Form\BookForm;
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

        $itemsQuery = (new Book())
            //->where('active', 1)
            ->with(['author',]);

        $itemsQuery = $this->applyOrder($itemsQuery);
        $itemsQuery = $this->applySearch($itemsQuery);

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

        $viewModel = [
            'form' => $form,
            'errorMessage' => '',
            'entityId' => $id,

            'possibleAuthors' => $possibleAuthors,
        ];

        // if !$id then create new object else load object from db
        $book = Book::find($id) ?? new Book;

        $authorsValueOptions = [];
        foreach ($possibleAuthors as $possibleAuthor) {
            $authorsValueOptions[$possibleAuthor->id]
                = sprintf('%s (%s) (%d)', $possibleAuthor->name, $possibleAuthor->slug, $possibleAuthor->id);
        }
        $form->get('id_author')->setValueOptions($authorsValueOptions);

        // set correct input filter to form
        $form->setInputFilter($book->getInputFilter());

        // set data to form
        $form->setData($book->getArrayCopy());

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

        try {
            if (!$book->save()) {
                $message = 'Cannot save entity';
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
}