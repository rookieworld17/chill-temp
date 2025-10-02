<head>
    <meta charset="utf-8">
    <title>Chill Guys</title>

    <!-- Lädt das Bootstrap-CSS-Framework von einem CDN (Content Delivery Network) für ein responsives Design. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Lädt ein seiten-spezifisches Stylesheet. Die Variable $stylesheet wird in der aufrufenden PHP-Datei (z.B. index.php) definiert. -->
    <!-- Der Null-Koaleszenz-Operator (??) stellt sicher, dass '#' als Fallback verwendet wird, wenn $stylesheet nicht gesetzt ist. -->
    <link href="<?= $stylesheet ?? '#' ?>" rel="stylesheet">
</head>