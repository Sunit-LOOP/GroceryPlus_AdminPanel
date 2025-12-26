// Categories management
async function loadCategories() {
    try {
        const response = await api.getCategories();
        const categories = response.data.categories;

        renderCategories(categories);
    } catch (error) {
        console.error('Error loading categories:', error);
        showAlert('Error loading categories', 'danger');
    }
}

function renderCategories(categories) {
    const content = document.getElementById('categoriesContent');

    if (categories.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No categories found.</div>';
        return;
    }

    const table = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${categories.map(category => `
                        <tr>
                            <td>${category.category_id}</td>
                            <td>${category.category_name}</td>
                            <td>${category.category_description || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editCategory(${category.category_id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${category.category_id}, '${category.category_name}')">
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

function showCategoryModal(categoryId = null) {
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('categoryModalTitle');

    // Reset form
    form.reset();

    if (categoryId) {
        // Edit mode
        title.textContent = 'Edit Category';
        loadCategoryForEdit(categoryId);
    } else {
        // Add mode
        title.textContent = 'Add Category';
        document.getElementById('categoryId').value = '';
    }

    modal.show();
}

async function loadCategoryForEdit(categoryId) {
    try {
        const response = await api.getCategories();
        const category = response.data.categories.find(c => c.category_id == categoryId);

        if (category) {
            document.getElementById('categoryId').value = category.category_id;
            document.getElementById('categoryName').value = category.category_name;
            document.getElementById('categoryDescription').value = category.category_description || '';
        }
    } catch (error) {
        console.error('Error loading category:', error);
        showAlert('Error loading category data', 'danger');
    }
}

async function saveCategory() {
    const form = document.getElementById('categoryForm');
    const categoryId = document.getElementById('categoryId').value;

    const categoryData = {
        category_name: document.getElementById('categoryName').value,
        category_description: document.getElementById('categoryDescription').value
    };

    try {
        if (categoryId) {
            await api.updateCategory(categoryId, categoryData);
            showAlert('Category updated successfully', 'success');
        } else {
            await api.createCategory(categoryData);
            showAlert('Category created successfully', 'success');
        }

        // Close modal and reload categories
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        loadCategories();
    } catch (error) {
        console.error('Error saving category:', error);
        showAlert('Error saving category: ' + error.message, 'danger');
    }
}

async function deleteCategory(categoryId, categoryName) {
    if (!confirmDelete(`Are you sure you want to delete "${categoryName}"? This will affect products in this category.`)) {
        return;
    }

    try {
        await api.deleteCategory(categoryId);
        showAlert('Category deleted successfully', 'success');
        loadCategories();
    } catch (error) {
        console.error('Error deleting category:', error);
        showAlert('Error deleting category', 'danger');
    }
}

function editCategory(categoryId) {
    showCategoryModal(categoryId);
}