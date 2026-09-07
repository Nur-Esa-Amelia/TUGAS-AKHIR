<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dosen - Sistem Early Warning IKU/IKT')</title>

    <!-- Favicon / Logo Poltek -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/LOGO POLTEKKKKK.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/LOGO POLTEKKKKK.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Apply theme immediately before paint to prevent flash -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>


    <style>
        /* ==================== THEME VARIABLES ==================== */
        :root, html[data-theme="dark"] {
            --bg-base:        #090d16;
            --bg-surface:     #0f172a;
            --bg-surface2:    #1e293b;
            --bg-surface3:    #334155;
            --border:         #1e293b;
            --border-muted:   rgba(30,41,59,0.5);
            --text-primary:   #ffffff;
            --text-secondary: #f8fafc;
            --text-muted:     #94a3b8;
            --text-faint:     #64748b;
            --header-bg:      rgba(15,23,42,0.9);
            --scrollbar-track:#0f172a;
            --scrollbar-thumb:#1e293b;
            --scrollbar-hover:#334155;
            --table-th-bg:    rgba(15,23,42,0.4);
            --input-bg:       #1e293b;
            --input-border:   #334155;
            --nav-hover-bg:   rgba(255,255,255,0.04);
            --time-card-bg:   #090d16;
            --time-card-border: #1e293b;
            --time-card-label: #64748b;
            --time-card-val-1: #10b981;
            --time-card-val-2: #64748b;
            --time-icon-bg-1: rgba(16, 185, 129, 0.1);
            --time-icon-border-1: rgba(16, 185, 129, 0.2);
            --time-icon-color-1: #10b981;
            --time-icon-bg-2: rgba(100, 116, 139, 0.1);
            --time-icon-border-2: rgba(100, 116, 139, 0.2);
            --time-icon-color-2: #64748b;
            --tr-hover-bg:    rgba(30,41,59,0.25);
            --pagination-bg:  #1e293b;
            --pagination-border:#334155;
            --pagination-dis: #0f172a;
            --nav-active-bg:  #10b981;
            --nav-active-text:#ffffff;
            --link-accent:    #10b981;
            --link-hover:     #ffffff;
        }

        html[data-theme="light"] {
            --bg-base:        #f1f5f9;
            --bg-surface:     #ffffff;
            --bg-surface2:    #f8fafc;
            --bg-surface3:    #e2e8f0;
            --border:         #e2e8f0;
            --border-muted:   rgba(226,232,240,0.7);
            --text-primary:   #0f172a;
            --text-secondary: #1e293b;
            --text-muted:     #475569;
            --text-faint:     #64748b;
            --header-bg:      rgba(255,255,255,0.95);
            --scrollbar-track:#f1f5f9;
            --scrollbar-thumb:#cbd5e1;
            --scrollbar-hover:#94a3b8;
            --table-th-bg:    rgba(248,250,252,0.8);
            --input-bg:       #f8fafc;
            --input-border:   #cbd5e1;
            --nav-hover-bg:   rgba(0,0,0,0.04);
            --time-card-bg:   linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            --time-card-border: rgba(37,99,235,0.2);
            --time-card-label: rgba(255,255,255,0.8);
            --time-card-val-1: #ffffff;
            --time-card-val-2: #ffffff;
            --time-icon-bg-1: rgba(255, 255, 255, 0.15);
            --time-icon-border-1: rgba(255, 255, 255, 0.3);
            --time-icon-color-1: #ffffff;
            --time-icon-bg-2: rgba(255, 255, 255, 0.15);
            --time-icon-border-2: rgba(255, 255, 255, 0.3);
            --time-icon-color-2: #ffffff;
            --tr-hover-bg:    rgba(226,232,240,0.5);
            --pagination-bg:  #f1f5f9;
            --pagination-border:#e2e8f0;
            --pagination-dis: #ffffff;
            --nav-active-bg:  #d1fae5;
            --nav-active-text:#065f46;
            --link-accent:    #047857;
            --link-hover:     #064e3b;
        }

        /* ==================== THEME TOGGLE BUTTON ==================== */
        .theme-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-surface);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .theme-toggle-btn:hover {
            background: var(--bg-surface2);
            color: var(--text-primary);
            border-color: var(--bg-surface3);
        }
        .theme-toggle-btn svg { width: 18px; height: 18px; }
        html[data-theme="light"] .icon-moon { display: none; }
        html[data-theme="dark"]  .icon-sun  { display: none; }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-secondary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .admin-container {
            display: flex;
            width: 100vw;
            min-height: 100vh;
            padding-top: 80px;
        }

        .sidebar {
            width: 260px;
            background-color: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
            position: sticky;
            top: 80px;
            flex-shrink: 0;
            z-index: 45;
        }

        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .sidebar-subtitle {
            font-size: 0.75rem;
            color: #10b981;
            font-weight: 600;
        }

        .sidebar-nav {
            flex: 1;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
        }

        .nav-category {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .category-label {
            padding: 0 12px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--nav-hover-bg);
        }

        .nav-link.active {
            color: var(--nav-active-text) !important;
            background-color: var(--nav-active-bg) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.18);
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        /* Workspace Layout */
        .main-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            height: 80px;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 50;
            flex-shrink: 0;
        }

        .header-title-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
        }

        .menu-toggle-btn:hover {
            background-color: var(--bg-surface2);
            color: var(--text-primary);
        }

        .page-title-group {
            display: flex;
            flex-direction: column;
        }

        .page-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .page-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .user-profile-panel {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
        }

        .user-profile-panel:hover .user-avatar {
            transform: scale(1.08);
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
            border-color: #10b981;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .user-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .user-role {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }

        /* Body container */
        .main-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            flex: 1;
            overflow-y: auto;
        }

        /* Cards and Components */
        .card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }

        .welcome-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .welcome-text {
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 2;
        }

        .welcome-badge {
            align-self: flex-start;
            padding: 4px 10px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #34d399;
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .welcome-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 580px;
        }

        .system-time-card {
            background: var(--time-card-bg);
            border: 1px solid var(--time-card-border);
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .time-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .time-icon-1 {
            background-color: var(--time-icon-bg-1);
            border: 1px solid var(--time-icon-border-1);
            color: var(--time-icon-color-1);
        }
        
        .time-icon-2 {
            background-color: var(--time-icon-bg-2);
            border: 1px solid var(--time-icon-border-2);
            color: var(--time-icon-color-2);
        }

        .time-text {
            display: flex;
            flex-direction: column;
        }

        .time-label {
            font-size: 0.65rem;
            color: var(--time-card-label);
        }

        .time-value {
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .time-value-1 {
            color: var(--time-card-val-1);
        }
        
        .time-value-2 {
            color: var(--time-card-val-2);
        }

        /* Responsive Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-faint);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.assign {
            background-color: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #6366f1;
        }

        .stat-icon.upload {
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .stat-icon.valid {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .stat-icon.pending {
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .stat-footer {
            padding-top: 12px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-desc {
            font-size: 0.75rem;
            color: var(--text-faint);
        }

        .stat-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--link-accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-link:hover {
            color: var(--link-hover);
        }

        /* Buttons styling */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
            outline: none;
        }

        .btn-primary {
            background-color: #10b981;
            border-color: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .btn-primary:hover {
            background-color: #059669;
            border-color: #059669;
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--bg-surface3);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background-color: var(--bg-surface2);
            color: var(--text-primary);
            border-color: var(--text-faint);
        }

        .btn-rose {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .btn-rose:hover {
            background-color: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
        }

        .btn-action-delete {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0 !important;
            border-radius: 10px;
            border: 1px solid rgba(239, 68, 68, 0.25) !important;
            background-color: rgba(239, 68, 68, 0.05) !important;
            color: #f87171 !important;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .btn-action-delete:hover {
            border-color: #ef4444 !important;
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #ffffff !important;
        }

        /* Toast boxes */
        .alert-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* Forms Styling */
        .form-layout-container {
            max-width: 580px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group-custom {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.01em;
        }

        .form-input-custom {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        .form-input-custom:focus {
            border-color: #10b981;
            background-color: var(--input-bg);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-input-custom::placeholder {
            color: var(--text-faint);
        }

        .form-input-custom[readonly],
        .form-input-custom:disabled,
        .form-select-custom:disabled {
            background-color: var(--bg-surface2);
            border-color: var(--input-border);
            color: var(--text-primary);
            opacity: 1;
        }

        .form-select-custom {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .form-select-custom option {
            background-color: var(--input-bg);
            color: var(--text-primary);
        }

        .form-select-custom:focus {
            border-color: #10b981;
            background-color: var(--input-bg);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-error-custom {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 4px;
            font-weight: 500;
        }

        .form-footer-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            margin-top: 8px;
        }

        /* Filter rows */
        .filter-row-custom {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item-custom {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 180px;
        }

        /* Table custom designs */
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table-custom th {
            padding: 14px 20px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
            background-color: var(--table-th-bg);
        }

        .table-custom td {
            padding: 14px 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-muted);
            vertical-align: middle;
        }

        .table-custom tr:hover {
            background-color: var(--tr-hover-bg);
        }

        /* Badges */
        .badge-custom {
            display: inline-flex;
            padding: 2px 8px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 4px;
            letter-spacing: 0.05em;
            border: 1px solid transparent;
        }

        .badge-purple {
            background-color: rgba(168, 85, 247, 0.1);
            border-color: rgba(168, 85, 247, 0.2);
            color: #c084fc;
        }

        .badge-blue {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .badge-green {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .badge-rose {
            background-color: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.2);
            color: #fb7185;
        }

        .badge-yellow {
            background-color: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .badge-gray {
            background-color: rgba(100, 116, 139, 0.1);
            border-color: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
        }

        /* Responsive rules */
        @media (max-width: 1024px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            #sidebar-overlay.open {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .welcome-card {
                flex-direction: column;
                align-items: stretch;
            }
            .top-header {
                padding: 0 16px;
            }
            .main-body {
                padding: 16px;
            }
            .filter-row-custom {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* Laravel Pagination CSS Fix */
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
            margin-top: 10px;
        }

        nav[role="navigation"] p {
            margin: 0;
            font-size: 0.8rem;
            color: #64748b;
        }

        nav[role="navigation"] div:last-child {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 6px;
            margin-left: auto;
            overflow-x: auto;
            max-width: 100%;
            padding-bottom: 2px;
        }

        nav[role="navigation"] div:last-child > div > span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        nav[role="navigation"] div:last-child > div > span > a,
        nav[role="navigation"] div:last-child > div > span > span {
            margin-left: 0 !important;
        }

        nav[role="navigation"] a.relative,
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] span[aria-disabled="true"] > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            flex: 0 0 auto;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #cbd5e1;
            margin: 0;
            transition: all 0.2s ease;
        }

        nav[role="navigation"] a.relative:hover {
            background-color: #334155;
            color: #ffffff;
            border-color: #475569;
        }

        nav[role="navigation"] span[aria-current="page"] > span {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
            cursor: default;
        }

        nav[role="navigation"] span[aria-disabled="true"] > span {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            color: #475569 !important;
            opacity: 0.5;
            cursor: not-allowed;
        }

        nav[role="navigation"] svg {
            width: 14px;
            height: 14px;
        }

        @media (max-width: 640px) {
            nav[role="navigation"] {
                align-items: stretch;
            }

            nav[role="navigation"] p,
            nav[role="navigation"] div:last-child {
                width: 100%;
                margin-left: 0;
            }

            nav[role="navigation"] div:last-child {
                justify-content: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (mobileMenuBtn && sidebar && sidebarOverlay) {
                mobileMenuBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                    sidebarOverlay.classList.toggle('open');
                });

                sidebarOverlay.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('open');
                });
            }

            // Profile Dropdown Toggle
            const profileBtn = document.getElementById('profile-btn');
            const profileMenu = document.getElementById('profile-menu');
            
            if(profileBtn && profileMenu) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileMenu.style.display = profileMenu.style.display === 'none' || profileMenu.style.display === '' ? 'block' : 'none';
                });
                
                document.addEventListener('click', (e) => {
                    if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.style.display = 'none';
                    }
                });
            }

            // ===== Theme Toggle =====
            const themeBtn = document.getElementById('theme-toggle-btn');
            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-theme');
                    const next = current === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', next);
                    localStorage.setItem('theme', next);
                });
            }
        });
    </script>
</head>
<body>

    <div class="admin-container">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 40; display: none;"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <!-- MENU UTAMA -->
                <div class="nav-category">
                    <span class="category-label">MENU UTAMA</span>
                    <a href="{{ route('dosen.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('dosen.pencapaian.index') }}" 
                       class="nav-link {{ request()->routeIs('dosen.pencapaian.index') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Target & Capaian IKU/IKT
                    </a>

                    <a href="{{ route('dosen.pengisian.index') }}" 
                       class="nav-link {{ request()->routeIs('dosen.pengisian.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Riwayat Pengisian
                    </a>
                </div>
            </nav>

        </aside>

        <!-- Main Workspace -->
        <div class="main-content">
            <!-- Top bar Header -->
            <header class="top-header" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 50; background-color: #007bff; color: white; border-bottom: none; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
                <div class="header-left" style="display: flex; align-items: center; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <!-- Logo Placeholder -->
                        <div style="width: 52px; height: 52px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <img src="{{ asset('images/LOGO POLTEKKKKK.jpg') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.8rem; font-weight: 500; opacity: 0.9; margin-bottom: 2px;">SISTEM IKU/IKT</span>
                            <strong style="font-size: 1.25rem; font-weight: 700; letter-spacing: 0.5px; line-height: 1;">POLITEKNIK SUKABUMI</strong>
                        </div>
                    </div>
                </div>

                <!-- Theme Toggle + Right Actions -->
                <div style="display:flex;align-items:center;gap:20px;">
                    <button id="theme-toggle-btn" class="theme-toggle-btn" style="background: transparent; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Toggle Tema Gelap/Terang" aria-label="Toggle tema">
                        <!-- Moon icon -->
                        <svg class="icon-moon" style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                        </svg>
                        <!-- Sun icon -->
                        <svg class="icon-sun" style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10A5 5 0 0012 7z"/>
                        </svg>
                    </button>
                    


                    <!-- User Profile Dropdown -->
                    <div style="position: relative;" id="profile-dropdown-container">
                        <div id="profile-btn" style="display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #1e293b; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid rgba(255,255,255,0.8); font-size: 0.85rem;">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div id="profile-menu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; width: 160px; background: white; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; z-index: 100;">
                            <a href="{{ route('profile') }}" style="display: block; padding: 12px 16px; color: #1e293b; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 12px 16px; color: #e11d48; font-size: 0.9rem; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="main-body">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Floating Toast Notification Pop-up Container -->
    <div id="toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; max-width: 420px; width: calc(100% - 48px); pointer-events: none;">
        @if(session('success'))
            <div class="toast-popup toast-success" style="pointer-events: auto; background: var(--bg-surface); border-left: 4px solid #10b981; border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); transition: all 0.3s ease;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">Berhasil!</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0; line-height: 1.4;">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.closest('.toast-popup').remove()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="Tutup">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-popup toast-danger" style="pointer-events: auto; background: var(--bg-surface); border-left: 4px solid #ef4444; border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); transition: all 0.3s ease;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">Terjadi Kesalahan</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0; line-height: 1.4;">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="this.closest('.toast-popup').remove()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="Tutup">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any() && !request()->routeIs('*.store'))
            <div class="toast-popup toast-danger" style="pointer-events: auto; background: var(--bg-surface); border-left: 4px solid #ef4444; border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); transition: all 0.3s ease;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0;">Periksa Form Input:</h4>
                    <ul style="list-style-type: none; margin: 0; padding: 0; font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4;">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" onclick="this.closest('.toast-popup').remove()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="Tutup">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    <style>
    @keyframes toastSlideIn {
        from {
            transform: translateX(100%) scale(0.9);
            opacity: 0;
        }
        to {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
    }
    .toast-popup.fade-out {
        opacity: 0;
        transform: translateX(100%);
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('#toast-container .toast-popup');
        toasts.forEach(function (t) {
            setTimeout(function () {
                t.classList.add('fade-out');
                setTimeout(function () {
                    t.remove();
                }, 300);
            }, 4500);
        });
    });
    </script>

    <!-- Custom Floating Modal: Unggah Bukti IKU/IKT -->
    @auth
        @if(auth()->user()->role === 'dosen')
            @php
                $dosenUser = auth()->user();
                $dosenSettings = \App\Models\Pengaturan::where('id_prodi', $dosenUser->prodi_id)->first();
                $dosenTahunAktif = $dosenSettings?->tahun_aktif ?? date('Y');
                $dosenAssignedIkuIds = \App\Models\PenugasanDosen::where('id_user', $dosenUser->id)
                    ->where('tahun', $dosenTahunAktif)
                    ->pluck('id_iku')
                    ->toArray();
                $modalIkus = \App\Models\Iku::whereIn('id', $dosenAssignedIkuIds)->get();
                $modalBuktiIku = \App\Models\BuktiIku::whereIn('id_iku', $dosenAssignedIkuIds)->get();
            @endphp

            <div id="upload-bukti-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; transition: all 0.3s ease;">
                <div style="background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 0 30px rgba(16, 185, 129, 0.15); border-radius: 12px; width: 100%; max-width: 650px; max-height: 90vh; display: flex; flex-direction: column; animation: modalSlideIn 0.25s ease-out; overflow: hidden;">
                    <!-- Modal Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 16px 20px; background: var(--bg-surface);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">Unggah Bukti Kinerja - Tahun {{ $dosenTahunAktif }}</h3>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">Kirim berkas bukti pemenuhan IKU/IKT yang ditugaskan</p>
                            </div>
                        </div>
                        <button type="button" id="btn-close-upload-modal" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; transition: all 0.2s;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Modal Body Form -->
                    <form action="{{ route('dosen.pengisian.store') }}" method="POST" enctype="multipart/form-data" style="padding: 20px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 16px;">
                        @csrf
                        <!-- IKU Select -->
                        <div class="form-group-custom">
                            <label for="modal_id_iku" class="form-label-custom">Pilih Indikator IKU/IKT Ditugaskan</label>
                            <select id="modal_id_iku" name="id_iku" class="form-select-custom" required>
                                <option value="">-- Pilih Indikator --</option>
                                @foreach($modalIkus as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->nama_iku }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bukti Select -->
                        <div class="form-group-custom">
                            <label for="modal_id_bukti_iku" class="form-label-custom">Pilih Jenis Bukti Dokumen</label>
                            <select id="modal_id_bukti_iku" name="id_bukti_iku" class="form-select-custom" required>
                                <option value="">-- Pilih Jenis Bukti --</option>
                                @foreach($modalBuktiIku as $b)
                                    <option value="{{ $b->id }}" data-iku-id="{{ $b->id_iku }}">
                                        {{ $b->nama_bukti }}
                                    </option>
                                @endforeach
                            </select>
                            <small style="color: var(--text-muted); font-size: 0.72rem; margin-top: 2px;">Pilihan jenis bukti disesuaikan secara otomatis berdasarkan IKU/IKT yang Anda pilih.</small>
                        </div>

                        <!-- File Inputs Container -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Unggah Berkas Bukti</label>
                            <div id="modal-file-inputs-container" style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="modal-file-input-card" style="background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 10px; padding: 14px; display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span class="modal-file-number-label" style="font-size: 0.78rem; font-weight: 700; color: #10b981;">Berkas Bukti #1</span>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <label class="form-label-custom" style="font-size: 0.72rem;">Pilih Berkas</label>
                                        <input type="file" name="files[]" class="form-input-custom modal-file-selector-input" required>
                                    </div>
                                    <div class="modal-keterangan-file-group" style="display: none; flex-direction: column; gap: 4px;">
                                        <label class="form-label-custom" style="font-size: 0.72rem;">Keterangan Berkas (Opsional)</label>
                                        <textarea name="keterangan_files[]" rows="2" placeholder="Tulis keterangan spesifik (contoh: Link Google Drive, Judul Dokumen, dll)..." class="form-input-custom"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="modal-add-file-btn" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; align-self: flex-start; margin-top: 8px; display: none;">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                </svg>
                                Tambah File Lainnya
                            </button>
                            <small style="color: var(--text-muted); font-size: 0.72rem; margin-top: 8px;">Format: PDF, JPG, JPEG, PNG, ZIP, DOC, DOCX (Maks 10 MB per file).</small>
                        </div>

                        <!-- Footer Actions -->
                        <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 14px; border-top: 1px solid var(--border); margin-top: 8px;">
                            <button type="button" id="btn-cancel-upload-modal" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary">Unggah Berkas Bukti</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const uploadModal = document.getElementById('upload-bukti-modal');
                const btnCloseUploadModal = document.getElementById('btn-close-upload-modal');
                const btnCancelUploadModal = document.getElementById('btn-cancel-upload-modal');
                const modalIkuSelect = document.getElementById('modal_id_iku');
                const modalBuktiSelect = document.getElementById('modal_id_bukti_iku');

                if (!uploadModal) return;

                const originalBuktiOptions = Array.from(modalBuktiSelect.querySelectorAll('option')).filter(opt => opt.value !== '');

                function updateModalBuktiOptions() {
                    const selectedIkuId = modalIkuSelect.value;
                    modalBuktiSelect.innerHTML = '<option value="">-- Pilih Jenis Bukti --</option>';
                    if (selectedIkuId) {
                        const filtered = originalBuktiOptions.filter(opt => opt.getAttribute('data-iku-id') === selectedIkuId);
                        if (filtered.length > 0) {
                            filtered.forEach(opt => modalBuktiSelect.appendChild(opt.cloneNode(true)));
                        } else {
                            const noOpt = document.createElement('option');
                            noOpt.value = "";
                            noOpt.disabled = true;
                            noOpt.textContent = "Belum ada jenis bukti yang dikonfigurasi untuk IKU/IKT ini oleh Admin Prodi";
                            modalBuktiSelect.appendChild(noOpt);
                        }
                    }
                }

                if (modalIkuSelect) {
                    modalIkuSelect.addEventListener('change', updateModalBuktiOptions);
                }

                document.querySelectorAll('.btn-open-upload-modal').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const ikuId = btn.getAttribute('data-iku-id');
                        if (ikuId && modalIkuSelect) {
                            modalIkuSelect.value = ikuId;
                            updateModalBuktiOptions();
                        }
                        uploadModal.style.display = 'flex';
                    });
                });

                function closeUploadModal() {
                    uploadModal.style.display = 'none';
                }

                if (btnCloseUploadModal) btnCloseUploadModal.addEventListener('click', closeUploadModal);
                if (btnCancelUploadModal) btnCancelUploadModal.addEventListener('click', closeUploadModal);

                window.addEventListener('click', function (e) {
                    if (e.target === uploadModal) closeUploadModal();
                });

                const fileContainer = document.getElementById('modal-file-inputs-container');
                const addFileBtn = document.getElementById('modal-add-file-btn');

                if (fileContainer && addFileBtn) {
                    function checkModalFiles() {
                        let hasFile = false;
                        fileContainer.querySelectorAll('.modal-file-input-card').forEach(card => {
                            const input = card.querySelector('.modal-file-selector-input');
                            const ket = card.querySelector('.modal-keterangan-file-group');
                            if (input && input.files && input.files.length > 0) {
                                hasFile = true;
                                if (ket) ket.style.display = 'flex';
                            } else {
                                if (ket) ket.style.display = 'none';
                            }
                        });
                        addFileBtn.style.display = hasFile ? 'inline-flex' : 'none';
                    }

                    fileContainer.addEventListener('change', function (e) {
                        if (e.target.classList.contains('modal-file-selector-input')) {
                            checkModalFiles();
                        }
                    });

                    addFileBtn.addEventListener('click', function () {
                        const count = fileContainer.querySelectorAll('.modal-file-input-card').length + 1;
                        const card = document.createElement('div');
                        card.className = 'modal-file-input-card';
                        card.style.cssText = 'background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 10px; padding: 14px; display: flex; flex-direction: column; gap: 10px; margin-top: 10px;';
                        card.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="modal-file-number-label" style="font-size: 0.78rem; font-weight: 700; color: #10b981;">Berkas Bukti #${count}</span>
                                <button type="button" class="btn-action-delete remove-modal-file-btn" style="padding: 6px; height: auto; width: auto; border-radius: 6px;" title="Hapus File">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <label class="form-label-custom" style="font-size: 0.72rem;">Pilih Berkas</label>
                                <input type="file" name="files[]" class="form-input-custom modal-file-selector-input" required>
                            </div>
                            <div class="modal-keterangan-file-group" style="display: none; flex-direction: column; gap: 4px;">
                                <label class="form-label-custom" style="font-size: 0.72rem;">Keterangan Berkas (Opsional)</label>
                                <textarea name="keterangan_files[]" rows="2" placeholder="Tulis keterangan spesifik (contoh: Link Google Drive, Judul Dokumen, dll)..." class="form-input-custom"></textarea>
                            </div>
                        `;
                        fileContainer.appendChild(card);
                    });

                    fileContainer.addEventListener('click', function (e) {
                        const delBtn = e.target.closest('.remove-modal-file-btn');
                        if (delBtn) {
                            const card = delBtn.closest('.modal-file-input-card');
                            if (card) {
                                card.remove();
                                checkModalFiles();
                            }
                        }
                    });
                }
            });
            </script>
        @endif
    @endauth
</body>
</html>
