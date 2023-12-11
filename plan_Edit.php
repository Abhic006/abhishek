<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$plan_name = $amount = $duration = "";
$plan_name_err = $amount_err = $duration_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["plan_id"]) && !empty($_POST["plan_id"])){
    // Get hidden input value
    $plan_id = $_POST["plan_id"];
    
    // Validate plan_name
    $input_plan_name = trim($_POST["plan_name"]);
    if(empty($input_plan_name)){
        $plan_name_err = "Please enter a plan_name.";
    } elseif(!filter_var($input_plan_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $plan_name_err = "Please enter a valid plan_name.";
    } else{
        $plan_name = $input_plan_name;
    }
    
    
    
    // Validate amount
    $input_amount = trim($_POST["amount"]);
    if(empty($input_amount)){
        $amount_err = "Please enter the amount amount.";     
    } elseif(!ctype_digit($input_amount)){
        $amount_err = "Please enter a positive integer value.";
    } else{
        $amount = $input_amount;
    }
	
	// Validate duration
    $input_duration = trim($_POST["duration"]);
    if(empty($input_duration)){
        $duration_err = "Please enter an duration.";     
    } else{
        $duration = $input_duration;
    }
    
    // Check input errors before inserting in database
    if(empty($plan_name_err) && empty($amount_err) && empty($duration_err)){
        // Prepare an update statement
        $sql = "UPDATE our_plan SET plan_name=?, amount=?, duration=? WHERE plan_id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_plan_name, $param_amount, $param_duration, $param_plan_id);
            
            // Set parameters
            $param_plan_name = $plan_name;
            $param_amount = $amount;
            $param_duration = $duration;
            $param_plan_id = $plan_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: plan_view.php");
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
} else{
    // Check existence of plan_id parameter before processing further
    if(isset($_GET["plan_id"]) && !empty(trim($_GET["plan_id"]))){
        // Get URL parameter
        $plan_id =  trim($_GET["plan_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM our_plan WHERE plan_id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_plan_id);
            
            // Set parameters
            $param_plan_id = $plan_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $plan_name = $row["plan_name"];
                    $amount = $row["amount"];
                    $duration = $row["duration"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
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
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label class="plan-1" style="padding-left:13px;">Plan Name</label>
                            <input type="text" name="plan_name" class="plan-1-1  <?php echo (!empty($plan_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $plan_name; ?>">
                            <span class="invalid-feedback"><?php echo $plan_name_err;?></span>
                        </div>
                       <div class="form-group">
                            <label class="amount-1">Amount</label>
                            <input type="text" name="amount" class="amount-1-1 <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $amount; ?>">
                            <span class="invalid-feedback"><?php echo $amount_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="duration-1">Duration</label>
                            <input type="text" name="duration" class="duration-1-1 <?php echo (!empty($duration_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $duration; ?>">
                            <span class="invalid-feedback"><?php echo $duration_err;?></span>
                        </div>
                        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>"/>
                         <input class="add-2" type="submit" class="btn btn-primary" value="Edit Plan Details">
                        
                    </form>
        
    </div>
</body>
</html>