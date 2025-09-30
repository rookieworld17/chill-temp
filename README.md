# 🌐 Chill-Temp

## 🔎 Live Demo
Du kannst die Entwicklung der Website **aus Sicht eines Benutzers live verfolgen**:  
👉 [https://chill-temp.xyz/](https://chill-temp.xyz/)

Der Stand der Live-Seite entspricht immer dem aktuellen Status dieses Repositories.

---

## 📦 Enthaltene Komponenten
- **PHP 8.2** mit Apache
- **PDO MySQL**
- **MariaDB**
- Konfiguration über `.env`
- **Docker Compose** für einfachen Start

---

## 📥 Lokale Installation (Development)

1. Repository klonen:
```bash
  git clone https://github.com/rookieworld17/chill-temp.git
```

2. In das Projektverzeichnis wechseln:
```bash
  cd chill-temp
```
3. Datei `.env-dev-example` in `.env` umbenennen und mit den Dev-Daten füllen.
4. Projekt starten:
```bash
  docker compose up --build
```
Das Projekt läuft dann lokal z. B. unter http://localhost:8080.

## 🚀 Live Umgebung (Production)
1. In den Ordner `public_html` wechseln. 
2. Datei `.htaccess-prod-example` in `.htaccess` umbenennen. 
3. Mit den Live-Umgebungsdaten füllen. 
4. Der Server übernimmt danach automatisch die richtige Konfiguration.

## 🔄 Deployment Workflow
- Änderungen auf `main` → werden für die Live-Seite übernommen.
- Änderungen auf `dev` → können für lokale Tests und Staging genutzt werden.

## 📌 Hinweis
- Sensible Daten (Passwörter, Keys, Zugangsdaten) niemals direkt committen.
- Sie gehören in `.env` oder `.htaccess` (je nach Umgebung).
- Die Dateien sind in `.gitignore` eingetragen und werden **nicht ins Repo hochgeladen**.