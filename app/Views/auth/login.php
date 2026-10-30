<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartCRM Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width:420px">
        <div class="card-body p-4">
            <h3 class="mb-2">SmartCRM</h3>
            <p class="text-secondary">Sign in to your account</p>

            <?php if ($error = session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc($error) ?></div>
            <?php endif; ?>

            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" name="email"
                           type="email" maxlength="190"
                           value="<?= esc(old('email') ?? '') ?>"
                           autocomplete="username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password"
                           type="password" autocomplete="current-password"
                           required>
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>