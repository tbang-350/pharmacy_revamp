<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmacy Management System')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-prescription2"></i> Pharmacy System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text"><small>Role: {{ auth()->user()->roles->first()->name ?? 'User' }}</small></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.create') }}">
                                <i class="bi bi-cart-plus"></i> New Sale
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sales.index') }}">
                                <i class="bi bi-receipt"></i> Sales History
                            </a>
                        </li>
                        
                        @can('make purchases')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('purchases.create') ? 'active' : '' }}" href="{{ route('purchases.create') }}">
                                <i class="bi bi-bag-plus"></i> New Purchase
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('purchases.index') }}">
                                <i class="bi bi-bag"></i> Purchase History
                            </a>
                        </li>
                        @endcan
                        
                        @can('manage products')
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading px-3 text-muted">Inventory</h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">
                                <i class="bi bi-box"></i> Products
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.stock') }}">
                                <i class="bi bi-boxes"></i> Stock Management
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-tags"></i> Categories
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('suppliers.index') }}">
                                <i class="bi bi-truck"></i> Suppliers
                            </a>
                        </li>
                        @endcan
                        
                        @can('view reports')
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading px-3 text-muted">Reports</h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.sales') }}">
                                <i class="bi bi-graph-up"></i> Sales Report
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.purchases') }}">
                                <i class="bi bi-graph-down"></i> Purchase Report
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.stock') }}">
                                <i class="bi bi-clipboard-data"></i> Stock Report
                            </a>
                        </li>
                        @endcan
                        
                        @can('manage users')
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading px-3 text-muted">Administration</h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Global Form Submission Handler -->
    <script>
    // Handle all form submissions with spinner animation
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Skip GET forms (filters/search forms)
        if (form.method.toUpperCase() === 'GET') {
            return;
        }
        
        // Skip forms with custom onsubmit handler (like Excel import which already has spinner)
        if (form.hasAttribute('onsubmit') && form.getAttribute('onsubmit').includes('showImportLoader')) {
            return;
        }
        
        // Find submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        // Store original button content
        const originalContent = submitBtn.innerHTML;
        const originalDisabled = submitBtn.disabled;
        
        // Disable button and show spinner
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        
        // Re-enable if validation fails (will be caught by browser's built-in validation)
        setTimeout(() => {
            if (!form.checkValidity()) {
                submitBtn.disabled = originalDisabled;
                submitBtn.innerHTML = originalContent;
            }
        }, 100);
    });
    
    // Handle delete forms with confirmation
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('button[type="submit"]');
        if (!deleteBtn) return;
        
        const form = deleteBtn.closest('form');
        if (!form) return;
        
        // Check if it's a delete form (has DELETE method)
        const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
        if (methodInput && deleteBtn.hasAttribute('onclick') && deleteBtn.getAttribute('onclick').includes('confirm')) {
            // Form already has inline confirm, enhance with spinner after confirmation
            const originalOnclick = deleteBtn.getAttribute('onclick');
            deleteBtn.removeAttribute('onclick');
            
            deleteBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                if (confirm('Delete this item?')) {
                    // Disable button and show spinner
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    form.submit();
                }
            }, { once: true });
        }
    });
    </script>
    
    @stack('scripts')
</body>
</html>
