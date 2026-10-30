
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$stats = $stats ?? [
    'totalLeads' => 0,
    'convertedLeads' => 0,
    'pendingFollowUps' => 0,
    'overdueFollowUps' => 0,
];

$statusCounts = $statusCounts ?? [];
$monthlyLeads = $monthlyLeads ?? ['labels' => [], 'values' => []];
$recentLeads = $recentLeads ?? [];

$statusLabels = [
    'new' => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'won' => 'Won',
    'lost' => 'Lost',
];

$statusClasses = [
    'new' => 'text-bg-primary',
    'contacted' => 'text-bg-info',
    'qualified' => 'text-bg-warning',
    'won' => 'text-bg-success',
    'lost' => 'text-bg-danger',
];

$priorityClasses = [
    'low' => 'text-bg-success',
    'medium' => 'text-bg-warning',
    'high' => 'text-bg-danger',
];

$chartLabels = array_map(
    static fn ($key) => $statusLabels[$key] ?? ucfirst($key),
    array_keys($statusLabels)
);

$chartValues = [];

foreach (array_keys($statusLabels) as $key) {
    $chartValues[] = (int) ($statusCounts[$key] ?? 0);
}

$jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-secondary mb-0">
            Welcome back, <?= esc(session()->get('user_name') ?? 'User') ?>!
            Here's your leads overview.
        </p>
    </div>

    <a href="<?= site_url('leads/create') ?>" class="btn btn-primary">
        + Add Lead
    </a>
</div>

<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Total Leads</div>
                <h2 class="fw-bold mb-2">
                    <?= number_format($stats['totalLeads']) ?>
                </h2>
                <a href="<?= site_url('leads') ?>"
                   class="small text-decoration-none">
                    View all leads →
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Converted Leads</div>
                <h2 class="fw-bold text-success mb-2">
                    <?= number_format($stats['convertedLeads']) ?>
                </h2>
                <div class="small text-secondary">
                    Status: Won
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Pending Follow-ups</div>
                <h2 class="fw-bold text-primary mb-2">
                    <?= number_format($stats['pendingFollowUps']) ?>
                </h2>
                <div class="small text-secondary">
                    Due today or later
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Overdue Follow-ups</div>
                <h2 class="fw-bold text-danger mb-2">
                    <?= number_format($stats['overdueFollowUps']) ?>
                </h2>
                <div class="small text-secondary">
                    Past due date
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4 mb-4">

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-1">Monthly Leads</h5>
                <p class="text-secondary small mb-3">
                    New leads created over the last six months
                </p>
                <div style="height: 300px;">
                    <canvas id="monthlyLeadsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-1">Lead Status Breakdown</h5>
                <p class="text-secondary small mb-3">
                    Current distribution of your leads
                </p>
                <div style="height: 300px;">
                    <canvas id="leadStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card border-0 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">Recent Leads</h5>
            <p class="text-secondary small mb-0">
                Your latest lead activity
            </p>
        </div>
        <a href="<?= site_url('leads') ?>"
           class="btn btn-outline-primary btn-sm">
            View All
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Lead</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Follow-up</th>
                    <th>Assigned To</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($recentLeads)): ?>
                <tr>
                    <td colspan="7"
                        class="text-center text-secondary py-4">
                        No leads yet.
                        <a href="<?= site_url('leads/create') ?>">Create your first lead</a>.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentLeads as $lead): ?>
                    <?php
                    $status = $lead['status'] ?? 'new';
                    $priority = $lead['priority'] ?? 'medium';
                    $followUp = $lead['follow_up_date'] ?? null;
                    $isOverdue = $followUp
                        && $followUp < date('Y-m-d')
                        && ! in_array($status, ['won', 'lost'], true);
                    ?>

                    <tr>
                        <td class="fw-semibold">
                            <?= esc($lead['name']) ?>
                        </td>

                        <td><?= esc($lead['company'] ?: '-') ?></td>

                        <td>
                            <span class="badge <?= esc($statusClasses[$status] ?? 'text-bg-secondary') ?>">
                                <?= esc($statusLabels[$status] ?? ucfirst($status)) ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge <?= esc($priorityClasses[$priority] ?? 'text-bg-secondary') ?>">
                                <?= esc(ucfirst($priority)) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($followUp): ?>
                                <span class="<?= $isOverdue ? 'text-danger fw-semibold' : '' ?>">
                                    <?= esc(date('d M Y', strtotime($followUp))) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <div class="small text-danger">Overdue</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-secondary">Not scheduled</span>
                            <?php endif; ?>
                        </td>

                        <td><?= esc($lead['owner_name'] ?? 'Unassigned') ?></td>

                        <td>
                            <a href="<?= site_url('leads/' . $lead['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js for dashboard charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyLabels = <?= json_encode($monthlyLeads['labels'], $jsonFlags) ?>;
    const monthlyValues = <?= json_encode($monthlyLeads['values'], $jsonFlags) ?>;

    const statusLabels = <?= json_encode($chartLabels, $jsonFlags) ?>;
    const statusValues = <?= json_encode($chartValues, $jsonFlags) ?>;

    const monthlyCanvas = document.getElementById('monthlyLeadsChart');

    if (monthlyCanvas && window.Chart) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Leads Created',
                    data: monthlyValues,
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    const statusCanvas = document.getElementById('leadStatusChart');

    if (statusCanvas && window.Chart) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
