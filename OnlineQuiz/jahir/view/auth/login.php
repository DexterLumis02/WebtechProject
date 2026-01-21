<div class="auth-form">
    <h2>Login</h2>
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="<?php echo base_url('login'); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" data-required="true">
        </div>
        <div class="form-group">
            <label>Login as</label>
            <label>
                <input type="radio" name="role" value="user" <?php echo (($role ?? 'user') === 'user') ? 'checked' : ''; ?>>
                User
            </label>
            <label>
                <input type="radio" name="role" value="admin" <?php echo (($role ?? 'user') === 'admin') ? 'checked' : ''; ?>>
                Admin
            </label>
        </div>
        <div class="form-group checkbox">
            <label>
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary">Login</button>
            <a href="<?php echo base_url('forgot-password'); ?>">Forgot password?</a>
        </div>
    </form>
</div>

