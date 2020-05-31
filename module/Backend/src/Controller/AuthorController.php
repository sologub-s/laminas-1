<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:56
 */

namespace Backend\Controller;

use App\Model\Author;
use Backend\Form\AuthorForm;

/**
 * Class AuthorController
 * @package Backend\Controller
 */
class AuthorController extends BaseBackendController
{
    const GRID_PERPAGE = 10;

    public function listAction()
    {
        //$this->layout()->setVariables([]);

        $page = (int)$this->getRequest()->getQuery('page');

        return [
            'items' => (new Author)
                //->where('active', 1)
                ->orderBy('updated_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('name', 'asc')
                ->skip(0) // offset
                ->take(self::GRID_PERPAGE) // limit
                ->get(),
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
        } catch (\Exception $e) {
            $message = sprintf('Cannot save entity because: %s', $e->getMessage());
            $viewModel['errorMessage'] = $message;
            return $viewModel;
        }

        return $this->redirect()->toRoute(
            'backend/author/edit',
            [
                'id' => $author->id,
            ]
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