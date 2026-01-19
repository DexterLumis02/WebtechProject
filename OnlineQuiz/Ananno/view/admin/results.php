<h2>Results</h2>

<form action="<?php echo base_url('admin/results'); ?>" method="get" class="filter-form">
    <div class="form-group">
        <label for="filter-exam">Exam</label>
        <select id="filter-exam" name="exam_id">
            <option value="">All</option>
            <?php foreach ($exams as $exam): ?>
                <option value="<?php echo (int)$exam->id; ?>" <?php echo $selectedExamId === $exam->id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($exam->title, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="filter-user">User</label>
        <select id="filter-user" name="user_id">
            <option value="">All</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo (int)$user->id; ?>" <?php echo $selectedUserId === $user->id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn primary">Filter</button>
    </div>
</form>

<table class="table">
    <thead>
    <tr>
        <th>User</th>
        <th>Exam</th>
        <th>Score</th>
        <th>Correct</th>
        <th>Wrong</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['exam_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo (int)$row['score']; ?> / <?php echo (int)$row['total_marks']; ?></td>
                <td><?php echo (int)$row['correct_answers']; ?></td>
                <td><?php echo (int)$row['wrong_answers']; ?></td>
                <td><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No results found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

