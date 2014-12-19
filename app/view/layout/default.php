<?php $this->setLayout('layout::app'); ?>
<header id="app_header">
	<?php include('default/header.php');?>			
</header>
<div id="app_content">
	<div id="app_content_wrapper" class="container clearfix">
			<?php echo $this->getContent(); ?>
	</div>
</div>
<footer id="app_footer">
	<?php include('default/footer.php');?>
</footer>
<a href="#0" id="back_to_top"><i class="fa fa-angle-up fa-2"></i></a>