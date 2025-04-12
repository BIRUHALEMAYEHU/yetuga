$(document).ready(function() {
    // Sample traffic updates data
    const updates = [
        {
            id: 1,
            location: '4 Kilo to Piassa Road',
            severity: 'high',
            description: 'Prime Minister Biruh having dinner at Sheraton Addis. Please use alternative routes. Expected heavy security presence.',
            timestamp: '2024-03-20 18:30:00',
            expectedDuration: '3',
            updatedBy: 'captain biruh'
        },
        {
            id: 2,
            location: 'Millennium Hall Area',
            severity: 'high',
            description: 'Singer Biruh having a major concert tonight. Heavy traffic expected. Consider using Mexico or Bole routes.',
            timestamp: '2024-03-20 17:15:00',
            expectedDuration: '5',
            updatedBy: 'Traffic Controller chebule'
        },
        {
            id: 3,
            location: 'Bole Road near Medhanealem',
            severity: 'medium',
            description: 'Major traffic congestion due to road maintenance work. All vehicles advised to use alternative routes.',
            timestamp: '2024-03-20 10:30:00',
            expectedDuration: '3',
            updatedBy: 'Dr. Biruh Alemayehu'
        },
        {
            id: 4,
            location: 'Megenagna Roundabout',
            severity: 'medium',
            description: 'Moderate traffic flow due to peak hours. Expect 15-20 minutes delay.',
            timestamp: '2024-03-20 09:15:00',
            expectedDuration: '2',
            updatedBy: 'Professor Biruh Alemayehu'
        },
        {
            id: 5,
            location: 'tor Hailoch',
            severity: 'low',
            description: 'General biruh is bombing the city , run for your life',
            timestamp: '2024-03-20 08:45:00',
            expectedDuration: '1',
            updatedBy: 'General Biruh Alemayehu'
        }
    ];

    // Function to display traffic updates
    function displayUpdates() {
        const container = $('#trafficUpdates');
        container.empty();

        updates.forEach(update => {
            container.append(`
                <div class="col-md-6 mb-4">
                    <div class="card traffic-card severity-${update.severity}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title">${update.location}</h5>
                                <span class="badge bg-${update.severity === 'high' ? 'danger' : 
                                                        update.severity === 'medium' ? 'warning' : 'success'}">
                                    ${update.severity.toUpperCase()}
                                </span>
                            </div>
                            <p class="card-text">${update.description}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Expected duration: ${update.expectedDuration} hours<br>
                                    Updated by: ${update.updatedBy}
                                </small>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-update" data-id="${update.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-update" data-id="${update.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    // Initial display
    displayUpdates();

    // Handle new update submission
    $('#saveUpdate').click(function() {
        const newUpdate = {
            id: updates.length + 1,
            location: $('#location').val(),
            severity: $('#severity').val(),
            description: $('#description').val(),
            expectedDuration: $('#expectedDuration').val(),
            timestamp: new Date().toISOString().slice(0, 19).replace('T', ' '),
            updatedBy: 'Dr. Biruh Alemayehu'
        };

        updates.unshift(newUpdate);
        displayUpdates();
        $('#addUpdateModal').modal('hide');
        $('#addUpdateForm')[0].reset();
    });
}); 