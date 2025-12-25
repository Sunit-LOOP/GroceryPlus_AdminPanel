<?php
// Footer include for admin pages
?>
            </div> <!-- end main-content -->
        </main> <!-- end admin-main -->
    </div> <!-- end admin-layout -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dashboard JavaScript -->
    <script>
        // Live clock update
        function updateClock() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('en-US', options);
        }

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);

        // Order Status Chart
        <?php if ($total_orders > 0): ?>
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Shipped', 'Delivered'],
                datasets: [{
                    data: [<?php echo $pending_orders; ?>, <?php echo $processing_orders; ?>, <?php echo $shipped_orders; ?>, <?php echo $delivered_orders; ?>],
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#007bff',
                        '#28a745'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
        <?php endif; ?>

        // Auto-refresh alerts every 30 seconds
        setInterval(() => {
            // Could add AJAX call here to refresh alerts
        }, 30000);
    </script>

    <!-- Mobile sidebar toggle -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="overlay" onclick="closeSidebar()"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.querySelector('.overlay');
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        }

        function closeSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.querySelector('.overlay');
            sidebar.classList.remove('show');
            overlay.style.display = 'none';
        }

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
