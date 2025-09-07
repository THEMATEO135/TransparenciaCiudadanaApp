<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($title) ? htmlspecialchars($title) : 'Mi App' ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
<?php if (isset($extra_css)): foreach($extra_css as $css): ?>
<link rel="stylesheet" href="/assets/css/<?= $css ?>">
<?php endforeach; endif; ?>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>


<main id="main-content">
<?= $content ?>
</main>


<script src="/assets/js/app.js"></script>
<?php if (isset($extra_js)): foreach($extra_js as $js): ?>
<script src="/assets/js/<?= $js ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>