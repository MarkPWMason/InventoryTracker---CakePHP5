<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductsFixture
 */
class ProductsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'quantity' => 1,
                'price' => 1.5,
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-02-24 21:21:05',
                'modified' => '2025-02-24 21:21:05',
            ],
        ];
        parent::init();
    }
}
