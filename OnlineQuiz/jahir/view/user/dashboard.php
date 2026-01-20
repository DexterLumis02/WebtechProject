<h2>User dashboard</h2>

<h3>Available exams</h3>
<table class="table">
    <thead>
    <tr>
        <th>Title</th>
        <th>Duration</th>
        <th>Total marks</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($exams)): ?>
        <?php foreach ($exams as $exam): ?>
            <?php $status = $examStatuses[$exam->id] ?? ['completed' => false]; ?>
            <tr>
                <td><?php echo htmlspecialchars($exam->title, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo (int)$exam->duration_minutes; ?> min</td>
                <td><?php echo (int)$exam->total_marks; ?></td>
                <td><?php echo $status['completed'] ? 'Completed' : 'Not attempted'; ?></td>
                <td>
                    <?php if ($status['completed']): ?>
                        <span class="badge">Done</span>
                    <?php else: ?>
                        <a class="btn small" href="<?php echo base_url('user/exam/start?id=' . (int)$exam->id); ?>">Start</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No exams available.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<h3>Recent results</h3>
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

