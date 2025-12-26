// Orders management
async function loadOrders() {
    try {
        const response = await api.getOrders();
        const orders = response.data.orders || response.data;

        renderOrders(orders);
    } catch (error) {
        console.error('Error loading orders:', error);
        showAlert('Error loading orders', 'danger');
    }
}

function renderOrders(orders) {
    const content = document.getElementById('ordersContent');

    if (!orders || orders.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No orders found.</div>';
        return;
    }

    const table = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${orders.map(order => `
                        <tr>
                            <td>${order.order_id}</td>
                            <td>${order.user_name || 'N/A'}</td>
                            <td>${formatCurrency(order.total_amount || 0)}</td>
                            <td>${getOrderStatusBadge(order.status || 'pending')}</td>
                            <td>${formatDate(order.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info me-1" onclick="viewOrderDetails(${order.order_id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="dropdown d-inline">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Update Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${order.order_id}, 'processing')">Processing</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${order.order_id}, 'shipped')">Shipped</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${order.order_id}, 'delivered')">Delivered</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${order.order_id}, 'cancelled')">Cancelled</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    content.innerHTML = table;
}

async function viewOrderDetails(orderId) {
    try {
        // For now, we'll just show an alert with basic info
        // In a full implementation, you'd fetch detailed order info
        const response = await api.getOrders();
        const orders = response.data.orders || response.data;
        const order = orders.find(o => o.order_id == orderId);

        if (order) {
            const details = `
Order ID: ${order.order_id}
Customer: ${order.user_name || 'N/A'}
Total: ${formatCurrency(order.total_amount || 0)}
Status: ${order.status || 'pending'}
Date: ${formatDate(order.created_at)}
            `;
            alert(details);
        }
    } catch (error) {
        console.error('Error viewing order details:', error);
        showAlert('Error loading order details', 'danger');
    }
}

async function updateOrderStatus(orderId, status) {
    try {
        await api.updateOrder(orderId, { status });
        showAlert(`Order status updated to ${status}`, 'success');
        loadOrders(); // Reload orders to show updated status
    } catch (error) {
        console.error('Error updating order status:', error);
        showAlert('Error updating order status', 'danger');
    }
}