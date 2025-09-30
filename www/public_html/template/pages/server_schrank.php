<?php
require_once __DIR__ . '/../../inc/auth.php';
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

    <div class="box_form_1">
    <img src="../../assets/uploads/server.png" alt="Meine Logo" class="logo_server">

    <span class="city_1">Kyiv</span>
    <span class="city_circel_1"></span>
    <img src="../../assets/uploads/pin.png" alt="Meine Logo" class="logo_location_1">

    <!--Das ist der erste sensor in erste schrank-->
    <div class="box_form_temp_1">

      <span class="design_1"></span>
      <span class="strasse_form_1">Straße</span>
      <b class="temp_1">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_1"></span>
      <img src="../../assets/uploads/sonne%20(2).png" alt="Weather Logo" class="logo_weather_sonne_1">

      <!--Das ist der zweite sensor in erste schrank-->
      <span class="design_2"></span>
      <span class="strasse_form_2">Straße</span>
      <b class="temp_2">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_2"></span>
      <img src="../../assets/uploads/warnung.png" alt="Weather Logo" class="logo_weather_sonne_2">

      <!--Das ist der 3. sensor in erste schrank-->
      <span class="design_3"></span>
      <span class="strasse_form_3">Straße</span>
      <b class="temp_3">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_3"></span>
      <img src="../../assets/uploads/wolken-und-sonne.png" alt="Weather Logo" class="logo_weather_sonne_3">
    </div>


    <button class="open_button_1" onclick="document.location='/template/pages/sensor_page_1.html'">Öffnen</button>

    </div>



    <div class="box_form_2">
    <img src="../../assets/uploads/server.png" alt="Meine Logo" class="logo_server">

    <span class="city_2">Baghdad</span>
    <span class="city_circel_2"></span>
    <img src="../../assets/uploads/pin.png" alt="Meine Logo" class="logo_location_2">

    <div class="box_form_temp_2">

      <span class="design_1"></span>
      <span class="strasse_form_1">Straße</span>
      <b class="temp_1">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_1"></span>
      <img src="../../assets/uploads/sonne%20(2).png" alt="Weather Logo" class="logo_weather_sonne_1">

      <!--Das ist der zweite sensor in erste schrank-->
      <span class="design_2"></span>
      <span class="strasse_form_2">Straße</span>
      <b class="temp_2">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_2"></span>
      <img src="../../assets/uploads/warnung.png" alt="Weather Logo" class="logo_weather_sonne_2">

      <!--Das ist der 3. sensor in erste schrank-->
      <span class="design_3"></span>
      <span class="strasse_form_3">Straße</span>
      <b class="temp_3">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_3"></span>
      <img src="../../assets/uploads/wolken-und-sonne.png" alt="Weather Logo" class="logo_weather_sonne_3">
      
    </div>

    <button class="open_button_2" onclick="document.location='/template/pages/sensor_page_2.html'">Öffnen</button>
    </div>





    <div class="box_form_3">
    <img src="../../assets/uploads/server.png" alt="Meine Logo" class="logo_server">

    <span class="city_3">Erfurt</span>
    <span class="city_circel_3"></span>
    <img src="../../assets/uploads/pin.png" alt="Meine Logo" class="logo_location_3">

    <div class="box_form_temp_3">
      <span class="design_1"></span>
      <span class="strasse_form_1">Straße</span>
      <b class="temp_1">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_1"></span>
      <img src="../../assets/uploads/sonne%20(2).png" alt="Weather Logo" class="logo_weather_sonne_1">

      <!--Das ist der zweite sensor in erste schrank-->
      <span class="design_2"></span>
      <span class="strasse_form_2">Straße</span>
      <b class="temp_2">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_2"></span>
      <img src="../../assets/uploads/warnung.png" alt="Weather Logo" class="logo_weather_sonne_2">

      <!--Das ist der 3. sensor in erste schrank-->
      <span class="design_3"></span>
      <span class="strasse_form_3">Straße</span>
      <b class="temp_3">zahl</b>

      <!--Hier, wenn ist hitze ändert den logo von weather das soll im backend machen -->
      <span class="background_logo_weather_3"></span>
      <img src="../../assets/uploads/wolken-und-sonne.png" alt="Weather Logo" class="logo_weather_sonne_3">
      
    </div>

    <button class="open_button_3" onclick="document.location='/template/pages/sensor_page_3.html'">Öffnen</button>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>