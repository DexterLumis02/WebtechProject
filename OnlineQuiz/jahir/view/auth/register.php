<div class="auth-form">
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <
        <div class="auth-form">
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="<?php echo base_url('register'); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="reg-email">Email</label>
            <input id="reg-email" type="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="reg-password">Password</label>
            <input id="reg-password" type="password" name="password" data-required="true">
        </div>
        <div class="form-group">
            <label for="reg-confirm">Confirm password</label>
            <input id="reg-confirm" type="password" name="confirm_password" data-required="true">
        </div>
        <div class="form-group">
            <label>Register as</label>
            <label>
                <input type="radio" name="role" value="user" <?php echo (($role ?? 'user') === 'user') ? 'checked' : ''; ?>>
                User
            </label>
            <label>
                <input type="radio" name="role" value="admin" <?php echo (($role ?? 'user') === 'admin') ? 'checked' : ''; ?>>
                Admin
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary">Register</button>
            <a href="<?php echo base_url('login'); ?>">Already have an account?</a>
        </div>
    </form>
</div>
