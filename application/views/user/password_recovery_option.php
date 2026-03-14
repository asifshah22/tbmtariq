<html>

<head>
    <title>Forgot Password Option-TBM Point of Sale</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }
    body {
      background-image: url("http://localhost/tbm-pos/assets/images/login_image/login-image.png");
    }
    main {
      flex: 1 0 auto;
    }

    .input-field input[type=date]:focus + label,
    .input-field input[type=text]:focus + label,
    .input-field input[type=email]:focus + label,
    .input-field input[type=password]:focus + label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=email]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;
    }
  </style>
</head>

<body>
  <div class="section"></div>
  <main>
    <center>
      <div class="section"></div>
        
      <div class="container" style="margin-top: 120px">
        <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

            <center>
              <div class='row'>
                <a href="<?php echo base_url()?>index.php/User/forgot_password_via_phone" class="col s12 btn btn-large" style="width: 350px; text-transform: capitalize;">Via Phone ?</a>
              </div>
              <div class='row'>
                <a href="<?php echo base_url()?>index.php/User/forgot_password" class="col s12 btn btn-large" style="text-transform: capitalize;">Via Email ?</a>
              </div>
            </center>
            <div class='row'>
                <label style='float: right;'>
                  <a class='pink-text' style="font-size:15px" href='<?php echo base_url()?>index.php/User/login_user'><b>Log In?</b></a>
                </label>
            </div>
        </div>
      </div>
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>

</html>