<?php
require_once dirname(__DIR__, 2) . '/inc/auth.php';
require_once dirname(__DIR__, 3) . '/config/bootstrap.php';

/**
 * Eine Hilfsfunktion zum Escapen von HTML-Sonderzeichen, um Cross-Site-Scripting (XSS)-Angriffe zu verhindern.
 * @param string $s Der zu escapende String.
 * @return string Der escapete String.
 */
function e($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Holt die ID des Serverschranks aus der URL. Falls keine ID vorhanden ist, wird 0 verwendet.
$serverschrankId = (int)($_GET['id'] ?? 0);

// Bereitet eine SQL-Anweisung vor, um die Daten des spezifischen Serverschranks abzurufen.
$stmt = $pdo->prepare("SELECT * FROM serverschrank WHERE serverschrankId = ?");
$stmt->execute([$serverschrankId]);
// Holt das Ergebnis als assoziatives Array.
$schrank = $stmt->fetch(PDO::FETCH_ASSOC);

// Bereitet eine SQL-Anweisung vor, um alle Sensoren abzurufen, die zu diesem Serverschrank gehören.
$stmt2 = $pdo->prepare("SELECT * FROM sensor WHERE serverschrankId = ?");
$stmt2->execute([$serverschrankId]);
// Holt alle Ergebnisse als assoziatives Array.
$sensors = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Initialisiert Arrays, um Sensoren nach Schrank zu gruppieren und Sensor-IDs zu sammeln.
$sensorsByRack = [];
$sensorIds = [];
// Durchläuft alle gefundenen Sensoren.
foreach ($sensors as $sensor) {
    // Gruppiert Sensoren nach ihrer serverschrankId (obwohl hier nur eine ID relevant ist).
    $sensorsByRack[$sensor['serverschrankId']][] = $sensor;
    // Sammelt die ID jedes Sensors.
    $sensorIds[] = (int)$sensor['sensorId'];
}
// Entfernt doppelte Sensor-IDs, falls vorhanden.
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
    <link href="/../../assets/css/styles_sensor_page_1.css" rel="stylesheet">

    <img src="/../../assets/uploads/klima.png" alt="Meine Logo" class="logo">

    <span class="herstelle">Herstelle</span>
    <img src="../../assets/uploads/hersteller.png" alt="Meine Logo" class="logo_herstelle">

    <!-- Zeigt den Standort des Serverschranks an. -->
    <span class="ort"><?= e($schrank['standort']) ?></span>
    <img src="../../assets/uploads/pin.png" alt="Meine Logo" class="logo_ort">

  </head>
  <body>

    <?php 
    // Schleife durch jeden Sensor, um seine Informationen anzuzeigen.
    foreach ($sensors as $idx => $sensor):
        $sensorId = (int)$sensor['sensorId'];
        // Holt die letzte Temperatur für den aktuellen Sensor.
        $lastTemp = array_key_exists($sensorId, $lastTemps) ? $lastTemps[$sensorId] : null;
        // Bestimmt die anzuzeigende Temperatur: die letzte Messung oder die Maximaltemperatur als Fallback.
        $displayTemp = $lastTemp !== null ? (float)$lastTemp : (isset($sensor['maxTemp']) ? (float)$sensor['maxTemp'] : null);

        // Logik zur Bestimmung der Warnmeldung und des Icons basierend auf der Temperatur.
        if ($displayTemp >= 20 && $displayTemp < $sensor['maxTemp']) {
            $icon = "sonne%20(2).png"; // Normaltemperatur-Icon
        } elseif ($displayTemp <= 19) {
            $icon = "wolken-und-sonne.png"; // Niedrigtemperatur-Icon
        } elseif ($displayTemp >= $sensor['maxTemp']) {
            $icon = "warnung.png"; // Hitzewarnung-Icon
        }
    ?>
        <!-- Container für die Anzeige eines einzelnen Sensors. -->
        <div class="erste_sensor">
            <img src="../../assets/uploads/technologie.png" alt="Meine Logo" class="logo_sensor">

            <!-- Zeigt die Adresse/Position des Sensors an. -->
            <span class="strasse_name_kyiv_1"><?= e($sensor['adresse']) ?></span>

            <div class="temp_form">

              <span class="background_logo_weather"></span>
              <img src="../../assets/uploads/<?php echo $icon?>" alt="Weather Logo" class="weather_logo">

              <!-- Zeigt die aktuelle Temperatur an. Das data-sensor-id Attribut kann für JavaScript verwendet werden. -->
                <span class="temp_aktuelle_kyiv_1" data-sensor-id="<?= e($sensor['sensorId']) ?>"><?php echo $displayTemp !== null ? $displayTemp . '°C' : '—'; ?></span>


                <b class="max_text">MAX:</b>
              <!-- Zeigt die konfigurierte Maximaltemperatur für den Sensor an. -->
              <span class="max_temp_kyiv_1"><?= e($sensor['maxTemp']) ?>°C</span>
            </div>

            <!-- Hier kommt drauf auf temp im backend das machen und hiere soll geändert sein -->
            <span class="
            <?php echo $displayTemp >= $sensor['maxTemp']
                    ? 'warnung_hitze'
                    : 'warnung_keine_hitze';
            ?>">
                <?php echo $displayTemp >= $sensor['maxTemp']
                        ? 'Warnung hitze'
                        : 'Keine hitze';
                ?></span>
            <img src="../../assets/uploads/<?php echo $displayTemp >= $sensor['maxTemp'] ? "unterschrift" : 'ok';?>.png" alt="Weather Logo" class="keine_hitze_logo">
        </div>
    <?php endforeach; ?>

    <button class="back_button" onclick="document.location='/template/pages/server_schrank.php'">Zurück</button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>