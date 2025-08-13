
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<footer class="footer border-top">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-4">
				<div class="copyright">All Rights Reserved | &copy; <?php echo SITE_NAME ?> - <?php echo date('Y') ?></div>
			</div>
			<div class="col">
				<div class="footer-links text-right">
					<a href="<?php print_link('info/about'); ?>">About us</a> | 
					<a href="<?php print_link('info/help'); ?>">Help and FAQ</a> |
					<a href="<?php print_link('info/contact'); ?>">Contact us</a>  |
					<a href="<?php print_link('info/privacy_policy'); ?>">Privacy Policy</a> |
					<a href="<?php print_link('info/terms_and_conditions'); ?>">Terms and Conditions</a>
				</div>
			</div>
			
		</div>
	</div>
</footer>