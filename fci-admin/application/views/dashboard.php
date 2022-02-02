<section class="dashboard">
	<div class="row">
		<div class="col-md-12">
			<h3 class="heading">Welcome back, <?php echo ucfirst($this->session->userdata['logged_in']->first_name); ?></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="section-white">
				<p class="text-uppercase p-b-15"><b>Study Content</b></p>
				<p class="sub-text">Here is where you can add and edit all study content
					including chapters, topics as well as assign
					resources to the sidebar</p>
				<div class="btn-group btn-group-justified">
					<a href="<?php echo base_url() ?>educational/list-chapters/study" class="btn btn-primary-outline btn-lg btn-block"> Study Content</a>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="section-white">
				<p class="text-uppercase p-b-15"><b>Global Resources</b></p>
				<p class="sub-text">Here is where you can add and edit all educational content</p>
				<a href="<?php echo base_url() ?>resources/list-resources" class="btn btn-primary-outline btn-lg btn-block">Go to global resources</a>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="section-white">
				<p class="text-uppercase p-b-15"><b>Participants</b></p>
				<p class="sub-text">Here is where you can add, edit and view fill list of participants</p>
				<div class="btn-group btn-group-justified">
					<a href="<?php echo base_url() ?>user/list-users/study" class="btn btn-primary-outline btn-lg btn-block">Study Participants</a>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="section-white">
				<p class="text-uppercase p-b-15"><b>Personnel</b></p>
				<p class="sub-text">Here is where you can add, edit and view the list of study
					personnel and their contact details.</p>
				<a href="<?php echo base_url() ?>user/list-users" class="btn btn-primary-outline btn-lg btn-block">Go to personnel</a>
			</div>
		</div>	
	</div>
</section>
