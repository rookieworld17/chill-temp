<?php
require_once dirname(__DIR__, 2) . '/inc/auth.php';
require_once dirname(__DIR__, 3) . '/config/bootstrap.php';

function e($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$stmt = $pdo->prepare("SELECT * FROM serverschrank ORDER BY serverschrankId ASC");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sensorStmt = $pdo->prepare("SELECT * FROM sensor ORDER BY serverschrankId ASC, sensorId ASC");
$sensorStmt->execute();
$sensors = $sensorStmt->fetchAll(PDO::FETCH_ASSOC);



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
    <link href="/../../assets/css/styles_schraenke.css" rel="stylesheet">

    <img src="/../../assets/uploads/klima.png" alt="Meine Logo" class="logo">

  </head>
  <body>

      <?php foreach ($rows as $idx => $row):
          $n = $idx + 1;
          $rackSensors = $sensorsByRack[$row['serverschrankId']] ?? [];
      ?>

      <div class="box_form_<?php echo $n; ?>">
          <img src="../../assets/uploads/server.png" alt="Logo" class="logo_server">

          <span class="city_<?php echo $n; ?>"><?php echo e($row['standort']); ?></span>
          <span class="city_circel_<?php echo $n; ?>"></span>
          <img src="../../assets/uploads/pin.png" alt="Pin" class="logo_location_<?php echo $n; ?>">

          <div class="box_form_temp_<?php echo $n; ?>">
              <?php foreach ($rackSensors as $sensorIdx => $sensor):
                  $sensorId = (int)$sensor['sensorId'];
                  $lastTemp = array_key_exists($sensorId, $lastTemps) ? $lastTemps[$sensorId] : null;
                  $displayTemp = $lastTemp !== null ? (float)$lastTemp : (isset($sensor['maxTemp']) ? (float)$sensor['maxTemp'] : null);

                  if ($displayTemp >= 20 && $displayTemp < $sensor['maxTemp']) {
                      $icon = "sonne%20(2).png";
                  } elseif ($displayTemp <= 19) {
                      $icon = "wolken-und-sonne.png";
                  } elseif ($displayTemp >= $sensor['maxTemp']) {
                      $icon = "warnung.png";
                  }
              ?>
                  <span class="design_<?php echo $sensorIdx+1; ?>"></span>
                  <span class="strasse_form_<?php echo $sensorIdx+1; ?>"><?php echo e($sensor['adresse']); ?></span>
                  <b class="temp_<?php echo $sensorIdx+1; ?>"><?php echo $displayTemp !== null ? $displayTemp . '°C' : '—'; ?></b>

                  <span class="background_logo_weather_<?php echo $sensorIdx+1; ?>"></span>
                  <img src="../../assets/uploads/<?php echo $icon; ?>" alt="Weather Logo" class="logo_weather_sonne_<?php echo $sensorIdx+1; ?>">
              <?php endforeach; ?>
          </div>

          <button class="open_button_<?php echo $n; ?>" onclick="document.location='/template/pages/sensor_page.php?id=<?php echo e($row['serverschrankId']); ?>'">Öffnen</button>
      </div>

      <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
