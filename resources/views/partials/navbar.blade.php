<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
      <i class="fas fa-train me-2 text-warning"></i>
      My Train Shop
    </a>

    <!-- Mobile toggle button -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation items -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <!-- Home Link -->
        <li class="nav-item">
          <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
            <i class="fas fa-home me-1"></i>
            Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
            <i class="fas fa-shopping-bag me-1"></i>
            Products
          </a>
        </li>

        @auth
          <!-- Admin Links -->
          @if (Auth::user()->hasPermission('admin.access'))
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog me-1"></i>
                Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                @if (Auth::user()->hasPermission('users.view'))
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                      <i class="fas fa-users me-2"></i>
                      Manage Users
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                @endif
                
                @if (Auth::user()->hasPermission('products.view'))
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.products.index') }}">
                      <i class="fas fa-box me-2"></i>
                      Manage Products
                    </a>
                  </li>
                @endif
                
                @if (Auth::user()->hasPermission('products.create'))
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.products.create') }}">
                      <i class="fas fa-plus me-2"></i>
                      Add Product
                    </a>
                  </li>
                @endif
                
                @if (Auth::user()->hasPermission('categories.view'))
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                      <i class="fas fa-tags me-2"></i>
                      Manage Categories
                    </a>
                  </li>
                @endif
                
                @if (Auth::user()->hasPermission('orders.view'))
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                      <i class="fas fa-clipboard-list me-2"></i>
                      Manage Orders
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @endif

          <!-- User Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              @if(Auth::user()->image_url)
                <img src="{{ Auth::user()->image_url }}" 
                     alt="Profile" 
                     class="rounded-circle me-2" 
                     style="width: 32px; height: 32px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
              @else
                <div class="bg-light text-dark rounded-circle d-flex align-items-center justify-content-center me-2" 
                     style="width: 32px; height: 32px; font-size: 0.875rem; font-weight: bold; border: 2px solid rgba(255,255,255,0.3);">
                  {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
              @endif
              <span>{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li>
                <div class="dropdown-header d-flex align-items-center p-3">
                  @if(Auth::user()->image_url)
                    <img src="{{ Auth::user()->image_url }}" 
                         alt="Profile" 
                         class="rounded-circle me-3" 
                         style="width: 48px; height: 48px; object-fit: cover;">
                  @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 48px; height: 48px; font-size: 1.25rem; font-weight: bold;">
                      {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                  @endif
                  <div>
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                    <br><small class="badge bg-primary">{{ Auth::user()->role_name }}</small>
                    @if(Auth::user()->city)
                      <br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Auth::user()->city }}</small>
                    @endif
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="{{ route('orders.index') }}">
                  <i class="fas fa-shopping-bag me-2"></i>
                  My Orders
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="fas fa-user-edit me-2"></i>
                  Edit Profile
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <!-- Guest Links -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
              <i class="fas fa-sign-in-alt me-1"></i>
              Login
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
              <i class="fas fa-user-plus me-1"></i>
              Register
            </a>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>