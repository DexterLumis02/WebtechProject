<?php if (!empty($singleResult)): ?>
    <h2>Result for <?php echo htmlspecialchars($exam->title ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
    <p>Score: <?php echo (int)$singleResult->score; ?> / <?php echo (int)$singleResult->total_marks; ?></p>
    <p>Correct answers: <?php echo (int)$singleResult->correct_answers; ?></p>
    <p>Wrong answers: <?php echo (int)$singleResult->wrong_answers; ?></p>
    <p>Date: <?php echo htmlspecialchars($singleResult->created_at, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><a href="<?php echo base_url('user/results'); ?>" class="btn secondary">Back to results</a></p>
<?php else: ?>
    <h2>My results</h2>
    <table class="table">
        <thead>
        <tr>
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
                    <td><?php echo htmlspecialchars($row['exam_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo (int)$row['score']; ?> / <?php echo (int)$row['total_marks']; ?></td>
                    <td><?php echo (int)$row['correct_answers']; ?></td>
                    <td><?php echo (int)$row['wrong_answers']; ?></td>
                    <td><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No results yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

