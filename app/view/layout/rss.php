<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<rss version="2.0">
	<channel>
	
		<title><?php __($this->getContent('head_title', APP_BASELINE). ' - '. APP_NAME); ?></title>
		<link><?php echo SERVER_URL; ?></link>
		<description><?php __($this->getContent('head_description', APP_DEFAULT_DESCRIPTION)); ?></description>
		<language>fr</language>
		<generator>Gen3sis</generator>
		
		<?php echo $this->getContent(); ?>
		
	</channel>
</rss>