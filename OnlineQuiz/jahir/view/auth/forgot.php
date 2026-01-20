<div class="auth-form">
    <h2>Reset password</h2>
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="<?php echo base_url('forgot-password'); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="forgot-email">Email</label>
            <input id="forgot-email" type="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="forgot-password">New password</label>
            <input id="forgot-password" type="password" name="password" data-required="true">
        </div>
        <div class="form-group">
            <label for="forgot-confirm">Confirm password</label>
            <input id="forgot-confirm" type="password" name="confirm_password" data-required="true">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary">Update password</button>
            <a href="<?php echo base_url('login'); ?>">Back to login</a>
        </div>
    </form>
</div>

