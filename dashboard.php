<!DOCTYPE html>
<html lang="en">
<head>
<script>
    setInterval(function() {
        location.reload();
    }, 5000); 
</script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
</body>
</html>
<?php
$host="localhost";
$username="root";
$pass="";
$db="smart_home";
$con=mysqli_connect("localhost","root","","smart_home");
$select=mysqli_query($con,"select * from sensor_data");
if(mysqli_num_rows($select)>0){
    ?>
    <p><h1> <u>DHTT DASHBOARD</u></h1></p>
    <table border="1">
    <tr>
    <th>ID</th>
    <th>temperature (°C)</th>
    <th>Humidity (%)</th>
    <th>Timestamp</th>
</tr>
    <?php
    while($data=mysqli_fetch_array($select)){
        ?>
      <tr><td><?php echo $data["id"];?></td><td><?php echo $data["temperature"];?></td><td><?php echo $data["humidity"];?></td><td><?php echo $data["created_at"];?></td></tr>

      <?php

    }
    ?>
    </table>
    <?php

}