<header class="site-header">
    <div class="container header-content">
        <a href="<?php echo base_url(''); ?>" class="logo">Online Quiz System</a>
        <nav class="nav">
            <?php if (!empty($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?php echo base_url('admin/dashboard'); ?>">Admin Dashboard</a>
                <?php else: ?>
                    <a href="<?php echo base_url('user/dashboard'); ?>">Dashboard</a>
                <?php endif; ?>
                <a href="<?php echo base_url('logout'); ?>">Logout</a>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>">Login</a>
                <a href="<?php echo base_url('register'); ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

