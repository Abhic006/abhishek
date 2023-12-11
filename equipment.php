<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$EQUIPMENT_NAME = $PRICE = $NUMBER_OF_UNITS = $date = $DISSCRIPTION = "";
$EQUIPMENT_NAME_err = $PRICE_err = $NUMBER_OF_UNITS_err = $date_err = $DISSCRIPTION_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate EQUIPMENT_NAME
    $input_EQUIPMENT_NAME = trim($_POST["EQUIPMENT_NAME"]);
    if(empty($input_EQUIPMENT_NAME)){
        $EQUIPMENT_NAME_err = "Please enter a EQUIPMENT_NAME.";
    } elseif(!filter_var($input_EQUIPMENT_NAME, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $EQUIPMENT_NAME_err = "Please enter a valid EQUIPMENT_NAME.";
    } else{
        $EQUIPMENT_NAME = $input_EQUIPMENT_NAME;
    }
	
	// Validate PRICE
    $input_PRICE = trim($_POST["PRICE"]);
    if(empty($input_PRICE)){
        $PRICE_err = "Please enter the PRICE amount.";     
    } elseif(!ctype_digit($input_PRICE)){
        $PRICE_err = "Please enter a positive integer value.";
    } else{
        $PRICE = $input_PRICE;
    }
    
    // Validate NUMBER_OF_UNITS
    $input_NUMBER_OF_UNITS = trim($_POST["NUMBER_OF_UNITS"]);
    if(empty($input_NUMBER_OF_UNITS)){
        $NUMBER_OF_UNITS_err = "Please enter an NUMBER_OF_UNITS.";     
    } else{
        $NUMBER_OF_UNITS = $input_NUMBER_OF_UNITS;
    }
    
	// Validate date
    $input_date = trim($_POST["date"]);
    if(empty($input_date)){
        $date_err = "Please enter an date.";     
    } else{
        $date = $input_date;
    }
	
	// Validate DISSCRIPTION
    $input_DISSCRIPTION = trim($_POST["DISSCRIPTION"]);
    if(empty($input_DISSCRIPTION)){
        $DISSCRIPTION_err = "Please enter an DISSCRIPTION.";     
    } else{
        $DISSCRIPTION = $input_DISSCRIPTION;
    }
    
    
    // Check input errors before inserting in database
    if(empty($EQUIPMENT_NAME_err) && empty($PRICE_err) && empty($NUMBER_OF_UNITS_err) && empty($date_err) && empty($DISSCRIPTION_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO equipmenr (EQUIPMENT_NAME, PRICE, NUMBER_OF_UNITS, date, DISSCRIPTION) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_EQUIPMENT_NAME, $param_PRICE, $param_NUMBER_OF_UNITS, $param_date, $param_DISSCRIPTION);
            
            // Set parameters
            $param_EQUIPMENT_NAME = $EQUIPMENT_NAME;
            $param_PRICE = $PRICE;
            $param_NUMBER_OF_UNITS = $NUMBER_OF_UNITS;
			$param_date = $date;
            $param_DISSCRIPTION = $DISSCRIPTION;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: equipment.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html>
  <head>
    <title>Equipment Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index-2.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
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
      <div class="nav-3">

    </div>
    <div class="equipment">
        <h2>EQUIPMENT REGISTRATION</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label class="equipment-1">EQUIPMENT NAME</label>
                            <input type="text" name="EQUIPMENT_NAME" class="equipment-1-1 <?php echo (!empty($EQUIPMENT_NAME_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $EQUIPMENT_NAME; ?>">
                            <span class="invalid-feedback"><?php echo $EQUIPMENT_NAME_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="price-1">PRICE</label>
                            <input type="text" name="PRICE" class="price-1-1 <?php echo (!empty($PRICE_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $PRICE; ?>">
                            <span class="invalid-feedback"><?php echo $PRICE_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="unit-1">NUMBER OF UNITS</label>
                            <input type="text" name="NUMBER_OF_UNITS" class="unit-1-1 <?php echo (!empty($NUMBER_OF_UNITS_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $NUMBER_OF_UNITS; ?>">
                            <span class="invalid-feedback"><?php echo $NUMBER_OF_UNITS_err;?></span>
                        </div>
						 <div class="form-group">
                            <label class="date-1">DATE OF PURCHASE</label>
                            <input type="date" name="date" class="date-1-1 <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date; ?>">
                            <span class="invalid-feedback"><?php echo $date_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="dis-1">DISSCRIPTION</label>
                            <input type="text" name="DISSCRIPTION" class="dis-1-1 <?php echo (!empty($DISSCRIPTION_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DISSCRIPTION; ?>">
                            <span class="invalid-feedback"><?php echo $DISSCRIPTION_err;?></span>
                        </div>
                         <input class="add-2" type="submit" class="btn btn-primary" value="Add plan">
                    </form>
           
    </div>
</body>
</html>