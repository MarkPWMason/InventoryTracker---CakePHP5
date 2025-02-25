<?php

declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductsTable Test Case
 */
class ProductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductsTable
     */
    protected $Products;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Products',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Products') ? [] : ['className' => ProductsTable::class];
        $this->Products = $this->getTableLocator()->get('Products', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Products);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductsTable::validationDefault()
     */


    public function testValidationDefault(): void
    {
        //Valid data
        $product = $this->Products->newEntity([
            'name' => 'Valid Product',
            'quantity' => 50,
            'price' => 99.99,
            'status' => 'in_stock',
        ]);
        $this->assertEmpty($product->getErrors());

        //Name is too short
        $product = $this->Products->newEntity([
            'name' => 'A',
            'quantity' => 50,
            'price' => 99.99,
            'status' => 'in_stock',
        ]);
        $this->assertNotEmpty($product->getErrors());

        //Invalid quantity
        $product = $this->Products->newEntity([
            'name' => 'Valid Product',
            'quantity' => -50,
            'price' => 99.99,
            'status' => 'in_stock',
        ]);
        $this->assertNotEmpty($product->getErrors());

        //Price too high
        $product = $this->Products->newEntity([
            'name' => 'Valid Product',
            'quantity' => -50,
            'price' => 100000,
            'status' => 'in_stock',
        ]);
        $this->assertNotEmpty($product->getErrors());
    }

    public function testCustomValidation(): void
    {
        // Price > 100 requires quantity >= 10
        $product = $this->Products->newEntity([
            'name' => 'Expensive Product',
            'quantity' => 5,
            'price' => 101,
        ]);
        $this->assertNotEmpty($product->getErrors());

        // Name contains promo requires a price of < 50
        $product = $this->Products->newEntity([
            'name' => 'Promo Product',
            'quantity' => 50,
            'price' => 60,
        ]);
        $this->assertNotEmpty($product->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ProductsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
