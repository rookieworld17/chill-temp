<!doctype html>
<html lang="en">
  
  <?php
    $stylesheet = '/assets/css/styles.css';
    include __DIR__ . '/template/layout/head.php'; 
  ?>

  <body>

    <div class="box_form">
      <img src="assets/uploads/klima.png" alt="Meine Logo" class="logo">

    <label for="exampleInputEmail1" class="loginname">Loginname</label>
    <input type="email" class="loginname_feld" id="exampleInputEmail1" aria-describedby="emailHelp">
    <label class="loginname_shadow">Loginname</label> 

    <label for="exampleInputPassword1" class="passwort">Passwort</label>
    <input type="password" class="passwort_feld" id="exampleInputPassword1">
    <label class="passwort_shadow">Passwort</label> 

    <!-- Login-Button, der die JavaScript-Funktion 'loginUser' beim Klick aufruft -->
    <button class="login_button" onclick="loginUser()">Login</button>
    </div>

    <!-- Einbindung des JavaScript für die Login-Logik -->
    <script src="assets/js/login.js" defer></script>
    <!-- Einbindung des Bootstrap JavaScript-Bundles für zusätzliche Funktionalitäten -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>