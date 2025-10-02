/**
 * Fügt einen Event-Listener hinzu, der nach dem vollständigen Laden des DOMs ausgeführt wird.
 * Er sucht den Login-Button und weist ihm die Funktion 'loginUser' als Klick-Ereignis zu.
 */
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('loginBtn');
    if (btn) btn.addEventListener('click', loginUser);
});

/**
 * Asynchrone Funktion zur Abwicklung des Benutzer-Logins.
 * Sie sammelt die Formulardaten, sendet sie an den Server und verarbeitet die Antwort.
 */
async function loginUser() {
    // Liest den Anmeldenamen aus dem Eingabefeld.
    const loginName = document.getElementById("exampleInputEmail1").value;
    // Liest das Passwort aus dem Eingabefeld.
    const password = document.getElementById("exampleInputPassword1").value;

    // Erstellt ein FormData-Objekt, um die Anmeldedaten zu verpacken.
    const formData = new FormData();
    formData.append("loginName", loginName);
    formData.append("password", password);

    try {
        // Sendet eine asynchrone POST-Anfrage an die Login-API.
        const response = await fetch('/api/login.php', {
            method: "POST",
            body: formData,
            credentials: 'same-origin' // Wichtig, um Cookies (z.B. Session-ID) mitzusenden.
        });

        // Liest die Antwort des Servers als Text. Dies verhindert Fehler, falls die Antwort kein valides JSON ist.
        const text = await response.text();

        let result;
        try {
            // Versucht, den Text als JSON zu parsen.
            result = JSON.parse(text);
        } catch (e) {
            // Fängt Fehler ab, falls die Server-Antwort kein valides JSON ist.
            console.error('Invalid JSON from server', e);
            alert('Serverfehler – ungültige Antwort. Überprüfen Sie Konsole/Netzwerk und Server-Logs.');
            return;
        }

        // Überprüft, ob der Login laut Server-Antwort erfolgreich war.
        if (result.success) {
            alert("Erfolgreich angemeldet!");
            // Leitet den Benutzer zur Hauptseite weiter.
            document.location.href = "/template/pages/server_schrank.php";
        } else {
            // Behandelt fehlgeschlagene Anmeldeversuche.
            if (result.block >= 3) {
                alert("Konto gesperrt.");
            } else if (result.block === null || typeof result.block === 'undefined') {
                // Dieser Fall tritt ein, wenn der Benutzername nicht in der Datenbank gefunden wurde.
                alert("Benutzername oder Passwort ist nicht korrekt.");
            } else {
                // Informiert den Benutzer über den fehlgeschlagenen Versuch und die Anzahl der Versuche.
                alert(`Passwort ist nicht korrekt. Versuch: ${result.block}`)
            }
        }
    } catch (err) {
        // Fängt Netzwerkfehler oder andere Probleme mit der fetch-Anfrage ab.
        console.error(err);
        alert("Unbekannter Fehler");
    }
}
