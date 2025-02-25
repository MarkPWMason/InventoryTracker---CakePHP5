<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use CAKE\ORM\TableRegistry;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 */
class ProductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Products->find()->where(['deleted' => false]);

        //searching filter
        if ($this->request->getQuery('search')) {
            $search = $this->request->getQuery('search');
            $query->where(['name LIKE' => "%{$search}%"]);
        }

        //status filter
        if ($this->request->getQuery('status')) {
            $status = $this->request->getQuery('status');
            switch ($status) {
                case 'in_stock':
                    $query->where(['quantity >' => 10]);
                    break;

                case 'low_stock':
                    $query->where(['quantity >=' => 1, 'quantity <=' => 10]);
                    break;

                case 'out_of_stock':
                    $query->where(['quantity' => 0]);
                    break;
            }
        }
        $products = $this->paginate($query, [
            'limit' => 10,
            'order' => ['last_updated' => 'DESC']
        ]);

        $this->set(compact('products'));
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->autoRender = false; // Disable auto-rendering for AJAX requests
        $product = $this->Products->get($id);
        echo json_encode(['product' => $product]);
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->autoRender = false;
        $product = $this->Products->newEmptyEntity();

        if ($this->request->is('post')) {
            $product = $this->Products->patchEntity($product, $this->request->getData());

            if ($this->Products->save($product)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'errors' => $product->getErrors()]);
            }
        }
    }


    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->autoRender = false;
        $product = $this->Products->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->getData());

            if ($this->Products->save($product)) {
                echo json_encode(['success' => true]);
            } else {
                $errors = [];
                foreach ($product->getErrors() as $field => $errorMessages) {
                    foreach ($errorMessages as $message) {
                        $errors[] = $field . ': ' . $message;
                    }
                }
                echo json_encode(['success' => false, 'errors' => $errors]);
            }
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->response = $this->response->withType('application/json');

        $product = $this->Products->get($id);
        try {
            if ($this->Products->delete($product)) {
                $response = ['success' => true, 'message' => 'Product deleted successfully.'];
            } else {
                $response = ['success' => false, 'errors' => ['The product could not be deleted. Please, try again.']];
            }
        } catch (\Exception $e) {
            $response = ['success' => false, 'errors' => ['An error occurred: ' . $e->getMessage()]];
        }

        return $this->response->withStringBody(json_encode($response));
    }
}
