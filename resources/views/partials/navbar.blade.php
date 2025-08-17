<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
      <i class="fas fa-train me-2 text-warning"></i>
      {{ __('My Model Trains') }}
    </a>

    <!-- Mobile toggle button -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation items -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
        <!-- Home Link -->
        <li class="nav-item">
          <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
            <i class="fas fa-home me-1"></i>
            {{ __('Home') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
            <i class="fas fa-shopping-bag me-1"></i>
            {{ __('Products') }}
          </a>
        </li>

        <!-- Language Switcher (visible for all users) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-globe me-1"></i>{{ strtoupper(app()->getLocale()) }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'en']) }}">English</a></li>
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'ro']) }}">Română</a></li>
          </ul>
        </li>

        @auth
          <!-- Admin Links -->
          @if (Auth::user()->hasPermission('admin.access'))
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog me-1"></i>
                {{ __('Admin') }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                @if (Auth::user()->hasPermission('users.view'))
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                      <i class="fas fa-users me-2"></i>
                      {{ __('Manage Users') }}
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                @endif
                
                @if (Auth::user()->hasPermission('products.view'))
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.products.index') }}">
                      <i class="fas fa-box me-2"></i>
                      {{ __('Manage Products') }}
                    </a>
                  </li>
                @endif
                
                <li>
                  <a class="dropdown-item" href="{{ route('admin.messages.index') }}">
                    <i class="fas fa-envelope me-2"></i>
                    {{ __('Manage Messages') }}
                  </a>
                </li>
                
                @if (Auth::user()->hasPermission('categories.view'))
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                      <i class="fas fa-tags me-2"></i>
                      {{ __('Manage Categories') }}
                    </a>
                  </li>
                @endif
                
                @if (Auth::user()->hasPermission('orders.view'))
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                      <i class="fas fa-clipboard-list me-2"></i>
                      {{ __('Manage Orders') }}
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @endif

          <!-- Messages Notification -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle position-relative" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-envelope me-1"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unread-messages-count" style="display: none;">
                0
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown" style="min-width: 320px;">
              <li class="dropdown-header d-flex justify-content-between align-items-center">
                <span>{{ __('Messages') }}</span>
                <a href="{{ route('messages.index') }}" class="text-decoration-none small">{{ __('View All') }}</a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <div id="latest-messages">
                <li class="dropdown-item-text text-center text-muted py-3">
                  {{ __('No new messages') }}
                </li>
              </div>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item text-center" href="{{ route('messages.index') }}">
                  <i class="fas fa-inbox me-1"></i>
                  {{ __('Go to Inbox') }}
                </a>
              </li>
            </ul>
          </li>

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
                   {{ __('My Orders') }}
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('my.products') }}">
                  <i class="fas fa-box-open me-2"></i>
                  {{ __('My Products') }}
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('messages.index') }}">
                  <i class="fas fa-envelope me-2"></i>
                  {{ __('Messages') }}
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="fas fa-user-edit me-2"></i>
                   {{ __('Edit Profile') }}
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt me-2"></i>
                     {{ __('Logout') }}
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
              {{ __('Login') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
              <i class="fas fa-user-plus me-1"></i>
              {{ __('Register') }}
            </a>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>