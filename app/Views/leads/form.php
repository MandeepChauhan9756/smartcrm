
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($lead);
$action = $isEdit
    ? site_url('leads/' . $lead['id'] . '/update')
    : site_url('leads/store');

$value = static function (string $field, $default = '') use ($lead) {
    return old($field) ?? ($lead[$field] ?? $default);
};
?>

<div class="mb-4">
    <h2><?= $isEdit ? 'Edit Lead' : 'Create Lead' ?></h2>
    <p class="text-secondary">Enter the customer information below.</p>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Please correct these fields:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Customer Name *</label>
                    <input id="name" name="name" class="form-control"
                           maxlength="150" required
                           value="<?= esc($value('name')) ?>">
                </div>

                <div class="col-md-6">
                    <label for="company" class="form-label">Company</label>
                    <input id="company" name="company" class="form-control"
                           maxlength="150"
                           value="<?= esc($value('company')) ?>">
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email"
                           maxlength="190" class="form-control"
                           value="<?= esc($value('email')) ?>">
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" name="phone" type="tel"
                           maxlength="30" class="form-control"
                           value="<?= esc($value('phone')) ?>">
                </div>

                <div class="col-md-6">
                    <label for="source" class="form-label">Lead Source *</label>
                    <select id="source" name="source" class="form-select" required>
                        <?php foreach ([
                            'website' => 'Website',
                            'referral' => 'Referral',
                            'linkedin' => 'LinkedIn',
                            'other' => 'Other',
                        ] as $key => $label): ?>
                            <option value="<?= esc($key) ?>"
                                <?= $value('source', 'other') === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        <?php foreach ([
                            'new' => 'New',
                            'contacted' => 'Contacted',
                            'qualified' => 'Qualified',
                            'won' => 'Won',
                            'lost' => 'Lost',
                        ] as $key => $label): ?>
                            <option value="<?= esc($key) ?>"
                                <?= $value('status', 'new') === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- NEW: Lead Priority -->
                <div class="col-md-6">
                    <label for="priority" class="form-label">Priority *</label>
                    <select id="priority" name="priority" class="form-select" required>
                        <?php foreach ([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                        ] as $key => $label): ?>
                            <option value="<?= esc($key) ?>"
                                <?= $value('priority', 'medium') === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- NEW: Follow-up Date -->
                <div class="col-md-6">
                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                    <input
                        id="follow_up_date"
                        name="follow_up_date"
                        type="date"
                        class="form-control"
                        value="<?= esc($value('follow_up_date')) ?>">
                    <div class="form-text">
                        Select when you need to contact this lead again.
                    </div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              maxlength="5000"
                              class="form-control"><?= esc($value('notes')) ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Lead' : 'Save Lead' ?>
                </button>
                <a href="<?= site_url('leads') ?>"
                   class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
