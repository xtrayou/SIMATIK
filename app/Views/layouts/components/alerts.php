<?php
// Flash Messages Component with Enhanced Styling and Auto-dismiss
?>

<!-- Success Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" data-auto-dismiss="5000">
        <div class="d-flex align-items-center">
            <div class="alert-icon me-3">
                <i class="bi bi-check-circle-fill fs-4"></i>
            </div>
            <div class="alert-content flex-grow-1">
                <h6 class="alert-heading mb-1">Berhasil!</h6>
                <p class="mb-0"><?= session()->getFlashdata('success') ?></p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-progress"></div>
    </div>
<?php endif ?>

<!-- Error Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" data-auto-dismiss="8000">
        <div class="d-flex align-items-center">
            <div class="alert-icon me-3">
                <i class="bi bi-exclamation-circle-fill fs-4"></i>
            </div>
            <div class="alert-content flex-grow-1">
                <h6 class="alert-heading mb-1">Error!</h6>
                <p class="mb-0"><?= session()->getFlashdata('error') ?></p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-progress"></div>
    </div>
<?php endif ?>

<!-- Warning Messages -->
<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert" data-auto-dismiss="6000">
        <div class="d-flex align-items-center">
            <div class="alert-icon me-3">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            </div>
            <div class="alert-content flex-grow-1">
                <h6 class="alert-heading mb-1">Peringatan!</h6>
                <p class="mb-0"><?= session()->getFlashdata('warning') ?></p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-progress"></div>
    </div>
<?php endif ?>

<!-- Info Messages -->
<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert" data-auto-dismiss="5000">
        <div class="d-flex align-items-center">
            <div class="alert-icon me-3">
                <i class="bi bi-info-circle-fill fs-4"></i>
            </div>
            <div class="alert-content flex-grow-1">
                <h6 class="alert-heading mb-1">Informasi</h6>
                <p class="mb-0"><?= session()->getFlashdata('info') ?></p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-progress"></div>
    </div>
<?php endif ?>

<!-- Validation Errors (Multiple) -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" data-auto-dismiss="10000">
        <div class="d-flex align-items-start">
            <div class="alert-icon me-3 mt-1">
                <i class="bi bi-x-circle-fill fs-4"></i>
            </div>
            <div class="alert-content flex-grow-1">
                <h6 class="alert-heading mb-2">Terdapat kesalahan pada form:</h6>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-progress"></div>
    </div>
<?php endif ?>

<!-- Custom Messages (JSON Format) -->
<?php if (session()->getFlashdata('message')): ?>
    <?php $message = json_decode(session()->getFlashdata('message'), true); ?>
    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?? 'info' ?> alert-dismissible fade show shadow-sm" 
             role="alert" data-auto-dismiss="<?= $message['duration'] ?? 5000 ?>">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <?php
                    $icons = [
                        'success' => 'bi-check-circle-fill',
                        'error' => 'bi-exclamation-circle-fill',
                        'warning' => 'bi-exclamation-triangle-fill',
                        'info' => 'bi-info-circle-fill',
                        'danger' => 'bi-x-circle-fill'
                    ];
                    $icon = $icons[$message['type'] ?? 'info'] ?? 'bi-info-circle-fill';
                    ?>
                    <i class="bi <?= $icon ?> fs-4"></i>
                </div>
                <div class="alert-content flex-grow-1">
                    <?php if (!empty($message['title'])): ?>
                        <h6 class="alert-heading mb-1"><?= $message['title'] ?></h6>
                    <?php endif ?>
                    <p class="mb-0"><?= $message['text'] ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="alert-progress"></div>
        </div>
    <?php endif ?>
<?php endif ?>

<style>
/* Enhanced Alert Styles */
.alert {
    border: none;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert-success {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
    color: #0f5132;
    border-left: 4px solid #198754;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    color: #664d03;
    border-left: 4px solid #ffc107;
}

.alert-info {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
    color: #055160;
    border-left: 4px solid #0dcaf0;
}

.alert-icon {
    flex-shrink: 0;
    opacity: 0.8;
}

.alert-content {
    line-height: 1.4;
}

.alert-heading {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.alert p {
    font-size: 0.9rem;
    line-height: 1.5;
}

.alert ul {
    font-size: 0.9rem;
}

.alert ul li {
    margin-bottom: 0.25rem;
}

/* Progress Bar for Auto Dismiss */
.alert-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background-color: currentColor;
    opacity: 0.3;
    width: 0%;
    transition: width linear;
}

.alert[data-auto-dismiss] .alert-progress {
    animation: alertProgress linear;
}

@keyframes alertProgress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Hover effects */
.alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    transition: all 0.3s ease;
}

.alert:hover .alert-progress {
    animation-play-state: paused;
}

/* Close button enhancement */
.alert .btn-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.alert .btn-close:hover {
    opacity: 1;
}

/* Dark mode support */
[data-bs-theme="dark"] .alert-success {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.2) 0%, rgba(25, 135, 84, 0.1) 100%);
    color: #75b798;
}

[data-bs-theme="dark"] .alert-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(220, 53, 69, 0.1) 100%);
    color: #ea868f;
}

[data-bs-theme="dark"] .alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
    color: #ffda6a;
}

[data-bs-theme="dark"] .alert-info {
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.2) 0%, rgba(13, 202, 240, 0.1) 100%);
    color: #6edff6;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .alert {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
        border-radius: 8px;
    }
    
    .alert-heading {
        font-size: 0.9rem;
    }
    
    .alert p, .alert ul {
        font-size: 0.85rem;
    }
    
    .alert-icon {
        margin-right: 0.75rem !important;
    }
    
    .alert-icon i {
        font-size: 1.1rem !important;
    }
}

/* Stack multiple alerts nicely */
.alert + .alert {
    margin-top: -0.5rem;
}

/* Special styling for validation errors */
.alert ul {
    padding-left: 1.25rem;
}

.alert ul li::marker {
    color: currentColor;
}

/* Focus states for accessibility */
.alert:focus-within {
    outline: 2px solid rgba(13, 110, 253, 0.25);
    outline-offset: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss functionality
    const alerts = document.querySelectorAll('[data-auto-dismiss]');
    
    alerts.forEach(function(alert) {
        const duration = parseInt(alert.dataset.autoDismiss);
        const progressBar = alert.querySelector('.alert-progress');
        
        if (progressBar) {
            progressBar.style.animationDuration = duration + 'ms';
        }
        
        // Pause auto-dismiss on hover
        let timeoutId = setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, duration);
        
        alert.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            if (progressBar) {
                progressBar.style.animationPlayState = 'paused';
            }
        });
        
        alert.addEventListener('mouseleave', function() {
            const remainingTime = duration * (1 - (Date.now() - alert.dataset.startTime) / duration);
            timeoutId = setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, remainingTime);
            
            if (progressBar) {
                progressBar.style.animationPlayState = 'running';
            }
        });
        
        // Store start time for calculating remaining time
        alert.dataset.startTime = Date.now();
    });
    
    // Add sound effects (optional)
    const playAlertSound = function(type) {
        // You can add audio files here
        const audio = new Audio();
        switch(type) {
            case 'success':
                // audio.src = '/assets/sounds/success.mp3';
                break;
            case 'error':
                // audio.src = '/assets/sounds/error.mp3';
                break;
        }
        // audio.play().catch(e => console.log('Audio play failed:', e));
    };
    
    // Trigger sound for new alerts
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-success')) {
            playAlertSound('success');
        } else if (alert.classList.contains('alert-danger')) {
            playAlertSound('error');
        }
    });
    
    // Global function to create dynamic alerts
    window.showAlert = function(message, type = 'info', title = null, duration = 5000) {
        const alertContainer = document.querySelector('.container-fluid') || document.body;
        
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show shadow-sm" 
                 role="alert" data-auto-dismiss="${duration}" id="${alertId}">
                <div class="d-flex align-items-center">
                    <div class="alert-icon me-3">
                        <i class="bi ${getAlertIcon(type)} fs-4"></i>
                    </div>
                    <div class="alert-content flex-grow-1">
                        ${title ? `<h6 class="alert-heading mb-1">${title}</h6>` : ''}
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <div class="alert-progress"></div>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Re-initialize auto-dismiss for new alert
        const newAlert = document.getElementById(alertId);
        initializeAlert(newAlert);
    };
    
    function getAlertIcon(type) {
        const icons = {
            'success': 'bi-check-circle-fill',
            'error': 'bi-exclamation-circle-fill',
            'danger': 'bi-x-circle-fill',
            'warning': 'bi-exclamation-triangle-fill',
            'info': 'bi-info-circle-fill'
        };
        return icons[type] || 'bi-info-circle-fill';
    }
    
    function initializeAlert(alert) {
        const duration = parseInt(alert.dataset.autoDismiss);
        const progressBar = alert.querySelector('.alert-progress');
        
        if (progressBar) {
            progressBar.style.animationDuration = duration + 'ms';
        }
        
        let timeoutId = setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, duration);
        
        alert.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            if (progressBar) {
                progressBar.style.animationPlayState = 'paused';
            }
        });
        
        alert.addEventListener('mouseleave', function() {
            const remainingTime = duration * (1 - (Date.now() - alert.dataset.startTime) / duration);
            timeoutId = setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, remainingTime);
            
            if (progressBar) {
                progressBar.style.animationPlayState = 'running';
            }
        });
        
        alert.dataset.startTime = Date.now();
    }
});
</script>