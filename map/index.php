<!DOCTYPE html >
<head>
  <title>Geo Data Analysis</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
  <link href='custom.css' rel='stylesheet' type='text/css'>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZylJ0IC9kMVmf33mi-IlZDO9VzwzFAPQ"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#select1').change(getDropdownOptions);
      $('#select2').change(getDropdownOptions2);
    });

    function getDropdownOptions() {
      var val = $(this).val();
      // fire a POST request to populate.php
      $.post('populate.php?sel=1', { value : val }, populateDropdown, 'html');
    }

    function populateDropdown(data) {
      if (data != 'error') {
        $('#select2').html(data);
      }
    }

    function getDropdownOptions2() {
      var val = $(this).val();
      var val1 = document.getElementById("select1").value;
      // fire a POST request to populate.php
      $.post('populate.php?sel=2', { value : val,  value1 : val1 }, populateDropdown2, 'html');
    }

    function populateDropdown2(data) {
      if (data != 'error') {
        $('#select3').html(data);
      }
    }
  </script>

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

    function load() {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(33.3436491, 44.4343689),
        zoom: 11,
        mapTypeId: 'roadmap'
      });

      // Change this depending on the name of your PHP file
      /*downloadUrl("phpsqlajax_genxml.php?date=".$date, function(data) {
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
      });*/
    }

    function show($date, $uid, $tripid) {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(33.3436491, 44.4343689),
        zoom: 11,
        mapTypeId: 'roadmap'
      });
      //var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      //alert("phpsqlajax_genxml.php?date="+$date);
      var filter = "date="+$date+"&uid="+$uid+"&tripid="+$tripid;
      downloadUrl("phpsqlajax_genxml.php?"+filter, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        var fix = xml.documentElement.getElementsByTagName("fix");
        var minfix = fix[0].getAttribute("min");
        var maxfix = fix[0].getAttribute("max");
        var minDate = new Date(minfix*1);
        var maxDate = new Date (maxfix * 1);
        document.getElementById("minDate").innerHTML = "<b>Start Time:</b>"+minDate.toTimeString();
        document.getElementById("maxDate").innerHTML = "<b>End Time:</b>"+maxDate.toTimeString();
        for (var i = 0; i < markers.length; i++) {
          //var name = markers[i].getAttribute("name");
          //var address = markers[i].getAttribute("address");
          //var type = markers[i].getAttribute("type");
          var acc = markers[i].getAttribute("accuracy");
          var spd = markers[i].getAttribute("speed");
          var bearing = markers[i].getAttribute("bearing");
          var rot1 = Math.round(bearing);
          if(acc>100) {
            continue;
          }
          var rad1 = acc;
          if(rad1 == 0.0)
          {
            rad1 = 5;
          }
          var sColor;
          if(spd <1) {//3.6km/h
              sColor = '#FF0000';//Red
          } else if(spd>=1 && spd<6) {//21km/h
            sColor = '#FF8000';//Orange
          } else if(spd>=6 && spd<12) {//43km/h
            sColor = '#00FFFF';//Cyan
          } else if(spd>=12 && spd<20) {//72km/h
            sColor = '#00FF00';//Green
          } else {

          }
          var point = new google.maps.LatLng(
                  parseFloat(markers[i].getAttribute("lat")),
                  parseFloat(markers[i].getAttribute("lng")));
          //var html = "<b>" + name + "</b> <br/>" + address;

          new google.maps.Marker({
            map: map,
            position: point,
            icon: {
              strokeColor: sColor,
              strokeOpacity: 0.8,
              strokeWeight: 1,
              fillColor: '#FF0000',
              fillOpacity: 0.0,
              path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
              scale: 2,
              rotation: rot1
            }
          });

          /*var cityCircle = new google.maps.Circle({
            strokeColor: sColor,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.0,
            map: map,
            center: point,
            radius: rad1 * 1
          });*/
        }
      });

    }

    /*function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }*/

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

    function myFunction() {
      var date = document.getElementById("select1").value;//date
      var uid = document.getElementById("select2").value;//userid
      var tripid = document.getElementById("select3").value;//trip
      show(date, uid, tripid);
    }

    //]]>

  </script>

</head>

<body onload="load()">
<div class="container">
  <div class="row">
    <div class="form-group">
      <div class="col-sm-2">
        <label for="select1" class="col-sm-2 control-label">Select Date:</label>
        <select  class="form-control" id="select1" name="select1">
          <option value="">All</option>
          <?php
                            set_include_path("../");
                            require_once 'include/DB_Functions.php';
                            $db = new DB_Functions();
                            $query = "SELECT DISTINCT DATE_Format(`time`,'%d-%m-%Y') AS date FROM `geo`";
                            $result = mysqli_query($db->con, $query);
          while ($row = mysqli_fetch_assoc($result)){
              $aDate = $row['date'];
              echo "<option value='".$aDate."'>".$aDate."</option>" ."\n";
          }
          ?>
        </select>
      </div>
      <div class="col-sm-2">
      <label for="select2" class="col-sm-2 control-label">Select User:</label>

          <select class="form-control" id="select2" name="select2">
          </select>
      </div>
      <div class="col-sm-2">
      <label for="select3" class="col-sm-2 control-label">Select Trip:</label>

        <select class="form-control" id="select3" onchange="myFunction()">
          <option selected="selected">--Select Trip--</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
      <button type="button" class="btn btn-default" onclick="myFunction()">Refresh</button>
  </div>
  <div class="row">
      <p id="minDate"></p>
  </div>
  <div class="row">
    <p id="maxDate"></p>
  </div>
  <div class="row">
    <div class="col-lg-8 col-lg-offset-2">
    </div><!-- /.8 -->
  </div> <!-- /.row-->
</div> <!-- /.container-->

<div id="map"></div>

</body>

</html>