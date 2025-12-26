// Dashboard functionality
async function loadDashboard() {
    try {
        const response = await api.getAnalytics();
        const data = response.data;

        // Update metrics
        document.getElementById('totalUsers').textContent = data.total_users || 0;
        document.getElementById('totalProducts').textContent = data.total_products || 0;
        document.getElementById('totalOrders').textContent = data.total_orders || 0;
        document.getElementById('todayRevenue').textContent = formatCurrency(data.today_revenue || 0);

        // Update dashboard content with additional metrics
        const dashboardContent = document.getElementById('dashboardContent');

        // Add recent activity section
        const recentSection = `
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Today's Orders: ${data.today_orders || 0}</small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">New Users Today: ${data.today_new_users || 0}</small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Pending Orders: ${data.pending_orders || 0}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line"></i> Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Avg Order Value: ${formatCurrency(data.average_order_value || 0)}</small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Top Category: ${data.top_category || 'N/A'}</small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Low Stock Items: ${data.low_stock_count || 0}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append additional content
        const existingCards = dashboardContent.querySelector('.row');
        existingCards.insertAdjacentHTML('afterend', recentSection);

    } catch (error) {
        console.error('Error loading dashboard:', error);
        showAlert('Error loading dashboard data', 'danger');
    }
}