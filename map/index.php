<html>
    <head>
        <title>Geo Data Analysis</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <link href='custom.css' rel='stylesheet' type='text/css'>
    </head>
    <body>

        <div class="container">
            <div class="form-group">
                <label for="sel1">Select list:</label>
                <select class="form-control" id="sel1">
                    <option>All</option>
                    <?php
                        set_include_path("../");
                        require_once 'include/DB_Functions.php';
                        $db = new DB_Functions();
                        $query = "SELECT DISTINCT DATE_Format(`time`,'%d %m %y') AS date FROM `geo`";
                        $result = mysqli_query($db->con, $query);
                        while ($row = mysqli_fetch_assoc($result)){
                            echo "<option>".$row['date']."</option>";
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

        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZylJ0IC9kMVmf33mi-IlZDO9VzwzFAPQ"></script>
        <script src="custom.js"></script>
    </body>
</html>
