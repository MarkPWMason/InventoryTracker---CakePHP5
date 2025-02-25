<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Products Model
 *
 * @method \App\Model\Entity\Product newEmptyEntity()
 * @method \App\Model\Entity\Product newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Product> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Product get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Product findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Product patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Product> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Product|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Product saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProductsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('products');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'last_updated' => 'always',
                ],
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->minLength('name', 3, 'Name must be atleast 3 characters long')
            ->maxLength('name', 50, 'Name must be no more than 50 characters long')
            ->requirePresence('name', 'create', 'Name is required')
            ->notEmptyString('name', 'Name cannot be empty')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Product name must be unique'
            ]);

        $validator
            ->integer('quantity', 'Quantity must be an integer')
            ->requirePresence('quantity', 'create', 'Quantity is required')
            ->notEmptyString('quantity', 'Quantity cannot be empty')
            ->range('quantity', [0, 1000], 'Quantity must be between 0 and 1000');

        $validator
            ->decimal('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price')
            ->range('price', [0, 10000], 'Price must be between 0 and 10,000');

        $validator
            ->add('price', 'custom', [
                'rule' => function ($value, $context) {
                    if ($value > 100 && $context['data']['quantity'] < 10) {
                        return false;
                    }
                    return true;
                },
                'message' => 'Products with a price > 100 must have a minimum quanity of 10',
            ]);

        $validator
            ->add('name', 'custom', [
                'rule' => function ($value, $context) {
                    if (stripos($value, 'promo') !== false && $context['data']['price'] >= 50) {
                        return false;
                    }
                    return true;
                },
                'message' => 'Products with "promo" in the name must have a price less than 50',
            ]);
        return $validator;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->quantity > 10) {
            $entity->status = 'in stock';
        } elseif ($entity->quantity >= 1 && $entity->quantity <= 10) {
            $entity->status = 'low stock';
        } else {
            $entity->status = 'out of stock';
        }
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name']), ['errorField' => 'name']);

        return $rules;
    }
}
