<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$NAME = $Mobile = $Email = $Age = $GENDER = $Plan_Nmae = $Amount = $Duration = "";
$NAME_err = $Mobile_err = $Email_err = $Age_err = $GENDER_err = $Plan_Nmae_err = $Amount_err = $Duration_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate NAME
    $input_NAME = trim($_POST["NAME"]);
    if(empty($input_NAME)){
        $NAME_err = "Please enter a NAME.";
    } elseif(!filter_var($input_NAME, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $NAME_err = "Please enter a valid NAME.";
    } else{
        $NAME = $input_NAME;
    }
    
    // Validate Mobile
    $input_Mobile = trim($_POST["Mobile"]);
    if(empty($input_Mobile)){
        $Mobile_err = "Please enter an Mobile.";     
    } else{
        $Mobile = $input_Mobile;
    }
	// Validate Email
    $input_Email = trim($_POST["Email"]);
    if(empty($input_Email)){
        $Email_err = "Please enter an Email.";     
    } else{
        $Email = $input_Email;
    }
	// Validate Age
    $input_Age = trim($_POST["Age"]);
    if(empty($input_Age)){
        $Age_err = "Please enter an Age.";     
    } else{
        $Age = $input_Age;
    }
	// Validate GENDER
    $input_GENDER = trim($_POST["GENDER"]);
    if(empty($input_GENDER)){
        $GENDER_err = "Please enter an GENDER.";     
    } else{
        $GENDER = $input_GENDER;
    }
	// Validate Plan_Nmae
    $input_Plan_Nmae = trim($_POST["Plan_Nmae"]);
    if(empty($input_Plan_Nmae)){
        $Plan_Nmae_err = "Please enter an Plan_Nmae.";     
    } else{
        $Plan_Nmae = $input_Plan_Nmae;
    }
    
    // Validate Amount
    $input_Amount = trim($_POST["Amount"]);
    if(empty($input_Amount)){
        $Amount_err = "Please enter the Amount amount.";     
    } elseif(!ctype_digit($input_Amount)){
        $Amount_err = "Please enter a positive integer value.";
    } else{
        $Amount = $input_Amount;
    }
	// Validate Duration
    $input_Duration = trim($_POST["Duration"]);
    if(empty($input_Duration)){
        $Duration_err = "Please enter an Duration.";     
    } else{
        $Duration = $input_Duration;
    }
    
    // Check input errors before inserting in database
    if(empty($NAME_err) && empty($Mobile_err) && empty($Email_err) && empty($Age_err) && empty($GENDER_err) && empty($Plan_Nmae_err) && empty($Amount_err)&& empty($Duration_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO user (NAME, Mobile, Email, Age, GENDER, Plan_Nmae, Amount, Duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $param_NAME, $param_Mobile, $param_Email, $param_Age, $param_GENDER, $param_Plan_Nmae, $param_Amount, $param_Duration);
            
            // Set parameters
            $param_NAME = $NAME;
            $param_Mobile = $Mobile;
            $param_Email = $Email;
			$param_Age = $Age;
            $param_GENDER = $GENDER;
			$param_Plan_Nmae = $Plan_Nmae;
            $param_Amount = $Amount;
			 $param_Duration = $Duration;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: registration.php");
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
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index-2.css">
	<link rel="stylesheet" href="regis.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="manubar-1">
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
           <center> <h2>USER REGISTRATION FOR GYM</h2></center>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label class="name-6">Name</label>
                            <input type="text" name="NAME" class="name-6-1 <?php echo (!empty($NAME_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $NAME; ?>">
                            <span class="invalid-feedback"><?php echo $NAME_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="mobaile-2">Salary</label>
                            <input type="text" name="Mobile" class="mobile-2-1 <?php echo (!empty($Mobile_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Mobile; ?>">
                            <span class="invalid-feedback"><?php echo $Mobile_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="gmail-2">Salary</label>
                            <input type="email" name="Email" class="gmail-2-1 <?php echo (!empty($Email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Email; ?>">
                            <span class="invalid-feedback"><?php echo $Email_err;?></span>
                        </div>
						<div class="form-group">
                            <label class="age-2">Salary</label>
                            <input type="text" name="Age" class="age-2-1 <?php echo (!empty($Age_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Age; ?>">
                            <span class="invalid-feedback"><?php echo $Age_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="gender-1">GENDER</label>
                             <input type="radio" id="Male" name="fav_language" value="Male" class="male-2 <?php echo (!empty($GENDER_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $GENDER; ?>">
							 <span class="invalid-feedback"><?php echo $GENDER_err;?></span>
							 
                           <label class="male-2-1" for="html">Male</label><br>
                         <input  type="radio" id="Female" name="fav_language" value="Female" class="female-2 <?php echo (!empty($GENDER_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $GENDER; ?>">
							<span class="invalid-feedback"><?php echo $GENDER_err;?></span>
							
                           <label class="female-2-1" for="css">Female</label><br>
                        </div>
						<div class="form-group">
                             <label class="plan-3">Plan Nmae</label><br>
                 <select class="select-1 <?php echo (!empty($Plan_Nmae_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Plan_Nmae; ?>">
                    <option value="Select Plan" >Select Plan</option>
                    <option value="Chest" >Chest</option>
                    <option value="Beck" >Beck</option>
                    <option value="Bicep and Tricep" >Bicep and Tricep</option>
                    <option value="Leg and Sholder" >Leg and Sholder</option>
                    <option value="Abbs and Cordio" >Abbs and Cordio</option>
                </select>
                            <span class="invalid-feedback"><?php echo $Plan_Nmae_err;?></span>
                        </div>
                        <div class="form-group">
                            <label class="amount-2">Amount(In Rs)</label>
                            <input type="text" name="Amount" class="amount-2-1 <?php echo (!empty($Amount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Amount; ?>">
                            <span class="invalid-feedback"><?php echo $Amount_err;?></span>
                        </div>
						<div class="form-group">
                             <label class="duration-3">Duration</label><br>
                <select class="select-2 <?php echo (!empty($Duration_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Duration; ?>">
                   <option value="Select Duration" >Select Duration</option>
                   <option value="2 Month" >2 Month</option>
                   <option value="4 Month" >4 Month</option>
                   <option value="6 Month" >6 Month</option>
                   <option value="8 Month" >8 Month</option>
                   <option value="1 Year" >1 Year</option>
               </select>
                            <span class="invalid-feedback"><?php echo $Duration_err;?></span>
                        </div>
                        <input class="submit-3" type="submit" class="btn btn-primary" value="Submit">
                       
                    </form>
          
    </div>
</body>
</html>