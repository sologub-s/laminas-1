<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:56
 */

namespace Backend\Controller;

use App\Model\Author;
use App\Service\Pagination;
use Backend\Form\AuthorForm;
use Exception;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\View;

/**
 * Class AuthorController
 * @package Backend\Controller
 */
class AuthorController extends BaseBackendController
{
    const ORDER_DEFAULT = 'updated_at_desc';

    const ORDER_SPECIAL_SORTING = [
        'books_count' => [
            'withCount' => ['books',],
        ],
    ];

    /**
     * @return array
     * @throws Exception
     */
    public function listAction()
    {
        $this->layout()->setVariables(['ddd' => 333,]);

        $page = (int)$this->getRequest()->getQuery('page', 1);
        $skip = ($page - 1) * self::PAGINATION_GRID_PERPAGE;

        $itemsQuery = (new Author)
            //->where('active', 1)
            ->with(['books',]);

        $itemsQuery = $this->applyOrder($itemsQuery);

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
        ];
    }

    public function formAction()
    {
        // get request
        $request = $this->getRequest();

        // create form
        $form = new AuthorForm();
        $form->init();

        $id = $this->params()->fromRoute('id');

        $viewModel = [
            'form' => $form,
            'errorMessage' => '',
            'entityId' => $id,
        ];

        // if !$id then create new object else load object from db
        $author = Author::find($id) ?? new Author;

        // set correct input filter to form
        $form->setInputFilter($author->getInputFilter());

        // set data to form
        $form->setData($author->getArrayCopy());

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
        $author->fill($form->getData());

        try {
            if (!$author->save()) {
                $message = 'Cannot save entity';
                $viewModel['errorMessage'] = $message;
                return $viewModel;
            }
        } catch (Exception $e) {
            $message = sprintf('Cannot save entity because: %s', $e->getMessage());
            $viewModel['errorMessage'] = $message;
            return $viewModel;
        }

        return $this->redirect()->toRoute(
            is_null($this->params()->fromPost('submit_and_new')) ? 'backend/author/list' : 'backend/author/add',
        );
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        Author::destroy($id);

        return $this->redirect()->toRoute(
            'backend/author/list'
        );
    }
}