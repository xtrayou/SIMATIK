<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p class="mb-0">
                <?= date('Y') ?> &copy; 
                <strong>Inventory Management System</strong>
            </p>
            <small>Version 1.0.0</small>
        </div>
        <div class="float-end">
            <div class="d-flex align-items-center gap-3">
                <!-- System Status -->
                <div class="d-flex align-items-center">
                    <div class="status-indicator bg-success me-2"></div>
                    <small>System Online</small>
                </div>
                
                <!-- Build Info -->
                <small class="d-none d-md-inline">
                    Built with 
                    <span class="text-danger">
                        <i class="bi bi-heart-fill"></i>
                    </span>
                    using 
                    <a href="https://codeigniter.com/" target="_blank" class="text-decoration-none">
                        CodeIgniter 4
                    </a> 
                    & 
                    <a href="https://zuramai.github.io/mazer/" target="_blank" class="text-decoration-none">
                        Mazer
                    </a>
                </small>
                
                <!-- Quick Links -->
                <div class="dropdown d-none d-lg-inline">
                    <a class="text-muted text-decoration-none" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('/help') ?>">
                                <i class="bi bi-question-circle me-2"></i> Bantuan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('/about') ?>">
                                <i class="bi bi-info-circle me-2"></i> Tentang
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('/system-info') ?>">
                                <i class="bi bi-cpu me-2"></i> System Info
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Custom Styles */
footer {
    margin-top: 2rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.1);
    background-color: rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
}

.footer p {
    font-size: 0.9rem;
}

.footer small {
    font-size: 0.8rem;
}

.footer a {
    color: #435ebe;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #364296;
    text-decoration: underline !important;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse-status 2s infinite;
}

@keyframes pulse-status {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Dark mode support */
[data-bs-theme="dark"] footer {
    background-color: rgba(0,0,0,0.3);
    border-top-color: rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .footer a {
    color: #6196ff;
}

[data-bs-theme="dark"] .footer a:hover {
    color: #7ba7ff;
}

/* Responsive */
@media (max-width: 768px) {
    footer .float-end {
        float: none !important;
        margin-top: 0.5rem;
    }
    
    footer .d-flex {
        justify-content: center;
    }
}
</style>

<script>
// Real-time system status (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    // Check system status every 30 seconds
    setInterval(function() {
        // You can implement actual system health check here
        const statusIndicator = document.querySelector('.status-indicator');
        
        // Example: ping server or check API health
        fetch('<?= base_url('/api/health') ?>', { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    statusIndicator.className = 'status-indicator bg-success me-2';
                    statusIndicator.nextElementSibling.textContent = 'System Online';
                } else {
                    statusIndicator.className = 'status-indicator bg-warning me-2';
                    statusIndicator.nextElementSibling.textContent = 'System Issues';
                }
            })
            .catch(() => {
                statusIndicator.className = 'status-indicator bg-danger me-2';
                statusIndicator.nextElementSibling.textContent = 'System Offline';
            });
    }, 30000);
});
</script>