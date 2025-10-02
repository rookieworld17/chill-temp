<?php
require_once dirname(__DIR__, 2) . '/inc/auth.php';
require_once dirname(__DIR__, 3) . '/config/bootstrap.php';

/**
 * Eine Hilfsfunktion zum Escapen von HTML-Sonderzeichen, um Cross-Site-Scripting (XSS)-Angriffe zu verhindern.
 * @param string $s Der zu escapende String.
 * @return string Der escapete String.
 */
function e($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Bereitet eine SQL-Anweisung vor, um alle Serverschränke abzurufen, sortiert nach ihrer ID.
$stmt = $pdo->prepare("SELECT * FROM serverschrank ORDER BY serverschrankId ASC");
$stmt->execute();
// Holt alle Ergebnisse als assoziatives Array.
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bereitet eine SQL-Anweisung vor, um alle Sensoren abzurufen, sortiert nach Schrank-ID und dann Sensor-ID.
$sensorStmt = $pdo->prepare("SELECT * FROM sensor ORDER BY serverschrankId ASC, sensorId ASC");
$sensorStmt->execute();
$sensors = $sensorStmt->fetchAll(PDO::FETCH_ASSOC);


// Initialisiert Arrays, um Sensoren nach Schrank zu gruppieren und alle Sensor-IDs zu sammeln.
$sensorsByRack = [];
$sensorIds = [];
// Durchläuft alle gefundenen Sensoren.
foreach ($sensors as $sensor) {
    // Gruppiert Sensoren nach ihrer serverschrankId.
    $sensorsByRack[$sensor['serverschrankId']][] = $sensor;
    // Sammelt die ID jedes Sensors.
    $sensorIds[] = (int)$sensor['sensorId'];
}
// Entfernt doppelte Sensor-IDs, falls vorhanden, und re-indiziert das Array.
$sensorIds = array_values(array_unique($sensorIds));

// Initialisiert ein Array für die letzten Temperaturmessungen.
$lastTemps = [];
// Führt die Abfrage nur aus, wenn Sensor-IDs vorhanden sind.
if (!empty($sensorIds)) {
    // Erstellt Platzhalter ('?, ?, ?') für die IN-Klausel der SQL-Abfrage.
    $placeholders = implode(',', array_fill(0, count($sensorIds), '?'));
    // SQL-Abfrage, um die jeweils letzte Temperaturmessung für jeden Sensor zu erhalten.
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
    // Bereitet die Abfrage vor.
    $stmt = $pdo->prepare($sql);
    // Führt die Abfrage mit den gesammelten Sensor-IDs aus.
    $stmt->execute($sensorIds);
    // Verarbeitet die Ergebnisse und speichert die letzte Temperatur für jeden Sensor.
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

      <?php 
      // Schleife durch jeden Serverschrank, um ihn auf der Seite darzustellen.
      foreach ($rows as $idx => $row):
          $n = $idx + 1; // Zählvariable für dynamische CSS-Klassen.
          // Holt die zu diesem Schrank gehörenden Sensoren aus dem gruppierten Array.
          $rackSensors = $sensorsByRack[$row['serverschrankId']] ?? [];
      ?>

      <div class="box_form_<?php echo $n; ?>">
          <img src="../../assets/uploads/server.png" alt="Logo" class="logo_server">

          <span class="city_<?php echo $n; ?>"><?php echo e($row['standort']); ?></span>
          <span class="city_circel_<?php echo $n; ?>"></span>
          <img src="../../assets/uploads/pin.png" alt="Pin" class="logo_location_<?php echo $n; ?>">

          <div class="box_form_temp_<?php echo $n; ?>">
              <?php 
              // Schleife durch die ersten drei Sensoren des aktuellen Schranks.
              foreach (array_slice($rackSensors, 0, 3) as $sensorIdx => $sensor):
                  $sensorId = (int)$sensor['sensorId'];
                  // Holt die letzte Temperatur für den aktuellen Sensor.
                  $lastTemp = array_key_exists($sensorId, $lastTemps) ? $lastTemps[$sensorId] : null;
                  // Bestimmt die anzuzeigende Temperatur: die letzte Messung oder die Maximaltemperatur als Fallback.
                  $displayTemp = $lastTemp !== null ? (float)$lastTemp : (isset($sensor['maxTemp']) ? (float)$sensor['maxTemp'] : null);

                  // Logik zur Bestimmung des Icons basierend auf der Temperatur.
                  if ($displayTemp >= 20 && $displayTemp < $sensor['maxTemp']) {
                      $icon = "sonne%20(2).png";
                  } elseif ($displayTemp <= 19) {
                      $icon = "wolken-und-sonne.png";
                  } elseif ($displayTemp >= $sensor['maxTemp']) {
                      $icon = "warnung.png";
                  } else {
                      $icon = "wolken-und-sonne.png";
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
