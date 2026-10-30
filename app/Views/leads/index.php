
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$search = $search ?? '';
$selectedStatus = $selectedStatus ?? '';
$selectedPriority = $selectedPriority ?? '';

$statusLabels = [
    'new' => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'won' => 'Won',
    'lost' => 'Lost',
];

$priorityLabels = [
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="mb-1">Leads</h2>
        <p class="text-secondary mb-0">Manage customer enquiries</p>
    </div>

    <a href="<?= site_url('leads/create') ?>" class="btn btn-primary">
        + Add Lead
    </a>
</div>

<?php if ($success = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('leads') ?>">
            <div class="row g-2">
                <div class="col-lg-5 col-md-6">
                    <input
                        type="search"
                        name="q"
                        class="form-control"
                        placeholder="Search name, email, phone or company..."
                        value="<?= esc($search) ?>">
                </div>

                <div class="col-lg-3 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        <?php foreach ($statusLabels as $key => $label): ?>
                            <option value="<?= esc($key) ?>"
                                <?= $selectedStatus === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3">
                    <select name="priority" class="form-select">
                        <option value="">All priorities</option>
                        <?php foreach ($priorityLabels as $key => $label): ?>
                            <option value="<?= esc($key) ?>"
                                <?= $selectedPriority === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        Filter
                    </button>
                    <a href="<?= site_url('leads') ?>"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Phone</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Priority</th>
                    <th>Follow-up</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="9" class="text-center text-secondary py-5">
                        No leads found. Try changing your filters or add a lead.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <?php
                    $priority = $lead['priority'] ?? 'medium';

                    $priorityClass = [
                        'low' => 'text-bg-success',
                        'medium' => 'text-bg-warning',
                        'high' => 'text-bg-danger',
                    ][$priority] ?? 'text-bg-secondary';

                    $statusClass = [
                        'new' => 'text-bg-primary',
                        'contacted' => 'text-bg-info',
                        'qualified' => 'text-bg-warning',
                        'won' => 'text-bg-success',
                        'lost' => 'text-bg-danger',
                    ][$lead['status']] ?? 'text-bg-light';

                    $followUp = $lead['follow_up_date'] ?? null;
                    $isOverdue = $followUp
                        && $followUp < date('Y-m-d')
                        && ! in_array($lead['status'], ['won', 'lost'], true);
                    ?>

                    <tr>
                        <td>
                            <div class="fw-semibold">
                                <?= esc($lead['name']) ?>
                            </div>
                            <small class="text-secondary">
                                <?= esc($lead['email'] ?? '') ?>
                            </small>
                        </td>

                        <td><?= esc($lead['company'] ?? '-') ?></td>
                        <td><?= esc($lead['phone'] ?? '-') ?></td>
                        <td><?= esc(ucfirst($lead['source'] ?? 'other')) ?></td>

                        <td>
                            <span class="badge <?= esc($statusClass) ?>">
                                <?= esc($statusLabels[$lead['status']] ?? ucfirst($lead['status'])) ?>
                            </span>
                        </td>

                        <td><?= esc($lead['owner_name'] ?? 'Unassigned') ?></td>

                        <td>
                            <span class="badge <?= esc($priorityClass) ?>">
                                <?= esc($priorityLabels[$priority] ?? ucfirst($priority)) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($followUp): ?>
                                <span class="<?= $isOverdue ? 'text-danger fw-semibold' : '' ?>">
                                    <?= esc(date('d M Y', strtotime($followUp))) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <br>
                                    <small class="text-danger">Overdue</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-secondary">Not scheduled</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="<?= site_url('leads/' . $lead['id'] . '/edit') ?>">
                                    Edit
                                </a>

                                <?php if (session()->get('user_role') === 'admin'): ?>
                                    <form method="post"
                                          action="<?= site_url('leads/' . $lead['id'] . '/delete') ?>"
                                          onsubmit="return confirm('Delete this lead?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger"
                                                type="submit">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pager)): ?>
        <div class="card-body">
            <?= $pager->only(['q', 'status', 'priority'])->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
