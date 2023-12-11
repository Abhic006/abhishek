<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <link rel="stylesheet" href="index-2.css">
</head>
<body>
  <div class="nav-2">
      <img src="image/muscle-fitness-logo-png-transparent.png">
      <a href="index-2.php">HOME</a>
      <a href="Plan_view.php">OUR PLANS</a>
      <a href="registration.php">REGISTRATION</a>
      <a href="equipment_view.php">EQUIPMENT</a>
      <a href="">LOGOUT</a>
    </div>
    <div class="plan">
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM equipmenr";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        
                                        echo "<th>EQUIPMENT_NAME</th>";
                                        echo "<th>PRICE</th>";
                                        echo "<th>NUMBER_OF_UNITS</th>";
										 echo "<th>date</th>";
                                     
                                        echo "<th>DISSCRIPTION</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        
                                        echo "<td>" . $row['EQUIPMENT_NAME'] . "</td>";
                                        echo "<td>" . $row['PRICE'] . "</td>";
                                        echo "<td>" . $row['NUMBER_OF_UNITS'] . "</td>";
										
                                        echo "<td>" . $row['date'] . "</td>";
										echo "<td>" . $row['DISSCRIPTION'] . "</td>";
                                        echo "<td>";
                                           
                                           echo '<a href="equipment_delete.php?equipment_id='. $row['equipment_id'] .'" style="text-decoration:none;"> <div class="delete-2" > <center><p>Delete</p></center></div></a>';
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
					<a href="equipment.php"style="text-decoration:none;"> <div class="add-3"><center><p>Add New Equipment</p></center></div></a>
           
    </div>
</body>
</html>