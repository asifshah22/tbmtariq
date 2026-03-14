<?php
  $user_id = $this->session->userdata('id');
  $user_data = $this->Model_users->getUserData($user_id);
?>
<header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini" style="text-transform: uppercase;"><b><?php echo $user_data['username'][0]; ?></b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg" style="text-transform: capitalize;"><b><?php echo $user_data['username']; ?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  