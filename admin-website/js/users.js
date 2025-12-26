// Users management
async function loadUsers() {
    try {
        const response = await api.getUsers();
        const users = response.data.users || response.data;

        renderUsers(users);
    } catch (error) {
        console.error('Error loading users:', error);
        showAlert('Error loading users', 'danger');
    }
}

function renderUsers(users) {
    const content = document.getElementById('usersContent');

    if (!users || users.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No users found.</div>';
        return;
    }

    const table = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.user_name}</td>
                            <td>${user.user_email}</td>
                            <td>${user.user_phone || '-'}</td>
                            <td>${getUserTypeBadge(user.user_type)}</td>
                            <td>${formatDate(user.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editUser(${user.user_id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.user_id}, '${user.user_name}')">
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

function editUser(userId) {
    // For now, just show an alert - in full implementation you'd have a modal
    alert(`Edit user ${userId} - Feature coming soon!`);
}

async function deleteUser(userId, userName) {
    if (!confirmDelete(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        return;
    }

    try {
        await api.deleteUser(userId);
        showAlert('User deleted successfully', 'success');
        loadUsers();
    } catch (error) {
        console.error('Error deleting user:', error);
        showAlert('Error deleting user', 'danger');
    }
}