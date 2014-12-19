<?php

$r->addRoute('image_text', 'img/txt', array('controller' => 'image', 'action' => 'text'), array('width' => $id));
$r->addRoute('image_thumb', 'img/thumb/:file', array('controller' => 'image', 'action' => 'thumb', 'width' => 200, 'file' => 'default.jpg'), array('width' => $id, 'height' => $id, 'file' => '[a-z0-9.]+'));
$r->addRoute('image_crop', 'img/crop/:file', array('controller' => 'image', 'action' => 'crop', 'width' => 200), array('width' => $id, 'height' => $id, 'file' => '[a-z0-9.]+'));
$r->addRoute('image_resize', 'img/resize/:file', array('controller' => 'image', 'action' => 'resize', 'width' => 200), array('width' => $id, 'height' => $id, 'file' => '[a-z0-9.]+'));