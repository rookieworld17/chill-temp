document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('loginBtn');
    if (btn) btn.addEventListener('click', loginUser);
});

async function loginUser() {
    const loginName = document.getElementById("exampleInputEmail1").value;
    const password = document.getElementById("exampleInputPassword1").value;

    const formData = new FormData();
    formData.append("loginName", loginName);
    formData.append("password", password);

    try {
        const response = await fetch('/api/login.php', {
            method: "POST",
            body: formData,
            credentials: 'same-origin'
        });

        const text = await response.text();

        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON from server', e);
            alert('Server error — invalid response. Check console/network and server logs.');
            return;
        }

        if (result.success) {
            alert("Erfolgreich angemeldet!");
            document.location.href = "/template/pages/server_schrank.php";
        } else {
            if (result.block >= 3) {
                alert("Konto gesperrt.");
            } else if (result.block === null || typeof result.block === 'undefined') {
                alert("Konto nicht definiert.");
            } else {
                alert(`Passwort ist nicht korrekt. Probe: ${result.block}`)
            }
        }
    } catch (err) {
        console.error(err);
        alert("Unbekannter Fehler");
    }
}
