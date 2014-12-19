<?php $this->setLayout('layout::default'); ?>
<?php $this->setContentFor('head_title', $this->getContent('head_title'). " - Backoffice"); ?>
<?php include(APP_DIR.'backoffice_menu.php'); ?>
<div class="row">
	<div class="col-md-2">
		<ul class="mlist border">
<?php foreach($backofficeMenu as $header => $items): ?>
			<li class="mlist-li"><span class="mlist-head"><?php __($header); ?></span></li>
<?php foreach($items as $item): ?>
			<li class="mlist-li"><?php __a($item['name'], $item['route'], array('class' => 'mlist-a')); ?></li>
<?php endforeach; ?>
<?php endforeach; ?>
		</ul>
	</div>
	<div class="col-md-10">
	<?php echo $this->getContent(); ?>
	</div>
</div>