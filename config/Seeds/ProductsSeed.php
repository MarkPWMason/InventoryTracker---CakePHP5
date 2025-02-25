<?php

declare(strict_types=1);

use Migrations\BaseSeed;
use Cake\ORM\TableRegistry;
use Migrations\AbstractSeed;


/**
 * Products seed.
 */
class ProductsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $productsTable = TableRegistry::getTableLocator()->get('Products');

        $products = [
            [
                'name' => 'Product A',
                'quantity' => 50,
                'price' => 29.99,
                'status' => 'in_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product B',
                'quantity' => 10,
                'price' => 9.99,
                'status' => 'low_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product C',
                'quantity' => 0,
                'price' => 49.99,
                'status' => 'out_of_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product D',
                'quantity' => 25,
                'price' => 19.99,
                'status' => 'in_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product E',
                'quantity' => 5,
                'price' => 14.99,
                'status' => 'low_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product F',
                'quantity' => 100,
                'price' => 99.99,
                'status' => 'in_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product G',
                'quantity' => 0,
                'price' => 79.99,
                'status' => 'out_of_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product H',
                'quantity' => 30,
                'price' => 39.99,
                'status' => 'in_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product I',
                'quantity' => 2,
                'price' => 4.99,
                'status' => 'low_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product J',
                'quantity' => 0,
                'price' => 89.99,
                'status' => 'out_of_stock',
                'last_updated' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert the sample products into the database
        foreach ($products as $productData) {
            $product = $productsTable->newEntity($productData);
            $productsTable->save($product);
        }
    }
}
