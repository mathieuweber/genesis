<!doctype html>
<!--
 ======  P O W E R E D   B Y  ======
   _____            ____      _     
  / ____|          |___ \    (_)    
 | |  __  ___ _ __   __) |___ _ ___ 
 | | |_ |/ _ \ '_ \ |__ </ __| / __|
 | |__| |  __/ | | |___) \__ \ \__ \
  \_____|\___|_| |_|____/|___/_|___/

 ===================================  
-->
<html>
	<head>
		<!--[if gt IE 7]>
			<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
		<![endif]-->

		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php $title = $this->getContent('head_title', APP_BASELINE); ?>
		<title><?php __($title. ' - '. APP_NAME); ?></title>
		<meta name="description" content="<?php echo str_replace('"', "'", _desc($this->getContent('head_description', APP_DEFAULT_DESCRIPTION), 150)); ?>" />

		<?php if($this->getContent('fb_image')): ?>
		<meta property="og:image" content="<?php echo SERVER_URL . $this->getContent('fb_image'); ?>"/>
		<?php endif; ?>

		<?php foreach ($this->getHeadMetas() as $tag) : ?>
			<meta 
			<?php foreach ($tag as $key => $value): ?>
				<?php if($value!=null): ?><?php __($key); ?>="<?php __($value); ?>"<?php endif; ?>
			<?php endforeach; ?>
			>
		<?php endforeach; ?>

		<link rel="shortcut icon" href="<?php echo PUBLIC_URL; ?>favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo PUBLIC_URL; ?>favicon.ico" type="image/x-icon">
		<link rel="apple-touch-icon" href="<?php echo PUBLIC_URL; ?>apple-touch-icon.png" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIB_URL . 'bootstrap.min.css?v='. APP_VERSION; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIB_URL . 'font-awesome/css/font-awesome.css?v='. APP_VERSION; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo JS_LIB_URL . 'select2/select2.css?v='. APP_VERSION; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIB_URL . 'gen.css?v='. APP_VERSION; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIB_URL . 'jquery-ui/jquery-ui-1.10.2.custom.min.css'; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo STYLE_URL . 'app.css?v='. APP_VERSION; ?>" />
		<?php foreach($this->getHeadLinks() as $link) : ?>
		<link rel="<?php __($link['rel']); ?>" type="<?php __($link['type']); ?>" href="<?php __($link['href']); ?>" <?php if($link['title']) { __('title="'.$link['title'].'" '); } ?>/>
		<?php endforeach; ?>

		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'jquery-1.9.1.min.js'; ?>"></script>
	</head>
	<body>
		<?php echo $this->getContent(); ?>

		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'jquery-ui-1.10.2.custom.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'bootstrap.min.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'jquery.autosize.min.js'; ?>"></script>
		<script type="text/javascript">$(document).ready(function(){ $('textarea').autosize(); });</script>
		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'select2/select2.js'; ?>"></script>
		<script type="text/javascript">$(document).ready(function(){ $('.select2').select2(); });</script>
		<script type="text/javascript" src="<?php echo JS_LIB_URL . 'gen.js'; ?>"></script>
		
		<?php if(null !== GOOGLE_ANALYTICS_KEY): ?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo GOOGLE_ANALYTICS_KEY; ?>', 'auto');
			ga('send', 'pageview');
		</script>
		<?php endif; ?>
	</body>
</html>