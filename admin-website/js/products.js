// Products management
let categories = []; // Store categories for dropdown

async function loadProducts() {
    try {
        // Load categories first for dropdown
        await loadCategoriesForDropdown();

        // Load products
        const response = await api.getProducts();
        const products = response.data.products;

        renderProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
        showAlert('Error loading products', 'danger');
    }
}

async function loadCategoriesForDropdown() {
    try {
        const response = await api.getCategories();
        categories = response.data.categories;
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function renderProducts(products) {
    const content = document.getElementById('productsContent');

    if (products.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No products found.</div>';
        return;
    }

    const table = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(product => `
                        <tr>
                            <td>${product.product_id}</td>
                            <td>${product.product_name}</td>
                            <td>${product.category_name}</td>
                            <td>${formatCurrency(product.price)}</td>
                            <td>
                                <span class="badge ${product.stock_quantity > 10 ? 'bg-success' : product.stock_quantity > 0 ? 'bg-warning' : 'bg-danger'}">
                                    ${product.stock_quantity}
                                </span>
                            </td>
                            <td>
                                <span class="badge ${product.stock_quantity > 0 ? 'bg-success' : 'bg-danger'}">
                                    ${product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(${product.product_id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.product_id}, '${product.product_name}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    content.innerHTML = table;
}

function showProductModal(productId = null) {
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const form = document.getElementById('productForm');
    const title = document.getElementById('productModalTitle');

    // Reset form
    form.reset();

    if (productId) {
        // Edit mode
        title.textContent = 'Edit Product';
        loadProductForEdit(productId);
    } else {
        // Add mode
        title.textContent = 'Add Product';
        document.getElementById('productId').value = '';
    }

    // Populate category dropdown
    const categorySelect = document.getElementById('productCategory');
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    categories.forEach(category => {
        categorySelect.innerHTML += `<option value="${category.category_id}">${category.category_name}</option>`;
    });

    modal.show();
}

async function loadProductForEdit(productId) {
    try {
        const response = await api.getProducts();
        const product = response.data.products.find(p => p.product_id == productId);

        if (product) {
            document.getElementById('productId').value = product.product_id;
            document.getElementById('productName').value = product.product_name;
            document.getElementById('productCategory').value = product.category_id;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock_quantity;
            document.getElementById('productDescription').value = product.description || '';
        }
    } catch (error) {
        console.error('Error loading product:', error);
        showAlert('Error loading product data', 'danger');
    }
}

async function saveProduct() {
    const form = document.getElementById('productForm');
    const productId = document.getElementById('productId').value;

    const productData = {
        product_name: document.getElementById('productName').value,
        category_id: parseInt(document.getElementById('productCategory').value),
        price: parseFloat(document.getElementById('productPrice').value),
        stock_quantity: parseInt(document.getElementById('productStock').value),
        description: document.getElementById('productDescription').value
    };

    try {
        if (productId) {
            await api.updateProduct(productId, productData);
            showAlert('Product updated successfully', 'success');
        } else {
            await api.createProduct(productData);
            showAlert('Product created successfully', 'success');
        }

        // Close modal and reload products
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        loadProducts();
    } catch (error) {
        console.error('Error saving product:', error);
        showAlert('Error saving product: ' + error.message, 'danger');
    }
}

async function deleteProduct(productId, productName) {
    if (!confirmDelete(`Are you sure you want to delete "${productName}"?`)) {
        return;
    }

    try {
        await api.deleteProduct(productId);
        showAlert('Product deleted successfully', 'success');
        loadProducts();
    } catch (error) {
        console.error('Error deleting product:', error);
        showAlert('Error deleting product', 'danger');
    }
}

function editProduct(productId) {
    showProductModal(productId);
}