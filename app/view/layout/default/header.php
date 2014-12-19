<?php include(APP_DIR.'menu.php'); ?>
<div class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php __url('default');?>"><?php echo APP_NAME; ?></a>
		</div>
		<nav class="navbar-collapse collapse">
			<ul  id="app_menu" class="nav navbar-nav">
			<?php foreach($appMenu as $route => $item): ?>
				<li><?php __a(_t($item['name']), $route); ?></li>
			<?php endforeach; ?>
			</ul>
			<ul id="quicklinks" class="nav navbar-nav navbar-right">
			<?php if($currentUser->isAuthenticated()) : ?>
				<?php if($currentUser->isAdmin()): ?>
				<li><?php __a("Admin", 'backoffice'); ?></li>
				<?php endif; ?>
				<li><?php __a($currentUser->getName(), array('user_show', array('id' => $currentUser->getId())), array('class' => 'hlist-a')); ?></li>
				<li><?php __a(_t("logout"), 'user_logout', array('class' => 'hlist-a')); ?></li>
			<?php else : ?>
				<li><?php __a(_t("login"), 'user_login', array('class' => 'hlist-a', 'rel' => 'nofollow')); ?></li>
			<?php endif; ?>
			</ul>
		</nav>
	</div>
</div>
<div id="app_breadcrumb">
	<div class="container">				
		<ol class="breadcrumb">
		<?php foreach($breadcrumbs as $i => $breadcrumb) : ?>
		<?php if($breadcrumb['route']) : ?>
			<li><?php __a($breadcrumb['label'], $breadcrumb['route']); ?></li>
		<?php else: ?>
			<li class="active"><?php __($breadcrumb['label']); ?></li>
		<?php endif; ?>
		<?php endforeach; ?>
		</ol>
	</div>
</div>