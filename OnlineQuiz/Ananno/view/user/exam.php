<div>
    <h2><?php echo htmlspecialchars($exam->title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><?php echo htmlspecialchars($exam->description, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Duration: <?php echo (int)$exam->duration_minutes; ?> minutes</p>

    <div id="exam-timer" data-remaining="<?php echo (int)$remainingSeconds; ?>">
        Time left: <span id="timer-display"></span>
    </div>

    <form id="exam-form" action="<?php echo base_url('user/exam/submit'); ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="exam_id" value="<?php echo (int)$exam->id; ?>">
        <input type="hidden" name="attempt_id" value="<?php echo (int)$attempt->id; ?>">

        <ol class="question-list">
        <?php foreach ($questionsWithOptions as $item): ?>
            <?php $q = $item['question']; ?>
            <li>
                <p><?php echo htmlspecialchars($q->question_text, ENT_QUOTES, 'UTF-8'); ?> (<?php echo (int)$q->marks; ?>)</p>
                <?php foreach ($item['options'] as $option): ?>
                    <label class="option-label">
                        <input type="radio" name="answers[<?php echo (int)$q->id; ?>]" value="<?php echo (int)$option->id; ?>">
                        <?php echo htmlspecialchars($option->option_text, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                <?php endforeach; ?>
            </li>
        <?php endforeach; ?>
        </ol>

        <div class="form-actions">
            <button type="submit" class="btn primary">Submit exam</button>
        </div>
    </form>
</div>

