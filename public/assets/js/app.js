/**
 * My Train Shop - Custom JavaScript
 */

// Utility functions
window.MyTrainShop = {
    // Initialize all functionality when DOM is ready
    init: function() {
        this.initNavbar();
        this.initAlerts();
        this.initTooltips();
        this.initImageViewer();
    },

    // Initialize navbar functionality
    initNavbar: function() {
        const navbar = document.querySelector('.navbar');
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        // Auto-close mobile navbar when clicking nav links
        if (navbar) {
            navbar.addEventListener('click', function(e) {
                if (e.target.classList.contains('nav-link') && 
                    window.innerWidth < 992 && 
                    navbarCollapse && 
                    navbarCollapse.classList.contains('show')) {
                    
                    if (navbarToggler) {
                        navbarToggler.click();
                    }
                }
            });
        }
    },

    // Auto-hide alerts after 5 seconds
    initAlerts: function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-persistent)');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    },

    // Initialize Bootstrap tooltips
    initTooltips: function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    // Utility function for AJAX requests
    ajax: function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        };

        return fetch(url, { ...defaults, ...options });
    },

    // Show confirmation dialog
    confirm: function(message, callback) {
        if (confirm(message)) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    },

    // Show success message
    showSuccess: function(message) {
        this.showAlert(message, 'success');
    },

    // Show error message
    showError: function(message) {
        this.showAlert(message, 'danger');
    },

    // Show alert message
    showAlert: function(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Find a container to append the alert to
        const container = document.querySelector('.container') || document.body;
        const alertDiv = document.createElement('div');
        alertDiv.innerHTML = alertHtml;
        
        container.insertBefore(alertDiv.firstElementChild, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    },

    // Initialize image viewer/lightbox functionality
    initImageViewer: function() {
        // Check if we have product images
        if (typeof window.productImages === 'undefined' || !window.productImages.length) {
            return;
        }

        let currentImageIndex = 0;
        const images = window.productImages;
        const modal = document.querySelector('#imageModal');
        const modalImage = document.querySelector('#modalImage');
        const imageCounter = document.querySelector('#imageCounter');
        const prevBtn = document.querySelector('#prevImage');
        const nextBtn = document.querySelector('#nextImage');

        // Function to update modal image
        const updateModalImage = (index) => {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;
            
            currentImageIndex = index;
            modalImage.src = images[index];
            
            if (imageCounter) {
                imageCounter.textContent = `${index + 1} / ${images.length}`;
            }

            // Update thumbnail highlighting
            const thumbnails = document.querySelectorAll('.modal-thumbnail');
            thumbnails.forEach((thumb, i) => {
                thumb.style.opacity = i === index ? '1' : '0.6';
                thumb.style.border = i === index ? '2px solid #007bff' : 'none';
            });

            // Update main carousel if it exists
            const carousel = document.querySelector('#productCarousel');
            if (carousel) {
                const carouselInstance = bootstrap.Carousel.getInstance(carousel);
                if (carouselInstance) {
                    carouselInstance.to(index);
                }
            }
        };

        // Function to show modal
        const showModal = (startIndex = 0) => {
            updateModalImage(startIndex);
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        };

        // Add click listeners to main product images
        const productImages = document.querySelectorAll('.product-image');
        productImages.forEach((img, index) => {
            img.addEventListener('click', () => {
                showModal(index);
            });
        });

        // Add click listeners to thumbnails (both main and modal)
        const allThumbnails = document.querySelectorAll('.product-thumbnail, .modal-thumbnail');
        allThumbnails.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                const index = parseInt(thumb.getAttribute('data-index'));
                if (modal.classList.contains('show')) {
                    updateModalImage(index);
                } else {
                    showModal(index);
                }
            });
        });

        // Navigation button listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                updateModalImage(currentImageIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                updateModalImage(currentImageIndex + 1);
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('show')) return;

            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    updateModalImage(currentImageIndex - 1);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    updateModalImage(currentImageIndex + 1);
                    break;
                case 'Escape':
                    e.preventDefault();
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    break;
            }
        });

        // Update carousel when modal is hidden
        modal.addEventListener('hidden.bs.modal', () => {
            const carousel = document.querySelector('#productCarousel');
            if (carousel) {
                const carouselInstance = bootstrap.Carousel.getInstance(carousel);
                if (carouselInstance) {
                    carouselInstance.to(currentImageIndex);
                }
            }
        });

        // Sync carousel with modal
        const carousel = document.querySelector('#productCarousel');
        if (carousel) {
            carousel.addEventListener('slide.bs.carousel', (e) => {
                if (!modal.classList.contains('show')) {
                    currentImageIndex = e.to;
                }
            });
        }

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchStartY = 0;
        
        modalImage.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        
        modalImage.addEventListener('touchend', (e) => {
            if (!touchStartX || !touchStartY) return;
            
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchStartX - touchEndX;
            const deltaY = touchStartY - touchEndY;
            
            // Check if it's a horizontal swipe (not vertical scroll)
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0) {
                    // Swipe left - next image
                    updateModalImage(currentImageIndex + 1);
                } else {
                    // Swipe right - previous image
                    updateModalImage(currentImageIndex - 1);
                }
            }
            
            // Reset touch coordinates
            touchStartX = 0;
            touchStartY = 0;
        }, { passive: true });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    MyTrainShop.init();
});

// Global delete image function for admin panels
function deleteImage(imageId) {
    MyTrainShop.confirm('Are you sure you want to delete this image?', function() {
        MyTrainShop.ajax(`/admin/images/${imageId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                MyTrainShop.showSuccess('Image deleted successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                MyTrainShop.showError('Error deleting image: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            MyTrainShop.showError('Error deleting image. Please try again.');
        });
    });
}
