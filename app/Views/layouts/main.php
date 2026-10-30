<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SmartCRM') ?> | SmartCRM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --crm-sidebar: #172033;
            --crm-primary: #4361ee;
            --crm-bg: #f5f7fc;
            --crm-text: #1c2541;
            --crm-muted: #7b849b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--crm-bg);
            color: var(--crm-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .crm-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            background: linear-gradient(180deg, #172023, #111a2c 100%);
            color: #fff;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            overflow-y: auto;
            transition: transform .25s ease;
        }

        .crm-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -.7px;
            color: #fff;
            padding: 0 10px 32px;
        }

        .crm-brand-icon {
            display: grid;
            place-items: center;
            width: 39px;
            height: 39px;
            border-radius: 13px;
            color: white;
            background: linear-gradient(135deg, #5d7cff, #7848ee);
            box-shadow: 0 6px 18px #4361ee55;
        }

        .sidebar-label {
            color: #8490aa;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            padding: 0 13px;
            margin: 0 0 12px;
        }

        .crm-nav {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .crm-nav a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            color: #c5cce0;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: .2s;
        }

        .crm-nav a i { font-size: 18px; }

        .crm-nav a:hover {
            color: #fff;
            background: #ffffff12;
        }

        .crm-nav a.active {
            color: #fff;
            background: linear-gradient(100deg, #4361ee, #5478ff);
            box-shadow: 0 7px 18px #4361ee30;
        }

        .crm.sidebar-bottom {
            margin-top: auto;
            padding-top: 28px;
        }

        .crm-profile {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 15px 10px 4px;
            border-top: 1px solid #ffffff18;
        }

        .crm-avatar {
            width: 39px;
            height: 39px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 50%;
            color: white;
            background: linear-gradient(135deg, #617dff, #8c58e9);
            font-weight: 700;
        }

        .crm-main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .crm-topbar {
            min-height: 76px;
            background: #fff;
            border-bottom: 1px solid #e9edf5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 14px 30px;
        }

        .crm-top-title { font-size: 14px; font-weight: 700; }
        .crm-top-subtitle { color: var(--crm-muted); font-size: 12px; margin-top: 3px; }

        .crm-content { padding: 30px; max-width: 1700px; margin: 0 auto; }

        .crm-card {
            background: #fff;
            border: 1px solid #edf0f6;
            border-radius: 16px;
            box-shadow: 0 4px 18px #26365b06;
        }

        .crm-card-title { font-size: 15px; font-weight: 750; }

        .crm-btn-primary {
            color: #fff;
            background: var(--crm-primary);
            border: 1px solid var(--crm-primary);
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
        }

        .crm-btn-primary:hover {
            color: #fff;
            background: #304de0;
        }

        .crm-icon-button {
            width: 40px;
            height: 40px;
            display: inline-grid;
            place-items: center;
            border: 1px solid #e9edf5;
            background: #fff;
            color: #536079;
            border-radius: 11px;
        }

        .crm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: #11182780;
            z-index: 1030;
        }

        .alert { border-radius: 12px; }

        @media (max-width: 991.98px) {
            .crm-sidebar { transform: translateX(-100%); }
            .crm-sidebar.show { transform: translateX(0); }
            .crm-overlay.show { display: block; }
            .crm-main { margin-left: 0; }
            .crm-topbar { padding: 13px 18px; }
            .crm-content { padding: 20px 16px; }
        }

        @media (max-width: 575.98px) {
            .crm-topbar { gap: 10px; }
            .crm-top-subtitle { display: none; }
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php
        $currentPath = trim(uri_string(), '/');
        $userName = session()->get('user_name') ?: 'User';
        $userRole = session()->get('user_role') ?: 'Member';
        $initial = strtoupper(substr($userName, 0, 1));
    ?>

<div class="crm-overlay" id="crmOverlay"></div>
<aside class="crm-sidebar" id="crmSidebar"> <a href="<?= site_url('dashboard') ?>" class="crm-brand text-decoration-none"> <span class="crm-brand-icon"> <i class="bi bi-layers-half"></i></span> <span>SmartCRM</span></a>

<div class="sidebar-label">WORKSPACE</div>
<nav class="crm-nav mb-4">
    <a href="<?= site_url('dashboard') ?>"
    class="<?= in_array($currentPath, ['', 'dashboard'], true) ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
    </a>
    <a href="<?= site_url('leads') ?>"
    class="<?= str_starts_with($currentPath, 'leads') ? 'active' : '' ?>">
        <i class="bi bi-person-lines-fill"></i><span>Leads</span>
    </a>
    <a href="<?= site_url('customers') ?>"
    class="<?= str_starts_with($currentPath, 'customers') ? 'active' : '' ?>">
    <i class="bi bi-people"></i><span>Customers</span>
</a>
</nav>

<div class="sidebar-label">ACCOUNT</div>
<nav class="crm-nav">
    <a href="<?= site_url('dashboard') ?>">
        <i class="bi bi-person-circle"></i><span>My Profile</span>
    </a>
</nav>

<div class="crm-sidebar-bottom">
    <div class="crm-profile">
        <div class="crm-avatar"><?= esc($initial) ?></div>
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate"><?= esc($userName) ?></div>
            <small class="text-white-50"><?= esc(ucfirst($userRole)) ?></small>
        </div>
    </div>

    <form action="<?= site_url('logout') ?>" method="post" class="mt-3">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline-light w-100 rounded-3">
            <i class="bi bi-box-arrow-left me-2"></i>Sign Out
        </button>
    </form>
</div>
</aside>

<div class="crm-main">
    <header class="crm-topbar">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="crm-icon-button d-lg-none" id="crmMenuToggle" aria-label="Open navigation">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div>
                <div class="crm-top-title">SmartCRM Workspace</div>
                <div class="crm-top-subtitle">Manage relationships. Grow your business.</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block">
                <div class="fw-semibold small"><?= esc($userName) ?></div>
                <div class="text-secondary" style="font-size:11px"><?= esc(ucfirst($userRole)) ?></div>
            </div>
            <div class="crm-avatar"><?= esc($initial) ?></div>
        </div>
    </header>

    <main class="crm-content">
        <?php if ($message = session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= esc($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($message = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?= esc($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?= $this->renderSection('content') ?>
    </main>
</div>

<script src="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js)"></script>

<script>
(() => {
 const sidebar = document.getElementById('crmSidebar');
 const overlay = document.getElementById('crmOverlay');
 const toggle = document.getElementById('crmMenuToggle');
 function closeMenu() {
     sidebar.classList.remove('show');
     overlay.classList.remove('show');
 }
 if (toggle) {
     toggle.addEventListener('click', () => {
         sidebar.classList.toggle('show');
         overlay.classList.toggle('show');
     });
}
 overlay.addEventListener('click', closeMenu);
 window.addEventListener('resize', () => {
     if (window.innerWidth >= 992) closeMenu();
 });
})();
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>