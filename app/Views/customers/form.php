<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = ! empty($customer);
$formAction = $isEdit
    ? site_url('customers/' . $customer['id'] . '/update')
    : site_url('customers/store');

function customerFieldValue($field, $customer)
{
    return old($field, $customer[$field] ?? '');
}
?>

<div class="mb-4">
    <a href="<?= site_url('customers') ?>" class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Back to customers
    </a>
    <h1 class="fw-bold mt-3 mb-1"><?= $isEdit ? 'Edit Customer' : 'Add Customer' ?></h1>
    <p class="text-secondary mb-0">
        <?= $isEdit ? 'Update customer information.' : 'Enter the details of your new customer.' ?>
    </p>
</div>

<div class="crm-card p-3 p-md-4">
    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Customer Name *</label>
                <input type="text" name="name" class="form-control"
                       maxlength="150" required
                       value="<?= esc(customerFieldValue('name', $customer)) ?>"
                       placeholder="Enter full name">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Company</label>
                <input type="text" name="company" class="form-control"
                       maxlength="150"
                       value="<?= esc(customerFieldValue('company', $customer)) ?>"
                       placeholder="Company name">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control"
                       maxlength="190"
                       value="<?= esc(customerFieldValue('email', $customer)) ?>"
                       placeholder="name@example.com">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="tel" name="phone" class="form-control"
                       maxlength="30"
                       value="<?= esc(customerFieldValue('phone', $customer)) ?>"
                       placeholder="Contact number">
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" rows="2"
                          maxlength="5000"
                          placeholder="Customer address"><?= esc(customerFieldValue('address', $customer)) ?></textarea>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="3"
                          maxlength="5000"
                          placeholder="Additional customer information"><?= esc(customerFieldValue('notes', $customer)) ?></textarea>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <a href="<?= site_url('customers') ?>" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn crm-btn-primary">
                <i class="bi bi-check2-circle me-2"></i>
                <?= $isEdit ? 'Save Changes' : 'Save Customer' ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>