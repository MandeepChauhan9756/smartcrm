<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$leadCounts = [
    'New'       => (int) ($counts['new'] ?? 0),
    'Contacted' => (int) ($counts['contacted'] ?? 0),
    'Qualified' => (int) ($counts['qualified'] ?? 0),
    'Won'       => (int) ($counts['won'] ?? 0),
    'Lost'      => (int) ($counts['lost'] ?? 0),
];

$totalLeads = (int) ($counts['total'] ?? 0);

$stateCards = [
    [
        'label' =>  'Total Leads',
        'value' =>  $totalLeads,
        'icon'  =>  'bi-people-fill',
        'color' =>  'primary',
        'note'  =>  'All recorded leads',
    ],
    [
        'label' =>  'New Leads',
        'value' =>  $leadCounts['New'],
        'icon'  =>  'bi-person-plus-fill',
        'color' =>  'info',
        'note'  =>  'Awaiting first contact',
    ],
    [
        'label' =>  'Qualified Leads',
        'value' =>  $leadCounts['Qualified'],
        'icon'  =>  'bi-patch-check-fill',
        'color' =>  'warning',
        'note'  =>  'Potential opportunities',
    ],
    [
        'label' =>  'Won Leads',
        'value' =>  $leadCounts['Won'],
        'icon'  =>  'bi-trophy-fill',
        'color' =>  'success',
        'note'  =>  'Successfully converted',
    ],
];

$statusColors = [
    'New'       => '#4361ee',
    'Contacted' => '#0ea5e9',
    'Qualified' => '#f59e0b',
    'Won'       => '#10b981',
    'Lost'      => '#ef4444',
];

$chartLabels = array_keys($leadCounts);
$chartValues = array_values($leadCounts);
$chartColors = array_values($statusColors);
?>

<style>
    .dashboard-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 27px;
    }

    .dashboard-heading h1 {
        font-size: clamp(24px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 7px;
    }

    .dashboard-heading p {
        color: #7b849b;
        margin: 0;
        font-size: 14px;
    }

    .stat-card {
        padding: 22px;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px #26365b0c;
    }

    .stat-icon {
        width: 47px;
        height: 47px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        font-size: 21px;
    }

    .icon-primary { color: #4361ee; background: #edf0ff; }
    .icon-info    { color: #0284c7; background: #e7f6ff; }
    .icon-warning { color: #d97706; background: #fff5df; }
    .icon-success { color: #059669; background: #e2f8ef; }

    .stat-label { color: #7b849b; font-size: 13px; font-weight: 600; }
    .stat-value { font-size: 32px; line-height: 1.3; font-weight: 800; letter-spacing: -1px; }
    .stat-note { color: #8a93a7; font-size: 12px; }

    .dashboard-panel { padding: 23px; height: 100%; }
    .panel-subtitle { color: #8992a6; font-size: 12px; margin-top: 5px; }

    .status-track {
        width: 100%;
        height: 8px;
        background: #eef1f7;
        border-radius: 30px;
        overflow: hidden;
        margin-top: 9px;
    }

    .status-fill { height: 100%; border-radius: 30px; }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 13px;
        height: 100%;
        padding: 17px;
        border: 1px solid #edf0f6;
        background: white;
        border-radius: 14px;
        color: inherit;
        text-decoration: none;
        transition: .2s;
    }

    .quick-action:hover {
        border-color: #cbd5ff;
        transform: translateY(-2px);
        box-shadow: 0 8px 22px #26365b09;
    }

    .quick-action-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        background: #edf0ff;
        color: #4361ee;
        font-size: 19px;
    }

    .quick-action-title { font-size: 13px; font-weight: 750; }
    .quick-action-description { color: #8a93a7; font-size: 11px; margin-top: 4px; }

    .chart-wrap { position: relative; height: 280px; }

    @media (max-width: 575.98px) {
        .dashboard-heading { flex-direction: column; }
        .stat-card { padding: 17px; }
        .stat-value { font-size: 28px; }
        .dashboard-panel { padding: 18px; }
        .chart-wrap { height: 235px; }
    }

</style>

<div class="dashboard-heading">
    <div>
        <h1>Dashboard <span aria-hidden="true">👋</span></h1>
        <p>Here's what's happening with your CRM today.</p>
    </div>

    <a href="<?= site_url('leads/create') ?>" class="btn crm-btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add New Lead
    </a>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($stateCards as $card): ?>
        <div class="col-6 col-xl-3">
            <div class="crm-card stat-card">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-4">
                    <div class="stat-label"><?= esc($card['label']) ?></div>
                    <div class="stat-icon icon-<?= esc($card['color']) ?>">
                        <i class="bi <?= esc($card['icon']) ?>"></i>
                    </div>
                </div>

                <div class="stat-value"><?= number_format($card['value']) ?></div>
                <div class="stat-note mt-2"><?= esc($card['note']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Analytics -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <section class="crm-card dashboard-panel">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="crm-card-title">Lead Overview</div>
                    <div class="panel-subtitle">Distribution of your current leads</div>
                </div>
                <span class="badge rounded-pill text-bg-light border">All time</span>
            </div>

            <div class="chart-wrap">
                <canvas id="leadOverviewChart" aria-label="Lead status bar chart" role="img"></canvas>
            </div>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="crm-card dashboard-panel">
            <div class="crm-card-title">Lead Status</div>
            <div class="panel-subtitle mb-4">Current pipeline breakdown</div>

            <?php foreach ($leadCounts as $status => $value): ?>
                <?php $percentage = $totalLeads > 0 ? round(($value / $totalLeads) * 100) : 0; ?>
                <div class="status-row">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: <?= esc($statusColors[$status]) ?>; display:inline-block"></span>
                            <span class="small fw-semibold"><?= esc($status) ?></span>
                        </div>

                        <div class="small text-secondary">
                            <strong class="text-dark"><?= number_format($value) ?></strong>
                            <span class="ms-1">(<?= $percentage ?>%)</span>
                        </div>
                    </div>
                    <div class="status-track">
                        <div class="status-fill" style="width: <?= $percentage ?>%; background: <?= esc($statusColors[$status]) ?>"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="border-top mt-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Total leads</span>
                <span class="fw-bold"><?= number_format($totalLeads) ?></span>
            </div>
        </section>
    </div>
</div>

<!-- Quick Actions -->
 <div class="mb-3">
    <h2 class="crm-card-title mb-1">Quick Actions</h2>
    <div class="panel-subtitle">Get things done faster</div>
 </div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <a href="<?= site_url('leads/create') ?>" class="quick-action">
            <span class="quick-action-icon"><i class="bi bi-person-plus"></i></span>
            <span>
                <span class="quick-action-title d-block">Create a Lead</span>
                <span class="quick-action-description d-block">Add a new potential customer</span>
            </span>
            <i class="bi bi-arrow-up-right ms-auto text-secondary"></i>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <a href="<?= site_url('leads') ?>" class="quick-action">
            <span class="quick-action-icon" style="background:#e3f8ef;color:#059669">
                <i class="bi bi-card-list"></i>
            </span>
            <span>
                <span class="quick-action-title d-block">Manage Leads</span>
                <span class="quick-action-description d-block">Review and update your pipeline</span>
            </span>
            <i class="bi bi-arrow-up-right ms-auto text-secondary"></i>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <a href="<?= site_url('leads') ?>" class="quick-action">
            <span class="quick-action-icon" style="background:#fff2df;color:#d97706">
                <i class="bi bi-funnel"></i>
            </span>
            <span>
                <span class="quick-action-title d-block">Review Pipeline</span>
                <span class="quick-action-description d-block">Check lead status and progress</span>
            </span>
            <i class="bi bi-arrow-up-right ms-auto text-secondary"></i>
        </a>
    </div>
</div>

<div class="text-center text-secondary small py-3">
    <i class="bi bi-shield-check me-1"></i>
    SmartCRM · Your customer relationships, organized.
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('leadOverviewChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = <?= json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const values = <?= json_encode($chartValues) ?>;
    const colors = <?= json_encode($chartColors) ?>;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Leads',
                data: values,
                backgroundColor: colors.map(color => color + 'cc'),
                borderColor: colors,
                borderWidth: 1,
                borderRadius: 7,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { displayColors: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#7b849b' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#7b849b' },
                    grid: { color: '#edf0f6' },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>