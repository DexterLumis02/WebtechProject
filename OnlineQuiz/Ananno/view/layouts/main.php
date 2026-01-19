<<?php
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Online Exam', ENT_QUOTES, 'UTF-8'); ?></title>
    <<link rel="stylesheet" href="<?php echo base_url('public/assets/css/style.css'); ?>">
</head>
<body>
<?php require BASE_PATH . '/view/layouts/header.php'; ?>
<main class="container">
    <?php
    $success = $_SESSION['flash']['success'] ?? null;
    $error = $_SESSION['flash']['error'] ?? null;
    if ($success) {
        echo '<div class="flash success">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['flash']['success']);
    }
    if ($error) {
        echo '<div class="flash error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['flash']['error']);
    }
    ?>
    <?php echo $content; ?>
</main>
<?php require BASE_PATH . '/view/layouts/footer.php'; ?>
<script src="<?php echo base_url('public/assets/js/app.js'); ?>"></script>
<script src="<?php echo base_url('public/assets/js/api.js'); ?>"></script>
<script>
    const BASE_URL = "<?php echo base_url(''); ?>";
</script>
</body>
</html>
