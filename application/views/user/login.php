<html>

<head>
    <title>TBM - Login Page</title>
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
            
        }
        
        .error {
            color: red;
            font-family: calibri;
            text-align: left;
        }
        
        .password-input .password-show-button {
            position: absolute;
            top: 18px;
            right: 20px;
            z-index: 3;
            opacity: .5;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .password-input .password-show-button:hover {
            opacity: .87;
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
            <div class="main-div">
                <h5 style="font-family:Road Test; color:000000; font-size: 30px; font-weight: bolder;letter-spacing: 1px;">
                    TBM Login
                </h5>
                <div class="z-depth-1 lighten-4 row" style="background-color: #ffffff; padding: 32px 40px 0px 40px; border: 1px solid #EEE;">
                    <form class="col s12" method="post" action="<?php echo base_url()?>index.php/User/login_user">

                        <div class='row'>
                            <div class='input-field col s12'>
                                <input style="box-shadow: none;" autocomplete="off" class='validate' type='text' name='username' id='username' value="<?php echo $user_username?>" />
                                <label for='username'>Enter your Username</label>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='input-field col s12 password-input'>
                                <i onclick="togglePassword()" id="removeRedEye" class="material-icons password-show-button"></i>
                                <input style="box-shadow: none;" autocomplete="off" class='validate' type='password' name='password' id='password' value="<?php echo $user_password?>" />
                                <label for='password'>Enter your password</label>
                            </div>
                            <?php $this->form_validation->set_error_delimiters('<div class="error col s12">', '</div>'); if(validation_errors()): ?>
                                <?php echo validation_errors();?>
                            <?php endif; ?>
                            <?php if($errors): ?>
                                <div class="error col s12">
                                    <?php echo $errors;?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <label style='float: right;'>
                                <a style="color: black" href='<?php echo base_url()?>index.php/User/forgot_password'>
                                    <b>Forgot Password?</b>
                                </a>
                            </label>
                        </div>
                        <center>
                            <div class='row'>
                                <button type='submit' style="background-color: #0f0f0f" name='btn_login' class='col s12 btn btn-large waves-effect'>Login</button>
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
<script type="text/javascript">

    $(document).ready(function() {
        setTimeout(function() {
            $("#removeRedEye").html('remove_red_eye');
        }, 1);
    });
    
    function togglePassword(){
        var inputField= document.querySelector('#password');
        if(inputField.getAttribute('type')=="password"){
          inputField.setAttribute('type','text');
        }else if (inputField.getAttribute('type')=="text"){
          inputField.setAttribute('type','password');
        }
    }
</script>

</html>