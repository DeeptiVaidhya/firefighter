
<div class="left_col scroll-view">
    <div class="navbar nav_title">
        <a href="<?php echo $base_url ?>dashboard" class="site_title"><img src="<?php echo assets_url('images/logo.png') ?>" > </a>
    </div>

    <div class="clearfix"></div>
    <?php $login_user_detail = $this->session->userdata('logged_in');?>
    <!-- menu profile quick info -->
    <div class="profile clearfix">
        <div class="profile_pic">
            <?php $profile_image = ($login_user_detail->profile_picture == '') ? 'assets/images/default-avatar.png' : 'assets/uploads/images/' . $login_user_detail->profile_picture?>
            <img src="<?php echo base_url($profile_image) ; ?>" alt="..." class="img-circle profile_img">
        </div>
        <div class="profile_info">
            <span>Welcome,</span>
            <h2><?php echo ucwords($login_user_detail->first_name . ' ' . $login_user_detail->last_name); ?></h2>
        </div>
    </div>
    <!-- /menu profile quick info -->

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
            <ul class="nav side-menu">
                <?php if ($login_user_detail->user_type == 2) {?>
                    <li><a href="<?php echo $base_url ?>dashboard"><i class="fa fa-tachometer"></i> Control Panel</a></li>
                    <li><a href="<?php echo $base_url ?>site/site-setting"><i class="fa fa-cogs"></i> Site Setting</a></li>
                    <li><a href="<?php echo $base_url ?>educational/list-chapters/study"><i class="fa fa-graduation-cap"></i> Study Content</a></li>
                    <li><a href="<?php echo $base_url ?>resources/list-resources"><i class="fa fa-info-circle"></i> Global Resources</a></li>
					<li><a href="<?php echo $base_url ?>user/list-users"><i class="fa fa-users"></i> Personnel</a></li>
					<li><a href="<?php echo $base_url ?>user/list-users/study"><i class="fa fa-users"></i> Study Participants</a></li>

                <?php }?>
                <?php if ($login_user_detail->user_type == 1) {?>
                    <li><a href="<?php echo $base_url ?>user/list-users"><i class="fa fa-users"></i> Participants</a></li>
                <?php }?>

                    <li><a href="<?php echo $base_url ?>auth/profile"><i class="fa fa-address-card"></i> Profile</a></li>
                    <li><a href="<?php echo $base_url ?>auth/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- /sidebar menu -->
</div>
