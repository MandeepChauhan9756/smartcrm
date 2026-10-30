<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
<div>
    <h1 class="fw-bold mb-1">Customers</h1>
    <p class="text-secondary mb-0">Manage your customer relationships.</p>
</div>

<a href="<?= site_url('customers/create') ?>" class="btn crm-btn-primary">
    <i class="bi bi-plus-lg me-2"></i>Add Customer
</a>
</div>

<div class="crm-card p-3 p-md-4">
<form method="get" action="<?= site_url('customers') ?>" class="row g-2 mb-4">
    <div class="col-sm">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="search" name="q" class="form-control"
                    placeholder="Search name, email, phone or company..."
                    value="<?= esc($search) ?>">
        </div>
    </div>
    <div class="col-sm-auto d-flex gap-2">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="<?= site_url('customers') ?>" class="btn btn-light border">Reset</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr class="text-secondary small">
                <th>Customer</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Added</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($customers)): ?>
            <tr>
                <td colspan="6" class="text-center py-5">
                    <i class="bi bi-people fs-1 text-secondary"></i>
                    <h6 class="mt-3">No customers found</h6>
                    <p class="text-secondary small mb-3">Add a customer to start building your database.</p>
                    <a href="<?= site_url('customers/create') ?>" class="btn btn-primary btn-sm">
                        Add your first customer
                    </a>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="crm-avatar text-white"
                                    style="width:36px;height:36px;font-size:13px">
                                <?= esc(strtoupper(substr($customer['name'], 0, 1))) ?>
                            </div>
                            <div class="fw-semibold"><?= esc($customer['name']) ?></div>
                        </div>
                    </td>
                    <td><?= esc($customer['company'] ?: '—') ?></td>
                    <td><?= esc($customer['phone'] ?: '—') ?></td>
                    <td><?= esc($customer['email'] ?: '—') ?></td>
                    <td class="text-secondary small">
                        <?= ! empty($customer['created_at'])
                            ? esc(date('d M Y', strtotime($customer['created_at'])))
                            : '—' ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= site_url('customers/' . $customer['id'] . '/edit') ?>"
                            class="btn btn-sm btn-light border" title="Edit customer">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form method="post"
                                action="<?= site_url('customers/' . $customer['id'] . '/delete') ?>"
                                class="d-inline"
                                onsubmit="return confirm('Delete this customer? This cannot be undone.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-light border text-danger"
                                    title="Delete customer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-3">
    <?= $pager->only(['q'])->links() ?>
</div>
</div>

<?= $this->endSection() ?>