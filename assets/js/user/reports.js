// Reports Page JavaScript
console.log('Reports.js loaded successfully');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing Reports');
    initializeReports();
});

function initializeReports() {
    console.log('Initializing Reports...');
    initializeFormHandling();
    initializeFileUpload();
    initializeReportTypeSelection();
    loadRecentReports();
    animateStats();
    setCurrentDate();
    console.log('Reports initialization complete');
}

function initializeFormHandling() {
    const form = document.getElementById('reportForm');
    if (form) {
        form.addEventListener('submit', handleReportSubmission);
    }
}

function initializeFileUpload() {
    const fileUploadArea = document.querySelector('.file-upload-area');
    const fileInput = document.getElementById('evidenceFile');
    
    if (fileUploadArea && fileInput) {
        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', handleDragOver);
        fileUploadArea.addEventListener('dragleave', handleDragLeave);
        fileUploadArea.addEventListener('drop', handleDrop);
        
        // Click to upload
        fileUploadArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFileSelect);
    }
}

function initializeReportTypeSelection() {
    console.log('Initializing report type selection...');
    const reportTypeOptions = document.querySelectorAll('.report-type-option input[type="radio"]');
    console.log('Found report type options:', reportTypeOptions.length);
    
    reportTypeOptions.forEach(option => {
        option.addEventListener('change', function() {
            console.log('Report type selected:', this.value);
            // Remove active class from all options
            document.querySelectorAll('.report-type-option label').forEach(label => {
                label.style.transform = 'translateY(0)';
            });
            
            // Add active class to selected option
            if (this.checked) {
                this.nextElementSibling.style.transform = 'translateY(-2px)';
            }
            
            // Show/hide dynamic form fields based on selection
            showDynamicFormFields(this.value);
        });
    });
}

function showDynamicFormFields(reportType) {
    console.log('Showing dynamic form fields for:', reportType);
    // Hide all dynamic form fields first
    const allDynamicFields = document.querySelectorAll('.dynamic-fields');
    console.log('Found dynamic fields:', allDynamicFields.length);
    allDynamicFields.forEach(field => {
        field.style.display = 'none';
    });
    
    // Show relevant fields based on report type
    if (reportType === 'fareDispute') {
        const fareDisputeFields = document.getElementById('fareDisputeFields');
        console.log('Fare dispute fields element:', fareDisputeFields);
        if (fareDisputeFields) {
            fareDisputeFields.style.display = 'block';
            console.log('Fare dispute fields shown');
            // Set required attributes for fare dispute fields
            setRequiredFields('fareDispute');
        }
    } else if (reportType === 'roadblock') {
        const roadblockFields = document.getElementById('roadblockFields');
        console.log('Roadblock fields element:', roadblockFields);
        if (roadblockFields) {
            roadblockFields.style.display = 'block';
            console.log('Roadblock fields shown');
            // Set required attributes for roadblock fields
            setRequiredFields('roadblock');
            // Auto-fill current date for roadblock
            const roadblockDate = document.getElementById('roadblockDate');
            if (roadblockDate) {
                roadblockDate.value = new Date().toISOString().split('T')[0];
            }
        }
    }
}

function setRequiredFields(reportType) {
    // Reset all required attributes first
    const allInputs = document.querySelectorAll('.dynamic-fields input, .dynamic-fields select, .dynamic-fields textarea');
    allInputs.forEach(input => {
        input.removeAttribute('required');
    });
    
    if (reportType === 'fareDispute') {
        // Set required fields for fare dispute (essential ones + transport type and plate number)
        const requiredFields = ['fromLocation', 'toLocation', 'transportType', 'vehiclePlate', 'description', 'fullName', 'contactEmail'];
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.setAttribute('required', 'required');
            }
        });
        
        // Make other fields optional but helpful
        const optionalFields = ['incidentDate', 'incidentTime', 'driverName', 'witnessesInfo', 'additionalInfo'];
        optionalFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.removeAttribute('required');
            }
        });
    } else if (reportType === 'roadblock') {
        // Set required fields for roadblock (only essential ones)
        const requiredFields = ['blockedLocation', 'roadblockReason', 'roadblockDescription', 'fullName', 'contactEmail'];
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.setAttribute('required', 'required');
            }
        });
        
        // Make other fields optional but helpful
        const optionalFields = ['roadblockFrom', 'roadblockTo'];
        optionalFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.removeAttribute('required');
            }
        });
    }
}

function setCurrentDate() {
    // Set current date for roadblock date field
    const roadblockDate = document.getElementById('roadblockDate');
    if (roadblockDate) {
        roadblockDate.value = new Date().toISOString().split('T')[0];
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFiles(files);
    }
}

function handleFileSelect(e) {
    const files = e.target.files;
    if (files.length > 0) {
        handleFiles(files);
    }
}

function handleFiles(files) {
    const fileUploadArea = document.querySelector('.file-upload-area');
    const fileInput = document.getElementById('evidenceFile');
    
    // Clear previous file info
    const existingFileInfo = fileUploadArea.querySelector('.file-info');
    if (existingFileInfo) {
        existingFileInfo.remove();
    }
    
    // Create file info display
    const fileInfo = document.createElement('div');
    fileInfo.className = 'file-info';
    
    if (files.length === 1) {
        const file = files[0];
        fileInfo.innerHTML = `
            <i class="fas fa-file"></i>
            <span>${file.name}</span>
            <small>${formatFileSize(file.size)}</small>
        `;
    } else {
        fileInfo.innerHTML = `
            <i class="fas fa-folder"></i>
            <span>${files.length} files selected</span>
        `;
    }
    
    fileUploadArea.appendChild(fileInfo);
    
    // Update file input
    const dataTransfer = new DataTransfer();
    Array.from(files).forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function handleReportSubmission(e) {
    e.preventDefault();
    
    // Validate form before submission
    if (!validateForm()) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }
    
    const form = e.target;
    const submitBtn = form.querySelector('.submit-btn');
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    // Simulate form submission
    setTimeout(() => {
        showSuccessModal();
        form.reset();
        
        // Reset file upload area
        const fileUploadArea = document.querySelector('.file-upload-area');
        const existingFileInfo = fileUploadArea.querySelector('.file-info');
        if (existingFileInfo) {
            existingFileInfo.remove();
        }
        
        // Hide dynamic form fields
        const allDynamicFields = document.querySelectorAll('.dynamic-fields');
        allDynamicFields.forEach(field => {
            field.style.display = 'none';
        });
        
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Report';
        
        // Add new report to recent reports
        addNewReport();
    }, 2000);
}

function validateForm() {
    const reportType = document.querySelector('input[name="reportType"]:checked');
    if (!reportType) {
        showNotification('Please select a report type.', 'error');
        return false;
    }
    
    // Check required fields based on report type
    if (reportType.value === 'fareDispute') {
        const requiredFields = ['fromLocation', 'toLocation', 'transportType', 'vehiclePlate', 'description', 'fullName', 'contactEmail'];
        return validateRequiredFields(requiredFields);
    } else if (reportType.value === 'roadblock') {
        const requiredFields = ['blockedLocation', 'roadblockReason', 'roadblockDescription', 'fullName', 'contactEmail'];
        return validateRequiredFields(requiredFields);
    }
    
    return false;
}

function validateRequiredFields(fieldNames) {
    for (const fieldName of fieldNames) {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            showNotification(`Please fill in ${field.labels[0].textContent.replace('*', '').trim()}.`, 'error');
            field.style.borderColor = '#e53e3e';
            return false;
        } else {
            field.style.borderColor = '#e2e8f0';
        }
    }
    
    // Check checkboxes
    const termsAgree = document.getElementById('termsAgree');
    const confirmAccuracy = document.getElementById('confirmAccuracy');
    
    if (!termsAgree.checked || !confirmAccuracy.checked) {
        showNotification('Please agree to the terms and confirm accuracy.', 'error');
        return false;
    }
    
    return true;
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#fed7d7' : '#c6f6d5'};
        color: ${type === 'error' ? '#c53030' : '#2f855a'};
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1001;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add keyframe animation
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function showSuccessModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.classList.add('show');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            modal.classList.remove('show');
        }, 5000);
    }
}

function closeModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function addNewReport() {
    const recentReportsContainer = document.querySelector('.recent-reports .reports-list');
    if (!recentReportsContainer) return;
    
    const reportType = document.querySelector('input[name="reportType"]:checked');
    if (!reportType) return;
    
    let reportData = {};
    if (reportType.value === 'fareDispute') {
        reportData = {
            type: 'Fare Dispute',
            description: `Fare dispute from ${document.getElementById('fromLocation')?.value || 'N/A'} to ${document.getElementById('toLocation')?.value || 'N/A'}`,
            date: new Date().toLocaleDateString(),
            status: 'pending'
        };
    } else if (reportType.value === 'roadblock') {
        reportData = {
            type: 'Roadblock',
            description: `Roadblock at ${document.getElementById('blockedLocation')?.value || 'N/A'}`,
            date: new Date().toLocaleDateString(),
            status: 'pending'
        };
    }
    
    const newReport = createReportElement(reportData);
    
    // Add to beginning of list
    recentReportsContainer.insertBefore(newReport, recentReportsContainer.firstChild);
    
    // Add animation
    newReport.style.opacity = '0';
    newReport.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
        newReport.style.transition = 'all 0.3s ease';
        newReport.style.opacity = '1';
        newReport.style.transform = 'translateX(0)';
    }, 100);
}

function createReportElement(report) {
    const reportItem = document.createElement('div');
    reportItem.className = 'report-item';
    
    reportItem.innerHTML = `
        <div class="report-header">
            <span class="report-type">${report.type}</span>
            <span class="report-date">${report.date}</span>
        </div>
        <p class="report-description">${report.description}</p>
        <span class="report-status ${report.status}">${report.status.charAt(0).toUpperCase() + report.status.slice(1)}</span>
    `;
    
    return reportItem;
}

function loadRecentReports() {
    const recentReportsContainer = document.querySelector('.recent-reports .reports-list');
    if (!recentReportsContainer) return;
    
    // Sample data - in real app, this would come from API
    const sampleReports = [
        {
            type: 'Roadblock',
            description: 'Construction work blocking main road to city center',
            date: '2024-01-15',
            status: 'resolved'
        },
        {
            type: 'Fare Dispute',
            description: 'Driver charged extra fare for regular route',
            date: '2024-01-13',
            status: 'pending'
        }
    ];
    
    sampleReports.forEach(report => {
        const reportElement = createReportElement(report);
        recentReportsContainer.appendChild(reportElement);
    });
}

function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(currentValue);
        }, 30);
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('successModal');
    if (modal && e.target === modal) {
        closeModal();
    }
});

// Keyboard navigation for modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
