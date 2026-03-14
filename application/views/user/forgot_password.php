<html>

<head>
  <title>Forgot Password-TBM Point of Sale</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/icon.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/materialize.min.css') ?>">
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
      background-color: #d2d6de;
    }

    .input-field input[type=text]:focus + label,
    .input-field input[type=password]:focus + label {
      color: red;
    }

    .input-field input[type=text]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 1px solid red;
      box-shadow: none;
    }
    .error {
      color: red;
      font-family: calibri;
      text-align: left;
    }
    .main-div{
      width: 33%;
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
</head>

<body>
  <main>
    <center>
      <?php if($this->session->flashdata('msg_failed')): ?>
        <p style="font-family: calibri; font-weight: bold;letter-spacing: 1px; color: #1a1a1a; font-size: 20px"><?php echo $this->session->flashdata('msg_failed');?></p>
      <?php endif; ?>
      <div class="main-div">
        <h5 style="font-family:Road Test; color:000000; font-size: 30px; font-weight: bolder;letter-spacing: 1px; text-shadow: ;">
          Forgot Password
        </h5>
        <div class="z-depth-1 lighten-4 row" style="background-color: #ffffff; padding: 32px 40px 0px 40px; border: 1px solid #EEE;">

          <form class="col s12" method="post" action="<?php echo base_url()?>index.php/User/forgot_password">

            <div class='row'>
              <div class='input-field col s12'>
                <input style="box-shadow: none;" autocomplete="off" class='validate' type='email' name='email' id='email' value="<?php echo $this->session->flashdata('inputed_email');?>" />
                <label for='email'>Enter your email</label>
              </div>
              <?php if(validation_errors()): ?>
                <div class="error col s12"><?php echo validation_errors();?></div>
              <?php endif; ?>
              <?php if($this->session->flashdata('not_found')): ?>
                  <div class="error col s12"><?php echo $this->session->flashdata('not_found');?></div>
              <?php endif; ?>
              <?php if($this->session->flashdata('msg_sent')): ?>
                  <div class="error col s12"><?php echo $this->session->flashdata('msg_sent');?></div>
              <?php endif; ?>
            </div>

            <div class='row'>
              <label style='float: right;'>
                <a style="font-weight: bold;color: black" href='<?php echo base_url()?>index.php/User/login_user'><b>Log In?</b></a>
			        </label>
            </div>

            <center>
              <div class='row'>
                <button type='submit' name='btn_login' style="background-color: #0f0f0f" class='col s12 btn btn-large waves-effect'>Send</button>
              </div>
            </center>
          </form>
        </div>
      </div>
    </center>

  </main>

  <script src="<?php echo base_url('assets/dist/js/jquery.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/dist/js/materialize.min.js') ?>"></script>
</body>

</html>