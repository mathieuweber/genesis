<?php include(APP_DIR.'menu.php'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9  http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	<url>
		<loc><?php __url('default', array(), false); ?></loc>
		<changefreq>hourly</changefreq>
		<priority>1.00</priority>
	</url>
<?php foreach($appMenu as $route => $item) :?>
	<url>
		<loc><?php __url($route, array(), false); ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>
<?php endforeach; ?>
</urlset>