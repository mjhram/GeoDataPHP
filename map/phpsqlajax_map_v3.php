<!DOCTYPE html >
<head>
  <title>Geo Data Analysis</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
  <link href='custom.css' rel='stylesheet' type='text/css'>

  <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZylJ0IC9kMVmf33mi-IlZDO9VzwzFAPQ"></script>

  <script type="text/javascript">
    //<![CDATA[

    /*var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };*/

    function show($date) {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(33.3436491, 44.4343689),
        zoom: 11,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("phpsqlajax_genxml.php?date=".$date, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");
          var address = markers[i].getAttribute("address");
          var type = markers[i].getAttribute("type");
          var acc = markers[i].getAttribute("accuracy");
          if(acc == 0.0)
          {
            acc = 5;
          }
          var point = new google.maps.LatLng(
                  parseFloat(markers[i].getAttribute("lat")),
                  parseFloat(markers[i].getAttribute("lng")));
          var html = "<b>" + name + "</b> <br/>" + address;
          /*var icon = customIcons[type] || {};
           var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: icon.icon
          });
          bindInfoWindow(marker, map, infoWindow, html);*/
          var cityCircle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.0,
            map: map,
            center: point,
            radius: acc * 1
          });
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
              new ActiveXObject('Microsoft.XMLHTTP') :
              new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //]]>

  </script>

</head>

<body onload="load()">
<div class="container">
  <div class="form-group">
    <label for="sel1">Select list:</label>
    <select class="form-control" id="sel1">
      <option onclick=show()>All</option>
      <?php
                        set_include_path("../");
                        require_once 'include/DB_Functions.php';
                        $db = new DB_Functions();
                        $query = "SELECT DISTINCT DATE_Format(`time`,'%d-%m-%Y') AS date FROM `geo`";
                        $result = mysqli_query($db->con, $query);
      while ($row = mysqli_fetch_assoc($result)){
      $aDate = $row['date'];
      echo "<option onclick=show(".$aDate.")>".$aDate."</option>";
      }
      ?>
    </select>
  </div>

  <div class="row">
    <div class="col-lg-8 col-lg-offset-2">
    </div><!-- /.8 -->
  </div> <!-- /.row-->
</div> <!-- /.container-->

<div id="map"></div>

</body>

</html>