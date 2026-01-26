<div>
<h2>Exams</h2>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($createMode) || !empty($editExam)): ?>
    <h3><?php echo !empty($editExam) ? 'Edit exam' : 'Create exam'; ?></h3>
    <form action="<?php echo !empty($editExam) ? base_url('admin/exams/edit?id=' . (int)$editExam->id) : base_url('admin/exams/create'); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="exam-title">Title</label>
            <input id="exam-title" type="text" name="title" value="<?php
                if (!empty($editExam)) {
                    echo htmlspecialchars($editExam->title, ENT_QUOTES, 'UTF-8');
                } else {
                    echo htmlspecialchars($titleValue ?? '', ENT_QUOTES, 'UTF-8');
                }
            ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="exam-description">Description</label>
            <textarea id="exam-description" name="description" rows="3" data-required="true"><?php
                if (!empty($editExam)) {
                    echo htmlspecialchars($editExam->description, ENT_QUOTES, 'UTF-8');
                } else {
                    echo htmlspecialchars($descriptionValue ?? '', ENT_QUOTES, 'UTF-8');
                }
            ?></textarea>
        </div>
        <div class="form-group">
            <label for="exam-duration">Duration (minutes)</label>
            <input id="exam-duration" type="number" name="duration" value="<?php
                if (!empty($editExam)) {
                    echo (int)$editExam->duration_minutes;
                } else {
                    echo (int)($durationValue ?? 0);
                }
            ?>" data-required="true">
        </div>
        <div class="form-group">
            <label for="exam-marks">Total marks</label>
            <input id="exam-marks" type="number" name="total_marks" value="<?php
                if (!empty($editExam)) {
                    echo (int)$editExam->total_marks;
                } else {
                    echo (int)($totalMarksValue ?? 0);
                }
            ?>" data-required="true">
        </div>
        <div class="form-group checkbox">
            <label>
                <input type="checkbox" name="is_active" value="1" <?php
                    if (!empty($editExam) && $editExam->is_active) {
                        echo 'checked';
                    } elseif (empty($editExam) && !empty($_POST['is_active'])) {
                        echo 'checked';
                    }
                ?>>
                Active
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary"><?php echo !empty($editExam) ? 'Update' : 'Create'; ?></button>
            <a href="<?php echo base_url('admin/exams'); ?>" class="btn secondary">Cancel</a>
        </div>
    </form>
<?php endif; ?>

<?php if (!empty($questionsExam)): ?>
    <h3>Questions for "<?php echo htmlspecialchars($questionsExam->title, ENT_QUOTES, 'UTF-8'); ?>"</h3>
    <form action="<?php echo base_url('admin/exams/questions?exam_id=' . (int)$questionsExam->id); ?>" method="post" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="question-text">Question</label>
            <textarea id="question-text" name="question_text" rows="3" data-required="true"></textarea>
        </div>
        <div class="form-group">
            <label for="question-marks">Marks</label>
            <input id="question-marks" type="number" name="marks" data-required="true">
        </div>
        <div class="form-group">
            <label>Options</label>
            <div class="option-row">
                <input type="radio" name="correct_option" value="0">
                <input type="text" name="option_1" placeholder="Option 1" data-required="true">
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="1">
                <input type="text" name="option_2" placeholder="Option 2" data-required="true">
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="2">
                <input type="text" name="option_3" placeholder="Option 3" data-required="true">
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="3">
                <input type="text" name="option_4" placeholder="Option 4" data-required="true">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn primary">Add question</button>
            <a href="<?php echo base_url('admin/exams'); ?>" class="btn secondary">Back to exams</a>
        </div>
    </form>

    <h4>Existing questions</h4>
    <?php if (!empty($questionsData)): ?>
        <ol>
            <?php foreach ($questionsData as $item): ?>
                <li>
                    <strong><?php echo htmlspecialchars($item['question']->question_text, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <ul>
                        <?php foreach ($item['options'] as $option): ?>
                            <li>
                                <?php echo htmlspecialchars($option->option_text, ENT_QUOTES, 'UTF-8'); ?>
                                <?php if ($option->is_correct): ?>
                                    <span class="badge">Correct</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>No questions added yet.</p>
    <?php endif; ?>
<?php endif; ?>

<h3>All exams</h3>
<p>
    <a href="<?php echo base_url('admin/exams/create'); ?>" class="btn primary">Create new exam</a>
</p>
<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Duration</th>
        <th>Total marks</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($exams)): ?>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?php echo (int)$exam->id; ?></td>
                <td><?php echo htmlspecialchars($exam->title, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo (int)$exam->duration_minutes; ?> min</td>
                <td><?php echo (int)$exam->total_marks; ?></td>
                <td><?php echo $exam->is_active ? 'Active' : 'Inactive'; ?></td>
                <td>
                    <a href="<?php echo base_url('admin/exams/edit?id=' . (int)$exam->id); ?>">Edit</a>
                    <a href="<?php echo base_url('admin/exams/questions?exam_id=' . (int)$exam->id); ?>">Questions</a>
                    <button class="btn small primary" onclick="API.getExamDetails(<?php echo (int)$exam->id; ?>)">Details (AJAX)</button>
                    <button class="btn small danger" onclick="API.deleteExam(<?php echo (int)$exam->id; ?>, this.closest('tr'))">Delete (AJAX)</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No exams found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
    </div>
