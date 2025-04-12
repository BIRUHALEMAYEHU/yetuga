$(document).ready(function() {
    // Sample staff data (replace with API call)
    const sampleStaffData = [
        {
            id: "TO-2024-001",
            name: "Inspector Biruh Alemayehu",
            title: "Inspector",
            type: "moteregna",
            age: 35,
            sex: "M",
            status: "available"
        },
        {
            id: "TO-2024-002",
            name: "Sergeant Kebede Bekele",
            title: "Sergeant",
            type: "patroller",
            age: 28,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-003",
            name: "Officer Almaz Tadesse",
            title: "Officer",
            type: "egregna",
            age: 30,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-004",
            name: "Inspector Yared Solomon",
            title: "Inspector",
            type: "moteregna",
            age: 42,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-005",
            name: "Officer Tigist Haile",
            title: "Officer",
            type: "patroller",
            age: 27,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-006",
            name: "Sergeant Dawit Mengistu",
            title: "Sergeant",
            type: "moteregna",
            age: 33,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-007",
            name: "Officer Bethlehem Alemu",
            title: "Officer",
            type: "egregna",
            age: 29,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-008",
            name: "Inspector Henok Girma",
            title: "Inspector",
            type: "patroller",
            age: 38,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-009",
            name: "Sergeant Sara Tekle",
            title: "Sergeant",
            type: "moteregna",
            age: 31,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-010",
            name: "Officer Abebe Desta",
            title: "Officer",
            type: "egregna",
            age: 26,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-011",
            name: "Inspector Meron Assefa",
            title: "Inspector",
            type: "patroller",
            age: 36,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-012",
            name: "Sergeant Fasil Kebede",
            title: "Sergeant",
            type: "moteregna",
            age: 34,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-013",
            name: "Officer Kidist Mulugeta",
            title: "Officer",
            type: "egregna",
            age: 28,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-014",
            name: "Inspector Samuel Tesfaye",
            title: "Inspector",
            type: "patroller",
            age: 40,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-015",
            name: "Sergeant Helen Gebre",
            title: "Sergeant",
            type: "moteregna",
            age: 32,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-016",
            name: "Officer Bereket Wendwosen",
            title: "Officer",
            type: "egregna",
            age: 29,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-017",
            name: "Inspector Rahel Tadesse",
            title: "Inspector",
            type: "patroller",
            age: 37,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-018",
            name: "Sergeant Daniel Mekonnen",
            title: "Sergeant",
            type: "moteregna",
            age: 35,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-019",
            name: "Officer Selam Hailu",
            title: "Officer",
            type: "egregna",
            age: 27,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-020",
            name: "Inspector Getachew Worku",
            title: "Inspector",
            type: "patroller",
            age: 41,
            sex: "M",
            status: "assigned"
        },
        {
            id: "TO-2024-021",
            name: "Sergeant Hiwot Bekele",
            title: "Sergeant",
            type: "moteregna",
            age: 33,
            sex: "F",
            status: "available"
        },
        {
            id: "TO-2024-022",
            name: "Officer Yonas Abebe",
            title: "Officer",
            type: "egregna",
            age: 30,
            sex: "M",
            status: "assigned"
        }
    ];

    // Initialize DataTable
    const staffTable = $('#staffTable').DataTable({
        data: sampleStaffData,
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'title' },
            { 
                data: 'type',
                render: function(data) {
                    const typeLabels = {
                        'moteregna': 'Motorcycle',
                        'patroller': 'Patroller',
                        'egregna': 'On Foot'
                    };
                    return typeLabels[data] || data;
                }
            },
            { data: 'age' },
            { data: 'sex' },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'available' ? 'bg-success' :
                                     data === 'assigned' ? 'bg-warning' : 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    const assignBtn = data.status === 'available' ?
                        `<button class="btn btn-sm btn-primary assign-btn" data-id="${data.id}">
                            <i class="bi bi-person-plus"></i> Assign
                        </button>` : '';
                    return `
                        <div class="btn-group">
                            ${assignBtn}
                            <button class="btn btn-sm btn-info view-btn" data-id="${data.id}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[1, 'asc']], // Sort by name by default
        pageLength: 10,
        responsive: true
    });

    // Toggle filter panel
    $('#filterBtn').click(function() {
        $('#filterPanel').slideToggle();
    });

    // Handle filter application
    $('#applyFilters').click(function() {
        const title = $('#titleFilter').val();
        const type = $('#typeFilter').val();
        const status = $('#statusFilter').val();

        staffTable.columns(2).search(title);
        staffTable.columns(3).search(type);
        staffTable.columns(6).search(status);
        staffTable.draw();
    });

    // Handle export
    $('#exportBtn').click(function() {
        // TODO: Implement export functionality
        alert('Export functionality will be implemented here');
    });

    // Sample routes data (replace with API call)
    const routes = [
        { id: 1, name: "Bole Road - Megenagna" },
        { id: 2, name: "Piassa - Merkato" },
        { id: 3, name: "Mexico - Stadium" }
    ];

    // Populate routes in assignment modal
    routes.forEach(route => {
        $('#routeSelect').append(`<option value="${route.id}">${route.name}</option>`);
    });

    // Handle assign button click
    $(document).on('click', '.assign-btn', function() {
        const staffId = $(this).data('id');
        const staffData = sampleStaffData.find(staff => staff.id === staffId);
        
        $('#staffId').val(staffId);
        $('#staffName').val(staffData.name);
        $('#staffStatus').val(staffData.status);
        
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        $('#startDate').val(today);
        $('#endDate').val(today);
        
        // Show modal
        $('#assignmentModal').modal('show');
    });

    // Handle assignment form submission
    $('#saveAssignment').click(function() {
        const assignmentData = {
            staffId: $('#staffId').val(),
            routeId: $('#routeSelect').val(),
            startTime: $('#startTime').val(),
            endTime: $('#endTime').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            notes: $('#assignmentNotes').val()
        };

        // Validate form
        if (!assignmentData.routeId || !assignmentData.startTime || !assignmentData.endTime ||
            !assignmentData.startDate || !assignmentData.endDate) {
            alert('Please fill in all required fields');
            return;
        }

        // TODO: Send to backend API
        console.log('Assignment data:', assignmentData);

        // Update staff status in table
        const rowData = staffTable.row($(`button[data-id="${assignmentData.staffId}"]`).closest('tr')).data();
        rowData.status = 'assigned';
        staffTable.row($(`button[data-id="${assignmentData.staffId}"]`).closest('tr')).data(rowData).draw();

        // Close modal and show success message
        $('#assignmentModal').modal('hide');
        alert('Staff member assigned successfully!');
    });

    // Handle view button click
    $(document).on('click', '.view-btn', function() {
        const staffId = $(this).data('id');
        // TODO: Implement view functionality
        alert(`View details for staff member ${staffId}`);
    });

    // Update dashboard stats when assignments change
    function updateDashboardStats() {
        const availableCount = sampleStaffData.filter(staff => staff.status === 'available').length;
        const assignedCount = sampleStaffData.filter(staff => staff.status === 'assigned').length;
        
        // TODO: Update dashboard stats via API
        console.log(`Available: ${availableCount}, Assigned: ${assignedCount}`);
    }
}); 