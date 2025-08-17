<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', __('My Model Trains'))</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <!-- Custom styles -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  
  <!-- Custom lightbox styles -->
  <style>
    /* Image Viewer Styles */
    .product-image, .product-thumbnail {
      transition: all 0.3s ease;
    }
    
    .product-image:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .product-thumbnail:hover {
      opacity: 1 !important;
      transform: scale(1.1);
    }
    
    /* Lightbox Modal Styles */
    #imageModal .modal-dialog {
      margin: 0;
      height: 100vh;
      max-width: 100vw;
    }
    
    #imageModal .modal-content {
      height: 100vh;
      background: rgba(0,0,0,0.9) !important;
    }
    
    #imageModal .modal-header {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1060;
      background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, transparent 100%);
      padding: 1rem 1.5rem;
    }
    
    #imageModal .modal-body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }
    
    #imageModal .modal-footer {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 1060;
      background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, transparent 100%);
      padding: 1rem;
    }
    
    #imageModal img {
      cursor: grab;
      user-select: none;
    }
    
    #imageModal img:active {
      cursor: grabbing;
    }
    
    /* Navigation buttons */
    #imageModal .btn {
      backdrop-filter: blur(10px);
      border: none;
    }
    
    #imageModal .btn:hover {
      opacity: 1 !important;
      transform: scale(1.1);
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
      #imageModal .modal-header {
        padding: 0.5rem 1rem;
      }
      
      #imageModal .modal-footer {
        padding: 0.5rem;
      }
      
      #imageModal .btn-lg {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
      }
      
      .modal-thumbnail {
        width: 40px !important;
        height: 40px !important;
      }
      
      #prevImage, #nextImage {
        margin: 0 !important;
      }
    }
    
    /* Thumbnail highlighting */
    .modal-thumbnail {
      border-radius: 0.375rem;
      transition: all 0.3s ease;
    }
    
    .modal-thumbnail:hover {
      transform: scale(1.1);
    }
    
    /* Loading animation */
    #modalImage {
      transition: opacity 0.3s ease;
    }
    
    /* Zoom indicator */
    .product-image:hover + .zoom-indicator,
    .product-image:focus + .zoom-indicator {
      opacity: 1;
    }

    /* Enhanced Navbar Styles */
    .navbar {
      background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%) !important;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255,255,255,0.1);
      z-index: 1030 !important;
      position: relative;
    }
    
    .navbar-brand {
      font-size: 1.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }
    
    .navbar-brand:hover {
      transform: translateY(-1px);
      text-shadow: 0 4px 8px rgba(0,0,0,0.4);
    }
    
    .navbar-brand .fa-train {
      animation: train-move 3s infinite ease-in-out;
    }
    
    @keyframes train-move {
      0%, 100% { transform: translateX(0); }
      50% { transform: translateX(3px); }
    }
    
    .nav-link {
      position: relative;
      transition: all 0.3s ease;
      border-radius: 0.375rem;
      margin: 0 0.25rem;
      font-weight: 500;
    }
    
    .nav-link:hover {
      background: rgba(255,255,255,0.1);
      transform: translateY(-1px);
    }
    
    .nav-link.active {
      background: rgba(255,193,7,0.2);
      color: #ffc107 !important;
    }
    
    .nav-link i {
      transition: all 0.3s ease;
    }
    
    .nav-link:hover i {
      transform: scale(1.1);
    }
    
    /* Dropdown Enhancements */
    .dropdown-menu {
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(0,0,0,0.1);
      border-radius: 0.75rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      margin-top: 0.5rem;
      padding: 0.5rem 0;
      z-index: 9999 !important;
      position: absolute !important;
    }
    
    .dropdown-item {
      transition: all 0.3s ease;
      border-radius: 0.5rem;
      margin: 0.125rem 0.5rem;
      padding: 0.5rem 1rem;
    }
    
    .dropdown-item:hover {
      background: linear-gradient(135deg, #007bff, #0056b3);
      color: white;
      transform: translateX(5px);
    }
    
    .dropdown-item i {
      width: 20px;
      text-align: center;
    }
    
    .dropdown-divider {
      margin: 0.5rem 1rem;
      border-color: rgba(0,0,0,0.1);
    }
    
    /* Mobile Navbar Improvements */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        background: rgba(0,0,0,0.9);
        margin-top: 1rem;
        border-radius: 0.75rem;
        padding: 1rem;
        backdrop-filter: blur(10px);
      }
      
      .nav-link {
        margin: 0.25rem 0;
        padding: 0.75rem 1rem;
      }
      
      .dropdown-menu {
        background: rgba(0,0,0,0.8);
        border: none;
        margin-left: 1rem;
      }
      
      .dropdown-item {
        color: rgba(255,255,255,0.8);
      }
      
      .dropdown-item:hover {
        color: white;
        background: rgba(255,255,255,0.1);
      }
    }
    
    /* Navbar Toggler Animation */
    .navbar-toggler {
      padding: 0.5rem;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .navbar-toggler:hover {
      background: rgba(255,255,255,0.1);
      transform: scale(1.05);
    }
    
    .navbar-toggler:focus {
      box-shadow: 0 0 0 0.2rem rgba(255,193,7,0.5);
    }
    
    /* Ensure dropdowns work properly */
    .dropdown {
      position: relative;
    }
    
    .dropdown-menu {
      display: none;
    }
    
    .dropdown-menu.show {
      display: block !important;
    }
    
    /* Fix for any container overflow issues */
    .container {
      overflow: visible !important;
    }
    
    .navbar-nav {
      overflow: visible !important;
    }
  </style>
</head>
<body>
  @include('partials.navbar')
  @if (session('success'))
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif
  @if (session('error'))
    <div class="container mt-3">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif
  @yield('content')
  @include('partials.footer')
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Initialize Bootstrap Components -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize all dropdowns
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
      var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
          boundary: 'viewport',
          display: 'dynamic'
        });
      });
      
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      });
      
      // Initialize alerts
      var alertList = document.querySelectorAll('.alert')
      alertList.forEach(function (alert) {
        new bootstrap.Alert(alert)
      });

      @auth
      // Messages notification system
      function updateMessagesNotification() {
        fetch('{{ route("messages.unread-count") }}')
          .then(response => response.json())
          .then(data => {
            const countBadge = document.getElementById('unread-messages-count');
            if (data.count > 0) {
              countBadge.textContent = data.count > 99 ? '99+' : data.count;
              countBadge.style.display = 'block';
            } else {
              countBadge.style.display = 'none';
            }
          });
      }

      function updateLatestMessages() {
        fetch('{{ route("messages.latest-unread") }}')
          .then(response => response.json())
          .then(data => {
            const container = document.getElementById('latest-messages');
            if (data.messages.length > 0) {
              container.innerHTML = data.messages.map(message => `
                <li>
                  <a class="dropdown-item" href="${message.url}">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <div class="fw-bold">${message.sender}</div>
                        <div class="small text-muted">${message.subject}</div>
                        <div class="small">${message.message}</div>
                        ${message.product ? `<div class="small text-info">{{ __('About:') }} ${message.product}</div>` : ''}
                        <div class="small text-muted">${message.created_at}</div>
                      </div>
                    </div>
                  </a>
                </li>
              `).join('');
            } else {
              container.innerHTML = '<li class="dropdown-item-text text-center text-muted py-3">{{ __("No new messages") }}</li>';
            }
          });
      }

      // Update on page load
      updateMessagesNotification();
      updateLatestMessages();

      // Update every 30 seconds
      setInterval(() => {
        updateMessagesNotification();
        updateLatestMessages();
      }, 30000);

      // Update when clicking on messages dropdown
      document.getElementById('messagesDropdown').addEventListener('shown.bs.dropdown', function () {
        updateLatestMessages();
      });
      @endauth
    });
  </script>
  
  <!-- Custom application JavaScript -->
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>