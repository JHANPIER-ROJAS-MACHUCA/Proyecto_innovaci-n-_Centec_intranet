<?php

use CrediSoporte\Domain\Models\Customer;

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

$customers = Customer::whereNotNull('coordinate_lat')
    ->whereNotNull('coordinate_lng')
    ->get();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div style="width: 100vw; height: 100vh;">
        <div id="map" style="width: 100%; height: 100%;"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
    <script>
        var map = L.map('map');

        var tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // filtramos los clientes validos
        var customers = (<?php echo $customers ?>).filter(item => {
            var lat = item.coordinate_lat;
            var lng = item.coordinate_lng;

            return lat && !isNaN(lat) && lng && !isNaN(lng)
        });

        // registramos el layer marker
        customers.forEach(customer => {
            customer.l_marker = L.marker([customer.coordinate_lat, customer.coordinate_lng]).addTo(map)
                .bindPopup(`
                ${customer.ap} ${customer.am} ${customer.nom} <br> 
                ${customer.direc} <br> 
                ${customer.comentario}
                `);
        });

        const query = new URLSearchParams(window.location.search);
        const customerId = query.get('customerId');

        if (customers.length) {
            var customer = customerId ? customers.find(item => item.idCG == customerId) : null;

            if (customer) {
                map.setView(customer.l_marker.getLatLng(), 15);
                customer.l_marker.openPopup();
            } else {
                map.setView(customers[0].l_marker.getLatLng(), 15);
                customers[0].l_marker.openPopup();
            }

        } else {
            map.setView([-11.250480, -74.637486], 15);
        }
    </script>
</body>

</html>