

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Permissions Groups</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Permissions Groups</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
          <?php elseif($this->session->flashdata('error')): ?>
            <div class="alert alert-error alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if(in_array('createGroup', $user_permission)): ?>
          <a title="Create Permission" href="<?php echo base_url() ?>index.php/User/create_group" class="btn btn-success">
            <span class="glyphicon glyphicon-plus"></span>
          </a>
        <?php endif; ?>
        <?php if(in_array('printGroup', $user_permission)): ?>
          <a title="Print Permissions" target="__blank" href="<?php base_url() ?>print_permissions_groups" class="btn btn-info" id="print">
            <span class="glyphicon glyphicon-print"></span>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createGroup', $user_permission) ) || ( in_array('printGroup', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Permissions Groups</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="groupTable" class="table table-bordered">
              <thead>
                <tr>
                  <td width="5%">#</td>
                  <th width="40%" style="color:#3c8dbc">Permissions Group Name</th>
                  <th width="40%" style="color:#3c8dbc">Total Permissions</th>
                  <?php if( (in_array('viewGroup', $user_permission)) || (in_array('updateGroup', $user_permission)) || (in_array('deleteGroup', $user_permission)) ): ?>
                    <th width="10%"></th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if($groups_data): ?>
                  <?php $counter = 1; ?>                  
                  <?php foreach ($groups_data as $k => $v): ?>
                    <?php 
                      $total_permissions = 0;
                      foreach (unserialize($v['permission']) as $key => $value) {
                        $total_permissions += 1;
                      }
                    ?>
                    <tr>
                      <td><?php echo $counter++; ?></td>
                      <td style="text-transform:capitalize"><?php echo $v['group_name']; ?></td>
                      <td><?php echo $total_permissions; ?></td>

                      <td>
                        <?php if(in_array('viewGroup', $user_permission)): ?>
                          <a title="View Permission" href="<?php echo base_url(); ?>index.php/User/view_group/<?php echo $v['id']?>"><i class="glyphicon glyphicon-eye-open"></i></a> 
                        <?php endif; ?>
                        <?php if(in_array('updateGroup', $user_permission)): ?>
                          <a title="Edit Permission" href="<?php echo base_url(); ?>index.php/User/edit_group/<?php echo $v['id']?>"><i class="glyphicon glyphicon-pencil"></i></a>
                        <?php endif; ?>
                        <?php if(in_array('deleteGroup', $user_permission)): ?>  
                          <a title="Delete Permission" href="<?php echo base_url()?>index.php/User/delete_group/<?php echo $v['id']?>"><i class="glyphicon glyphicon-trash"></i></a>
                        <?php endif; ?>

                      </td>

                    </tr>
                  <?php endforeach ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<script type="text/javascript">
  $(document).ready(function() {
    $('#groupTable').DataTable({
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });

    $("#mainUserPermissionsNav").addClass('active');
    $("#mainUserPermissionsNav").addClass('menu-open');
    $("#manageGroupNav").addClass('active');
  });
</script>
