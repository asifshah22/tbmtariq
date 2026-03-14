<html>

<head>
    <title>Forgot Password-TBM Point of Sale</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }

    main {
      flex: 1 0 auto;
    }

    body {
      background-image: url("http://localhost/tbm-pos/assets/images/login_image/login-image.png");
    }

    .input-field input[type=date]:focus + label,
    .input-field input[type=text]:focus + label,
    .input-field input[type=phone]:focus + label,
    .input-field input[type=password]:focus + label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=phone]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;
    }
  </style>
</head>

<body>
  <main>
    <center>
      <div class="section"></div>

        <?php if(validation_errors()): ?>
            <p style="color: FFFFFF"><?php echo validation_errors();?></p>
        <?php endif; ?>
        <?php if($this->session->flashdata('not_found')): ?>
            <p style="color: FFFFFF"><?php echo $this->session->flashdata('not_found');?></p>
        <?php endif; ?>
        <?php if($this->session->flashdata('msg_failed')): ?>
            <p style="color: FFFFFF"><?php echo $this->session->flashdata('msg_failed');?></p>
        <?php endif; ?>
        <?php if($this->session->flashdata('msg_sent')): ?>
            <p style="color: FFFFFF"><?php echo $this->session->flashdata('msg_sent');?></p>
        <?php endif; ?>
        
      <div class="section"></div>

      <div class="container" style="margin-top: 70px">
        <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <form class="col s12" method="post" action="<?php echo base_url()?>index.php/User/forgot_password_via_phone">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div style="width:350px" class='input-field col s12'>
                <input class='validate' type='text' name='phone' id='phone' value="<?php echo $this->session->flashdata('inputed_phone');?>" />
                <label for='phone'>Enter your Phone Number</label>
              </div>

            <div class='row'>
                <label style='float: right;'>
					<a class='pink-text' style="font-size:15px" href='<?php echo base_url()?>index.php/User/login_user'><b>Log In?</b></a>
			    </label>
            </div>

            <br />
            <center>
              <div class='row'>
                <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect indigo'>Send</button>
              </div>
            </center>
          </form>
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