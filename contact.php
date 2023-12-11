<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $gmail = $address = $Phone = "";
$name_err = $gmail_err = $address_err = $Phone_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate gmail
    $input_gmail = trim($_POST["gmail"]);
    if(empty($input_gmail)){
        $gmail_err = "Please enter an gmail.";     
    } else{
        $gmail = $input_gmail;
    }
    
     // Validate address
    $input_address = trim($_POST["address"]);
    if(empty($input_address)){
        $address_err = "Please enter an address.";     
    } else{
        $address = $input_address;
    }
	 // Validate Phone
    $input_Phone = trim($_POST["Phone"]);
    if(empty($input_Phone)){
        $Phone_err = "Please enter an Phone.";     
    } else{
        $Phone = $input_Phone;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($gmail_err) && empty($address_err) && empty($Phone_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO feedback (name, gmail, address, Phone) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_gmail, $param_address, $param_Phone);
            
            // Set parameters
            $param_name = $name;
            $param_gmail = $gmail;
            $param_address = $address;
			$param_Phone = $Phone;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: contact.php");
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
    <title>Contact</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="yoga.css">
	
    
   
    <link rel="shortcut icon"  href="image/pexels-mikhail-nilov-6932883.jpg">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <!--header start-->
    <div class="container">
      <div class="start">
        <img src="image/muscle-fitness-logo-png-transparent.png">
        <div class="home">
          <a href="index.php">Home</a>
        </div>
        <div class="about-us">
          <a href="">About Us</a>
        </div>
        <div class="gallery-home">
          <a href="gallery.php">Gallery</a>
        </div>
        <div class="contsct-1">
          <a href="contact.php">Contact</a>
        </div>
        <div class="login-1">
          <a href="login.php">Admin</a>
        </div>
        <div class="regis">
          <a href="registration.php">Registration</a>
        </div>
      </div>
      </div>
    <div class="container-1">
        <div class="img">
            <h1>CONTACT</h1>
            <hr></hr>
        </div>
    </div>
<div class="navbar-1">
  <div class="nav-1">
    <div class="location-3">
      <img src="image/address-icon-clipart-gold-2.png">
      <h2>Address</h2>
      <p>Freeganj Maksiroad,Ujjain (Madhya Pradesh)</p>
    </div>
    <div class="time">
      <img src="image/clock-icon.png">
      <h2>Opening Time</h2>
      <p>8.00-22.00 Mon-Sat</p>
    </div>
    <div class="phone-6">
      <img src="image/phone_PNG48922.png">
      <h2>Phone</h2>
      <p>7400694364</p>
    </div>
    <div class="mail">
      <img src="image/email-icon.png">
      <h2>Gmail</h2>
      <p>priyamchourasiya2000@gmail.com</p>
    </div>
  </div>
  <div class="nav-2">
    <div class="feedback">
    <center><h2>FEEDBACK FORM</h2>
    <p>We are sure that this decision will affect your life in a positive way. So why waiting?</p></center>
  </div>
  <div class="form-3">
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                       
                            
                            <input  type="text" name="name" placeholder="Your Name" class="name-3 <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        
                        
                       
                            
                            <input  type="gmail" name="gmail" placeholder="gmail" class="mail-3 <?php echo (!empty($gmail_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $gmail; ?>">
                            <span class="invalid-feedback"><?php echo $gmail_err;?></span>
                       
                            
                            <textarea name="address" style="padding-top:11px;" class="massage-3 <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo $address; ?></textarea>
                            <span class="invalid-feedback"><?php echo $address_err;?></span>
                       
                            
                            <input  type="text" name="Phone" placeholder="Your phone" class="phone-4 <?php echo (!empty($Phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Phone; ?>">
                            <span class="invalid-feedback"><?php echo $Phone_err;?></span>
                       
                         <button class="button">sumite</button>
</div>
</div>
</div>
     <!--footer start-->
  <div class="footer">
    <div class="about">
      <h2>ABOUT US</h2>
      <hr></hr>
      <p>Our gym is a perfect place for everyone to enjoy sports, bodybuilding, and fitness. With us youcan reach your top physical condition!</p>
    </div>
      <div class="open">
        <h2>OPENING HOURS</h2>
        <hr></hr>
        
        <h3>MONDAY :</h3></br>
          <h3>TUSDAY :</h3></br>
            <h3>WEDNASDAY :</h3></br>
              <h3>THUSDAY :</h3></br>
                <h3>FRIDAY :</h3></br>
                  <h3>SATERDAY :</h3></br>
                  <h3>SUNDAY :</h3>
        <b>Close</b></br>   
        <p>8:00 to 10:00</p></br>  
        <p>8:00 to 10:00</p></br>  
        <p>8:00 to 10:00</p></br>  
        <p>8:00 to 10:00</p></br>  
        <p>8:00 to 10:00</p></br>  
        <p>8:00 to 10:00</p></br>         
      </div>
      <div class="contact">
        <h2>CONTACT INFO</h2>
        <hr></hr>
      <div class="location">
        <div class="icone">
          <img src="image/location-icon-white-png-36.png">
        </div>
        <b>Freeganj Maksiroad,Ujjain (Madhya Pradesh)</b>
      </div>
        <div class="phone">
          <div class="icone-1">
            <img src="image/img_157010.png">
          </div>
         <a href="#" style="color: white;"> <b>+91 7400694364</b>
          <b>+91 6261932320</b>
          <b>+91 6264110322</b></a>
        </div>
      </div>
    </div>
  </div>

  <div class="start-1">
    <img src="image/clipart-exercise-fitness-centre-19.png">
    <div class="home-3">
      <a href="index.php" class="home-4" >Home</a>
      
    
      <a href=""class="about-3">About Us</a>
      
    
      <a href="gallery.php"class="gallery-3">Gallery</a>
    
   
      <a href="contact.php"class="contact-3">Contact</a>
    
    
      <a href="login.php"class="login-3">Admin</a>
    
    
      <a href="registration.php"class="rgis-3">Registration</a>
    
  </div>
</div>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://code.jquery.com/jquery.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
    </body>
  </html>