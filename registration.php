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
            <form>
                <label class="name-6"> NAME</label><br>
                <input class="name-6-1" type="text" id="EQUIPMENT NAME" placeholder=" Name">

                <label class="mobaile-2">Mobile</label><br>
                <input class="mobile-2-1" type="text" id="NUMBER OF UNITS" placeholder="Phone">

                <label class="gmail-2">Email</label><br>
                <input class="gmail-2-1" type="email" id="PRICE" placeholder="Email">
                
                <label class="age-2">Age</label><br>
                <input class="age-2-1" type="text" id="DATE OF PURCHASE" placeholder="Enter Age">

                <label class="gender-1">GENDER</label>
                <input class="male-2" type="radio" id="Male" name="fav_language" value="Male">
                <label class="male-2-1" for="html">Male</label><br>
                <input class="female-2" type="radio" id="Female" name="fav_language" value="Female">
                <label class="female-2-1" for="css">Female</label><br>

              
                    <label class="plan-3">Plan Nmae</label><br>
                 <select class="select-1">
                    <option value="Select Plan" >Select Plan</option>
                    <option value="Chest" >Chest</option>
                    <option value="Beck" >Beck</option>
                    <option value="Bicep and Tricep" >Bicep and Tricep</option>
                    <option value="Leg and Sholder" >Leg and Sholder</option>
                    <option value="Abbs and Cordio" >Abbs and Cordio</option>
                </select>

                <label class="amount-2">Amount(In Rs)</label><br>
                <input class="amount-2-1" type="text" id="DATE OF PURCHASE" >

                <label class="duration-3">Duration</label><br>
                <select class="select-2">
                   <option value="Select Duration" >Select Duration</option>
                   <option value="2 Month" >2 Month</option>
                   <option value="4 Month" >4 Month</option>
                   <option value="6 Month" >6 Month</option>
                   <option value="8 Month" >8 Month</option>
                   <option value="1 Year" >1 Year</option>
               </select>

                <a href=""> <div class="submit-3">
                    <center> <p> Submit</p></center>
                    </div>
                     </a>
            </form>
        </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>