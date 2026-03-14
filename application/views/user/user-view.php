<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Users 
      <small>User View</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">User View</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">View</h3>    
          </div>
          <div class="box-body">
            <table class="table table-striped table-bordered">
              <tbody>
                <tr>
                  <td><strong>ID</strong></td>
                  <td><?php echo $user_data['id']; ?></td>
                </tr>
                <tr>
                  <td><strong>Name</strong></td>
                  <td><?php echo $user_data['firstname']. ' ' .$user_data['lastname']; ?></td>
                </tr>
                <tr>
                  <td><strong>Username</strong></td>
                  <td><?php echo $user_data['username']; ?></td>
                </tr>
                <tr>
                  <td><strong>Email</strong></td>
                  <td><?php echo '<a href = "mailto: '.$user_data['email'].'">'.$user_data['email'].'</a>'; ?></td>
                </tr>
                <tr>
                  <td><strong>Contact</strong></td>
                  <?php 
                    $phones = '';
                    $db_phones = unserialize($user_data['phone']);
                    $count_phones = count($db_phones);
                    for ($x = 0; $x < $count_phones; $x++) {
                      if($x == $count_phones - 1){
                        $phones .= $db_phones[$x];
                      }
                      else{
                        $phones .= $db_phones[$x]. ", ";
                      }
                    }
                  ?>
                  <td><?php echo $phones; ?></td>
                </tr>
                <tr>
                  <td><strong>User Picture</strong></td>
                  <?php 
                    $image = '';
                    if($user_data['image']){
                      $image = '<a target="_blank" href="'.base_url().'assets/images/user_images/'.$user_data['image'].'" title="User image"><img src="'.base_url('/assets/images/user_images/'.$user_data['image'].'').'" class="img-circle" alt="User image" width="100" height="100" /></a>';
                    }
                  ?>
                  <td><?php echo $image; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">

  $("#mainUserPermissionsNav").addClass('active');

</script>
