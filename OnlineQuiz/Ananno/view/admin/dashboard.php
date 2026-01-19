<div>
    <h2>Admin dashboard</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Users</h3>
            <p><?php echo (int)$usersCount; ?></p>
        </div>
        <div class="stat-card">
            <h3>Exams</h3>
            <p><?php echo (int)$examsCount; ?></p>
        </div>
        <div class="stat-card">
            <h3>Attempts</h3>
            <p><?php echo (int)$attemptsCount; ?></p>
        </div>
        <div class="stat-card">
            <h3>Results</h3>
            <p><?php echo (int)$resultsCount; ?></p>
        </div>
    </div>

    <h3>Quick Actions</h3>
<div class="form-actions" style="margin-top: 1rem;">
    <a href="<?php echo base_url('admin/exams'); ?>" class="btn primary">Manage Exams</a>
    <a href="<?php echo base_url('admin/exams/create'); ?>" class="btn secondary">Create Exam</a>
    <a href="<?php echo base_url('admin/users'); ?>" class="btn secondary">Manage Users</a>
    <a href="<?php echo base_url('admin/results'); ?>" class="btn secondary">View Results</a>
</div>
</div>

