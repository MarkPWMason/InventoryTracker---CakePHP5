<h1>Products</h1>

<div>
    <?= $this->Form->create(null, ['url' => ['action' => 'index'], 'method' => 'get']) ?>
    <?= $this->Form->control('search', [
        'label' => false,
        'placeholder' => 'Search products...',
        'value' => $this->request->getQuery('search')
    ]) ?>
    <?= $this->Form->control('status', [
        'type' => 'select',
        'options' => [
            '' => 'All Statuses',
            'in_stock' => 'In Stock',
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock'
        ],
        'default' => $this->request->getQuery('status')
    ]) ?>
    <?= $this->Form->button('Filter') ?>
    <?= $this->Form->end() ?>

</div>


<div id="notification" class="alert" style="display: none;"></div>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($products->isEmpty()): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No products found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= h($product->name) ?></td>
                    <td><?= h($product->quantity) ?></td>
                    <td>£<?= h($product->price) ?></td>
                    <td><?= h($product->status) ?></td>
                    <td><?= h($product->last_updated) ?></td>
                    <td>
                        <?= $this->Html->link('Edit', '#', ['class' => 'editProductButton', 'data-id' => $product->id, 'data-name' => $product->name, 'data-quantity' => $product->quantity, 'data-price' => $product->price]) ?>
                        <?= $this->Html->Link('Delete', '#', ['class' => 'deleteProductButton', 'data-id' => $product->id, 'data-name' => $product->name]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('« Previous') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('Next »') ?>
    </ul>
    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
</div>

<div style="margin-top: 20px;">
    <button id="addProductButton">Add New Product</button>
</div>

<div id="addProductModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, ['url' => ['action' => 'add'], 'id' => 'addProductForm']) ?>
                <?= $this->Form->control('name', ['label' => 'Name', 'required' => true]) ?>
                <?= $this->Form->control('quantity', ['label' => 'Quantity', 'type' => 'number', 'required' => true]) ?>
                <?= $this->Form->control('price', ['label' => 'Price', 'type' => 'number', 'step' => '0.01', 'required' => true]) ?>
                <?= $this->Form->button('Submit') ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<div id="editProductModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, ['url' => ['action' => 'edit'], 'id' => 'editProductForm']) ?>
                <?= $this->Form->hidden('id', ['id' => 'editProductId']) ?>
                <?= $this->Form->control('name', ['label' => 'Name', 'id' => 'editProductName', 'required' => true]) ?>
                <?= $this->Form->control('quantity', ['label' => 'Quantity', 'type' => 'number', 'id' => 'editProductQuantity', 'required' => true]) ?>
                <?= $this->Form->control('price', ['label' => 'Price', 'type' => 'number', 'step' => '0.01', 'id' => 'editProductPrice', 'required' => true]) ?>
                <?= $this->Form->button('Save Changes') ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<div id="deleteProductModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product?</p>
                <p id="deleteProductName"></p>
            </div>
            <div class="modal-footer">
                <button id="confirmDelete" class="btn btn-danger">Yes, Delete</button>
                <button id="cancelDelete" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const csrfToken = document.querySelector('meta[name="csrfToken"]').getAttribute('content');
        const addProductButton = document.getElementById("addProductButton");
        const addProductModal = new bootstrap.Modal(document.getElementById("addProductModal"));
        const addProductForm = document.getElementById("addProductForm");

        addProductButton.addEventListener("click", () => {
            addProductModal.show();
        });

        addProductForm.addEventListener("submit", function(e) {
            e.preventDefault();
            fetch(addProductForm.action, {
                    method: "POST",
                    body: new FormData(addProductForm),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showNotification(data.errors.join("<br>"), 'danger');
                    }
                });
        });

        // Edit Product Modal
        const editProductModal = new bootstrap.Modal(document.getElementById("editProductModal"));
        const editProductForm = document.getElementById("editProductForm");

        document.querySelectorAll(".editProductButton").forEach((button) => {
            button.addEventListener("click", function() {
                document.getElementById("editProductId").value = this.dataset.id;
                document.getElementById("editProductName").value = this.dataset.name;
                document.getElementById("editProductQuantity").value = this.dataset.quantity;
                document.getElementById("editProductPrice").value = this.dataset.price;
                editProductModal.show();
            });
        });

        editProductForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const productId = document.getElementById("editProductId").value;
            fetch(`/products/edit/${productId}`, {
                    method: "POST",
                    body: new FormData(editProductForm),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showNotification(data.errors.join("<br>"), 'danger');
                    }
                });
        });

        const deleteProductModal = new bootstrap.Modal(document.getElementById("deleteProductModal"));
        const deleteProductName = document.getElementById("deleteProductName");
        let productIdToDelete;

        document.querySelectorAll(".deleteProductButton").forEach((button) => {
            button.addEventListener("click", function() {
                productIdToDelete = this.dataset.id;
                deleteProductName.textContent = "Product: " + this.dataset.name;
                deleteProductModal.show();
            });
        });

        document.getElementById("confirmDelete").addEventListener("click", () => {
            fetch(`/products/delete/${productIdToDelete}`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken,
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showNotification(data.errors.join("<br>"), 'danger');
                    }
                });
        });
    });
</script>