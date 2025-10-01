<?php
require_once dirname(__DIR__, 2) . '/inc/auth.php';
require_once dirname(__DIR__, 3) . '/config/bootstrap.php';

function e($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$serverschrankId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM serverschrank WHERE serverschrankId = ?");
$stmt->execute([$serverschrankId]);
$schrank = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT * FROM sensor WHERE serverschrankId = ?");
$stmt2->execute([$serverschrankId]);
$sensors = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$sensorsByRack = [];
$sensorIds = [];
foreach ($sensors as $sensor) {
    $sensorsByRack[$sensor['serverschrankId']][] = $sensor;
    $sensorIds[] = (int)$sensor['sensorId'];
}
$sensorIds = array_values(array_unique($sensorIds));

$lastTemps = [];
if (!empty($sensorIds)) {
    $placeholders = implode(',', array_fill(0, count($sensorIds), '?'));
    $sql = "
        SELECT t.sensorId, t.temperatur, t.zeit
        FROM temperaturmessung t
        JOIN (
            SELECT sensorId, MAX(zeit) AS mz
            FROM temperaturmessung
            WHERE sensorId IN ($placeholders)
            GROUP BY sensorId
        ) m ON t.sensorId = m.sensorId AND t.zeit = m.mz
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($sensorIds);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $lastTemps[(int)$r['sensorId']] = $r['temperatur'];
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Chill Guys</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="/../../assets/css/styles_sensor_page_1.css" rel="stylesheet">

    <img src="/../../assets/uploads/klima.png" alt="Meine Logo" class="logo">

    <span class="herstelle">Herstelle</span>
    <img src="../../assets/uploads/hersteller.png" alt="Meine Logo" class="logo_herstelle">

    <span class="ort"><?= e($schrank['standort']) ?></span>
    <img src="../../assets/uploads/pin.png" alt="Meine Logo" class="logo_ort">

  </head>
  <body>

    <?php foreach ($sensors as $idx => $sensor):
        $sensorId = (int)$sensor['sensorId'];
        $lastTemp = array_key_exists($sensorId, $lastTemps) ? $lastTemps[$sensorId] : null;
        $displayTemp = $lastTemp !== null ? (float)$lastTemp : (isset($sensor['maxTemp']) ? (float)$sensor['maxTemp'] : null);

        $warnungClass = "warnung_keine_hitze";
        $warnungMeldung = "Keine hitze";
        if ($displayTemp >= 20 && $displayTemp < $sensor['maxTemp']) {
            $icon = "sonne%20(2).png";
        } elseif ($displayTemp <= 19) {
            $icon = "wolken-und-sonne.png";
        } elseif ($displayTemp >= $sensor['maxTemp']) {
            $icon = "warnung.png";
            $warnungClass = "warnung_hitze";
            $warnungMeldung = "Warnung hitze";
        }
    ?>
        <div class="erste_sensor">
            <img src="../../assets/uploads/technologie.png" alt="Meine Logo" class="logo_sensor">

            <span class="strasse_name_kyiv_1"><?= e($sensor['adresse']) ?></span>

            <div class="temp_form">

              <span class="background_logo_weather"></span>
              <img src="../../assets/uploads/<?php echo $icon?>" alt="Weather Logo" class="weather_logo">

              <!--Hier zeigt den aktuelle Temp-->
                <span class="temp_aktuelle_kyiv_1" data-sensor-id="<?= e($sensor['sensorId']) ?>"><?php echo $displayTemp !== null ? $displayTemp . '°C' : '—'; ?></span>


                <b class="max_text">MAX:</b>
              <span class="max_temp_kyiv_1"><?= e($sensor['maxTemp']) ?>°C</span>
            </div>

            <!--Hier kommt drauf auf temp im backend das machen und hiere soll geändert sein-->
            <span class="<?= e($warnungClass) ?>"><?= e($warnungMeldung) ?></span>
            <img src="../../assets/uploads/ok.png" alt="Weather Logo" class="keine_hitze_logo">
        </div>
    <?php endforeach; ?>

    <button class="back_button" onclick="document.location='/template/pages/server_schrank.php'">Zurück</button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>