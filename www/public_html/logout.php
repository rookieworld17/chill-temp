<?php
// Überprüft, ob eine Session bereits gestartet wurde. Wenn nicht, wird eine neue Session gestartet.
// Dies ist notwendig, um auf die Session-Variablen zugreifen und sie zerstören zu können.
if (session_status() === PHP_SESSION_NONE) session_start();

// Löscht alle Session-Variablen, indem das $_SESSION-Array geleert wird.
$_SESSION = [];

// Löscht das Session-Cookie, indem ein Cookie mit demselben Namen, aber einem abgelaufenen Zeitstempel gesetzt wird.
// Dies sorgt dafür, dass der Browser das Cookie entfernt.
setcookie(session_name(), '', time()-3600, '/');

// Zerstört die Session auf dem Server. Alle mit der Session verbundenen Daten werden gelöscht.
session_destroy();

// Leitet den Benutzer zur Login-Seite (index.php) weiter.
header('Location: /index.php');

// Beendet die Ausführung des Skripts, um sicherzustellen, dass nach der Weiterleitung kein weiterer Code ausgeführt wird.
exit;