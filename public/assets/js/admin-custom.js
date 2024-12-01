// Admin Panel Custom JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Global değişkenler
    const sidenavToggle = document.getElementById('iconNavbarSidenav');
    const sidenav = document.querySelector('.sidenav');
    const darkModeToggle = document.getElementById('darkModeToggle');
    const globalSearch = document.getElementById('globalSearch');
    const fileUploads = document.querySelectorAll('.custom-file-upload');
    const charts = document.querySelectorAll('.chart-canvas');
    
    // Sidenav Toggle
    if (sidenavToggle) {
        sidenavToggle.addEventListener('click', function() {
            sidenav.classList.toggle('show');
        });
    }

    // Dark Mode
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-version');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-version'));
            updateChartColors();
        });

        // Sayfa yüklendiğinde dark mode kontrolü
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-version');
            updateChartColors();
        }
    }

    // Global Arama
    if (globalSearch) {
        let searchTimeout;
        globalSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performGlobalSearch(e.target.value);
            }, 500);
        });
    }

    // Dosya Yükleme
    fileUploads.forEach(upload => {
        initializeFileUpload(upload);
    });

    // Chart Renk Güncellemesi
    function updateChartColors() {
        charts.forEach(canvas => {
            const chart = Chart.getChart(canvas);
            if (chart) {
                const isDark = document.body.classList.contains('dark-version');
                updateChartTheme(chart, isDark);
            }
        });
    }

    // Global Fonksiyonlar
    window.AdminPanel = {
        showLoader: showLoader,
        hideLoader: hideLoader,
        showNotification: showNotification,
        initializeChart: initializeChart,
        initializeDataTable: initializeDataTable
    };
});

// Global Arama
function performGlobalSearch(query) {
    if (query.length < 2) return;

    fetch(`/admin/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            // Arama sonuçlarını göster
            showSearchResults(data);
        })
        .catch(error => {
            console.error('Arama hatası:', error);
        });
}

function showSearchResults(results) {
    // Arama sonuçları modalını göster
    const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
    const modalBody = document.querySelector('#searchResultsModal .modal-body');
    
    let html = '<div class="list-group">';
    results.forEach(result => {
        html += `
            <a href="${result.url}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${result.title}</h6>
                    <small>${result.type}</small>
                </div>
                <p class="mb-1">${result.description}</p>
            </a>
        `;
    });
    html += '</div>';

    modalBody.innerHTML = html;
    modal.show();
}

// Dosya Yükleme
function initializeFileUpload(element) {
    const input = element.querySelector('input[type="file"]');
    const preview = element.querySelector('.file-preview');

    element.addEventListener('dragover', (e) => {
        e.preventDefault();
        element.classList.add('dragging');
    });

    element.addEventListener('dragleave', () => {
        element.classList.remove('dragging');
    });

    element.addEventListener('drop', (e) => {
        e.preventDefault();
        element.classList.remove('dragging');
        
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });

    if (input) {
        input.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
    }

    function handleFiles(files) {
        if (!preview) return;

        preview.innerHTML = '';
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML += `
                        <div class="preview-item">
                            <img src="${e.target.result}" alt="${file.name}">
                            <span>${file.name}</span>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML += `
                    <div class="preview-item">
                        <i class="material-icons">description</i>
                        <span>${file.name}</span>
                    </div>
                `;
            }
        });
    }
}

// Bildirimler
function showNotification(message, type = 'success') {
    const container = document.createElement('div');
    container.className = `alert alert-${type} alert-dismissible fade show`;
    container.innerHTML = `
        <span class="alert-icon"><i class="material-icons">notification_important</i></span>
        <span class="alert-text">${message}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;

    document.querySelector('.container-fluid').insertBefore(container, document.querySelector('.container-fluid').firstChild);

    setTimeout(() => {
        container.remove();
    }, 5000);
}

// Loading Spinner
function showLoader() {
    const loader = document.createElement('div');
    loader.className = 'loading-overlay';
    loader.innerHTML = '<div class="loading-spinner"></div>';
    document.body.appendChild(loader);
}

function hideLoader() {
    const loader = document.querySelector('.loading-overlay');
    if (loader) {
        loader.remove();
    }
}

// Chart İnitialize
function initializeChart(canvas, config) {
    const isDark = document.body.classList.contains('dark-version');
    const ctx = canvas.getContext('2d');
    
    // Dark mode renk ayarlamaları
    if (isDark) {
        config.options = config.options || {};
        config.options.scales = config.options.scales || {};
        config.options.scales.y = config.options.scales.y || {};
        config.options.scales.x = config.options.scales.x || {};
        
        config.options.scales.y.grid = {
            color: 'rgba(255, 255, 255, 0.1)'
        };
        config.options.scales.x.grid = {
            color: 'rgba(255, 255, 255, 0.1)'
        };
        
        if (config.options.plugins && config.options.plugins.legend) {
            config.options.plugins.legend.labels = {
                color: '#fff'
            };
        }
    }

    return new Chart(ctx, config);
}

// DataTable İnitialize
function initializeDataTable(tableId, config = {}) {
    const defaultConfig = {
        language: {
            url: '/assets/js/datatable-tr.json'
        },
        pageLength: 10,
        responsive: true,
        dom: '<"top"<"left-col"B><"center-col"l><"right-col"f>>rtip',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-info'
            }
        ],
        initComplete: function() {
            const api = this.api();
            api.buttons().container().appendTo($('.left-col'));
        }
    };

    return new DataTable(`#${tableId}`, { ...defaultConfig, ...config });
}

// Chart Tema Güncelleme
function updateChartTheme(chart, isDark) {
    const colors = isDark ? {
        gridColor: 'rgba(255, 255, 255, 0.1)',
        textColor: '#fff'
    } : {
        gridColor: 'rgba(0, 0, 0, 0.1)',
        textColor: '#666'
    };

    chart.options.scales.y.grid.color = colors.gridColor;
    chart.options.scales.x.grid.color = colors.gridColor;

    if (chart.options.plugins && chart.options.plugins.legend) {
        chart.options.plugins.legend.labels.color = colors.textColor;
    }

    chart.update();
}

// Form Validation
function initializeFormValidation(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            highlightInvalidFields(form);
        }
        form.classList.add('was-validated');
    });

    // Gerçek zamanlı validasyon
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });
}

function validateField(field) {
    const validationMessage = field.nextElementSibling;
    if (!validationMessage || !validationMessage.classList.contains('validation-message')) return;

    if (field.checkValidity()) {
        validationMessage.textContent = 'Geçerli';
        validationMessage.classList.remove('invalid');
        validationMessage.classList.add('valid');
    } else {
        validationMessage.textContent = field.validationMessage;
        validationMessage.classList.remove('valid');
        validationMessage.classList.add('invalid');
    }
}

function highlightInvalidFields(form) {
    const invalidInputs = form.querySelectorAll(':invalid');
    invalidInputs.forEach(input => {
        input.parentElement.classList.add('has-error');
        // Input'un üstüne gelince hata mesajını göster
        input.title = input.validationMessage;
    });
}

// AJAX Form Submit
function initializeAjaxForm(formId, options = {}) {
    const form = document.getElementById(formId);
    if (!form) return;

    const defaultOptions = {
        beforeSubmit: () => true,
        success: (response) => console.log(response),
        error: (error) => console.error(error),
        complete: () => {}
    };

    const settings = { ...defaultOptions, ...options };

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (settings.beforeSubmit() === false) return;

        const formData = new FormData(form);
        showLoader();

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(response => {
            settings.success(response);
            if (response.message) {
                showNotification(response.message, 'success');
            }
        })
        .catch(error => {
            settings.error(error);
            showNotification('Bir hata oluştu', 'error');
        })
        .finally(() => {
            hideLoader();
            settings.complete();
        });
    });
}

// Dinamik Form Alanları
function initializeDynamicFields() {
    const container = document.querySelector('.dynamic-fields-container');
    if (!container) return;

    const addButton = container.querySelector('.add-field');
    if (addButton) {
        addButton.addEventListener('click', () => addDynamicField(container));
    }

    // Mevcut alanları initialize et
    container.querySelectorAll('.dynamic-field').forEach(field => {
        initializeDynamicField(field);
    });
}

function addDynamicField(container) {
    const template = container.querySelector('.field-template');
    if (!template) return;

    const newField = template.content.cloneNode(true);
    const fieldContainer = newField.querySelector('.dynamic-field');
    
    initializeDynamicField(fieldContainer);
    container.insertBefore(fieldContainer, container.querySelector('.add-field'));
}

function initializeDynamicField(field) {
    const removeButton = field.querySelector('.remove-field');
    if (removeButton) {
        removeButton.addEventListener('click', () => {
            field.remove();
        });
    }
}

// Debounce Fonksiyonu
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Sayfa yönlendirmeleri için loader
window.addEventListener('beforeunload', () => {
    showLoader();
});

// Hata işleme
window.addEventListener('error', (event) => {
    console.error('JavaScript Error:', event.error);
    showNotification('Bir hata oluştu', 'error');
});

// Responsive tablo scroll
const tables = document.querySelectorAll('.table-responsive');
tables.forEach(table => {
    let isDown = false;
    let startX;
    let scrollLeft;

    table.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - table.offsetLeft;
        scrollLeft = table.scrollLeft;
    });

    table.addEventListener('mouseleave', () => {
        isDown = false;
    });

    table.addEventListener('mouseup', () => {
        isDown = false;
    });

    table.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - table.offsetLeft;
        const walk = (x - startX) * 2;
        table.scrollLeft = scrollLeft - walk;
    });
});