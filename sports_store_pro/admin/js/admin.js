
document.addEventListener('DOMContentLoaded', function () {
    // Toggle sidebar
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // Dropdown menus
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;

            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('show');
                }
            });

            menu.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';

                    // Remove error styling on input
                    field.addEventListener('input', function () {
                        this.style.borderColor = '#ddd';
                    });
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('يرجى ملء جميع الحقول المطلوبة');
            }
        });
    });

    // Confirm deletion
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            if (!confirm('هل أنت متأكد من الحذف؟')) {
                e.preventDefault();
            }
        });
    });

    // Image preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let preview = input.parentNode.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview';
                        preview.style.cssText = 'max-width: 200px; max-height: 200px; margin-top: 10px; border-radius: 10px;';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Charts (if Chart.js is included)
    if (typeof Chart !== 'undefined') {
        // Sales chart
        const salesChartCanvas = document.getElementById('salesChart');
        if (salesChartCanvas) {
            new Chart(salesChartCanvas, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'المبيعات',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
});

// Helper functions
function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

    document.body.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, 3000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX helper
function ajaxRequest(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data ? new URLSearchParams(data) : null
    })
        .then(response => response.json())
        .catch(error => {
            console.error('Error:', error);
            showAlert('حدث خطأ في الاتصال', 'error');
        });
}
