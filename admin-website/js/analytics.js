// Analytics functionality
async function loadAnalytics() {
    try {
        const response = await api.getAnalytics();
        const data = response.data;

        renderAnalytics(data);
    } catch (error) {
        console.error('Error loading analytics:', error);
        showAlert('Error loading analytics', 'danger');
    }
}

function renderAnalytics(data) {
    const content = document.getElementById('analyticsContent');

    const analyticsHTML = `
        <div class="row">
            <!-- Revenue Analytics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-dollar-sign"></i> Revenue Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Revenue:</strong> ${formatCurrency(data.total_revenue || 0)}
                        </div>
                        <div class="mb-3">
                            <strong>Today's Revenue:</strong> ${formatCurrency(data.today_revenue || 0)}
                        </div>
                        <div class="mb-3">
                            <strong>Average Order Value:</strong> ${formatCurrency(data.average_order_value || 0)}
                        </div>
                        <div class="mb-3">
                            <strong>Revenue Growth:</strong>
                            <span class="${(data.revenue_growth || 0) >= 0 ? 'text-success' : 'text-danger'}">
                                ${(data.revenue_growth || 0) >= 0 ? '+' : ''}${(data.revenue_growth || 0).toFixed(2)}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Analytics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart"></i> Order Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Orders:</strong> ${data.total_orders || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Today's Orders:</strong> ${data.today_orders || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Pending Orders:</strong> ${data.pending_orders || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Completed Orders:</strong> ${data.completed_orders || 0}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- User Analytics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-users"></i> User Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Users:</strong> ${data.total_users || 0}
                        </div>
                        <div class="mb-3">
                            <strong>New Users Today:</strong> ${data.today_new_users || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Active Users:</strong> ${data.active_users || 0}
                        </div>
                        <div class="mb-3">
                            <strong>User Growth:</strong>
                            <span class="${(data.user_growth || 0) >= 0 ? 'text-success' : 'text-danger'}">
                                ${(data.user_growth || 0) >= 0 ? '+' : ''}${(data.user_growth || 0).toFixed(2)}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Analytics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-box"></i> Product Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Products:</strong> ${data.total_products || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Top Category:</strong> ${data.top_category || 'N/A'}
                        </div>
                        <div class="mb-3">
                            <strong>Low Stock Items:</strong> ${data.low_stock_count || 0}
                        </div>
                        <div class="mb-3">
                            <strong>Out of Stock:</strong> ${data.out_of_stock_count || 0}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts/Graphs would go here -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Recent Trends</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Advanced charts and graphs can be implemented here using Chart.js or similar libraries.</p>
                        <div class="alert alert-info">
                            <strong>Future Enhancement:</strong> Add interactive charts for:
                            <ul class="mb-0 mt-2">
                                <li>Revenue over time</li>
                                <li>Order trends</li>
                                <li>Popular products</li>
                                <li>User registration growth</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = analyticsHTML;
}