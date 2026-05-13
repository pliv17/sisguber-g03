<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Error', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1 text-muted">
                <?= str_contains($title ?? '', '404') ? '404' : '500' ?>
            </h1>
            <h2 class="h4 mb-3"><?= htmlspecialchars($title ?? 'Error', ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-muted"><?= htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <a href="/" class="btn btn-primary mt-3">Volver al inicio</a>
        </div>
    </div>
</div>
</body>
</html>
