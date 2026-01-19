<h2>Users</h2>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($editUser)): ?>
    <h3>Edit user</h3>
    <form action="<?php echo base_url('admin/users/edit?id=' . (int)$editUser->id); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="edit-name">Name</label>
            <input id="edit-name" type="text" name="name" value="<?php echo htmlspecialchars($editUser->name, ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="edit-email">Email</label>
            <input id="edit-email" type="email" name="email" value="<?php echo htmlspecialchars($editUser->email, ENT_QUOTES, 'UTF-8'); ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="edit-password">Password (leave blank to keep)</label>
            <input id="edit-password" type="password" name="password">
        </div>
        <div class="form-group">
            <label for="edit-role">Role</label>
            <select id="edit-role" name="role_id">
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo (int)$role->id; ?>" <?php echo $role->id === $editUser->role_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group checkbox">
            <label>
                <input type="checkbox" name="is_active" value="1" <?php echo $editUser->is_active ? 'checked' : ''; ?>>
                Active
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary">Save</button>
            <a href="<?php echo base_url('admin/users'); ?>" class="btn secondary">Cancel</a>
        </div>
    </form>
<?php endif; ?>

<h3>All users</h3>
<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo (int)$user->id; ?></td>
                <td><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user->role_name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $user->is_active ? 'Active' : 'Inactive'; ?></td>
                <td>
                    <a href="<?php echo base_url('admin/users/edit?id=' . (int)$user->id); ?>">Edit</a>
                    <form action="<?php echo base_url('admin/users'); ?>" method="post" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user->id; ?>">
                        <input type="hidden" name="action" value="toggle_status">
                        <button type="submit" class="btn small">
                            <?php echo $user->is_active ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No users found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
