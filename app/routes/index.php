<?php

$r->addRoute('sitemap', 'sitemap.xml' , array('controller' => 'index', 'action' => 'sitemap', 'format' => 'xml'));
$r->addRoute('robots', 'robots.txt' , array('controller' => 'index', 'action' => 'robots', 'format' => 'txt'));