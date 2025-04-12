document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for reports
    const reportsTable = $('#reportsTable').DataTable({
        ajax: '../../includes/api/reports/get_reports.php',
        columns: [
            { data: 'id' },
            { data: 'type' },
            { data: 'location' },
            { data: 'status' },
            { data: 'created_at' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary view-report" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success update-status" data-id="${row.id}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-report" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Handle report filter buttons
    document.querySelectorAll('.report-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.dataset.status;
            reportsTable.column(3).search(status).draw();
            
            // Update active state
            document.querySelectorAll('.report-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Handle view report
    $('#reportsTable').on('click', '.view-report', function() {
        const reportId = $(this).data('id');
        $.get('../../includes/api/reports/get_report.php', { id: reportId }, function(data) {
            $('#reportDetailModal').modal('show');
            // Populate modal with report details
            $('#reportType').text(data.type);
            $('#reportLocation').text(data.location);
            $('#reportStatus').text(data.status);
            $('#reportDescription').text(data.description);
            $('#reportCreatedAt').text(data.created_at);
        });
    });

    // Handle status update
    $('#reportsTable').on('click', '.update-status', function() {
        const reportId = $(this).data('id');
        $.post('../../includes/api/reports/update_status.php', { 
            id: reportId,
            status: 'resolved'
        }, function(response) {
            if(response.success) {
                reportsTable.ajax.reload();
                showNotification('Report status updated successfully', 'success');
            } else {
                showNotification('Failed to update report status', 'error');
            }
        });
    });

    // Handle report deletion
    $('#reportsTable').on('click', '.delete-report', function() {
        if(confirm('Are you sure you want to delete this report?')) {
            const reportId = $(this).data('id');
            $.post('../../includes/api/reports/delete_report.php', { 
                id: reportId 
            }, function(response) {
                if(response.success) {
                    reportsTable.ajax.reload();
                    showNotification('Report deleted successfully', 'success');
                } else {
                    showNotification('Failed to delete report', 'error');
                }
            });
        }
    });

    // Handle new report submission
    $('#newReportForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '../../includes/api/reports/add_report.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#newReportModal').modal('hide');
                    reportsTable.ajax.reload();
                    showNotification('Report added successfully', 'success');
                    this.reset();
                } else {
                    showNotification('Failed to add report', 'error');
                }
            }
        });
    });

    // Utility function for notifications
    function showNotification(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.querySelector('.toast-container').appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }
}); 