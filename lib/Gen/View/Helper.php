<?php
function _t($text, $params = null)
{
	if ($params === null) {
		return _sanitize(Gen_I18n::translate($text));
	}
	
	$params = (array) $params;
	$text = _sanitize(Gen_I18n::translate($text));
	
	foreach ($params as $key => $value) {
		$text = @preg_replace('#{' . $key . '}#', $value, $text);
	}
	
	return $text;
}

function __t($text, $params = null)
{
	echo _t($text, $params);
}

function _ct($text, $context, $params = null)
{
	$token = '{context:' . $context . '}';
	$text = _t($token . $text, $params);
	
	return preg_replace('#' . $token . '#', '', $text);
}

function __ct($text, $context, $params = null)
{
	echo _ct($text, $context, $params);
}

function _pt($text, $plural, $field, array $params)
{
	if (!isset($params[$field]) || strip_tags($params[$field]) <= 1) {
		return _t($text, $params);
	}
	
	return _t($plural, $params);
}

function __pt($text, $plural, $field, array $params)
{
	echo _pt($text, $plural, $field, $params);
}

function __if($value, $true, $false = null) {
	echo $value ? $true : $false;
}

function _sanitize($text)
{
	if (is_array($text)) return array_map('_sanitize', $text);
	else return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
}

function __($text)
{
	echo _sanitize($text);
}

function __include($action, $controller, array $params = array())
{
	$file = Gen_View_Base::buildFile($controller . '/' . $action);
	echo Gen_View_Base::merge($file, $params);
}

function _url($name = 'default', array $data = array(), $relative = true)
{
	return Gen_Controller_Front::getInstance()
				->getRouter()
				->url($name, $data, $relative);
}

function __url($name = 'default', array $data = array(), $relative = true)
{
	echo _url($name, $data, $relative);
}

function _currentUrl(array $data = array(), $relative = true)
{
	$url_infos = parse_url($_SERVER['REQUEST_URI']);
	$params = array();
	if (isset($url_infos['query'])) {
		parse_str($url_infos['query'], $params);
	}
	$params = array_merge($params, $data);
	return ($relative ? '' : ('http://' . $_SERVER['HTTP_HOST'])) . $url_infos['path'] . (count($params) ? ('?' . http_build_query($params)) : '');
}

function __currentUrl(array $data = array(), $relative = true)
{
    echo _currentUrl($data, $relative);
}

function _link($url, $label, array $attributes = array())
{
	$label =  $label ? $label : $url;
	$attributes['href'] = $url;
	$attributes['target'] = '_blank';
	return _tag('a', $label, $attributes);
}

function __link($url, array $attributes = array())
{
	echo _link($url, $attributes);
}

function _email($email, array $attributes = array())
{
	if($email === null) {
		return null;
	}
	$url = 'mailto:'.$email;
	return _link($url, $email, $attributes);
}

function __email($email, array $attributes = array())
{
	echo _email($email, $attributes);
}

function _menu($caption, $name, array $data = array(), array $attributes = array())
{
	require_once('Gen/Html/Element.php');
	$url = _url($name, $data);
	$attributes['href'] = $url;
	if($url == $_SERVER['REQUEST_URI']) {
		$attributes['class'] = isset($attributes['class']) ? ($attributes['class'] . ' active') : 'active';
	}
	$link = new Gen_Html_Element('a', $attributes);
	$link->append($caption);
	return $link;
}

function __menu($caption, $name, array $data = array(), array $attributes = array())
{
	echo _menu($caption, $name, $data, $attributes);
}

function _a($label, $route = null, $attributes = array())
{
	if(is_array($route)) {
		if(isset($route['name'])) {
			$name = $route['name'];
			$data = isset($route['data']) ? $route['data'] : array();
			$relative = isset($route['relative']) ? $route['relative'] : true;
		} else {
			$name = $route[0];
			$data = isset($route[1]) ? $route[1] : array();
			$relative = isset($route[2]) ? $route[2] : true;
		}
	} elseif($route !== null) {
		$name = $route;
		$data = array();
		$relative = true;
	}
	$url ="#";
	if($route !== null) {
		$url = _url($name, $data, $relative);
	}
	$attributes['href'] = $url;
	if(($url == $_SERVER['REQUEST_URI']) || (isset($attributes['active']) && $attributes['active'] === true)) {
		$activeCss = 'a-active';
		if(isset($attributes['active_css'])) {
			$activeCss = $attributes['active_css'];
			unset($attributes['active_css']);
		} elseif(isset($attributes['class'])) {
			$activeCss = $attributes['class'] . '-active';
		}
		$attributes['class'] = isset($attributes['class']) ? ($attributes['class'] . ' ' . $activeCss) : $activeCss;
	}
	return _tag('a', _t($label), $attributes);
}

function __a($label, $route, $attributes = array()) { echo _a($label, $route, $attributes); }

function _btn($label, $route = null, $attributes = array())
{
	$class = isset($attributes['class']) ? $attributes['class'] : null;
	$attributes['class'] = 'btn ' . $class;
	$attributes['rel'] = 'nofollow';
	return _a($label, $route, $attributes);
}

function __btn($label, $route = null, $attributes = array()) { echo _btn($label, $route, $attributes); }

function _br($text, $sanitize = true)
{
	if($sanitize) { $text = _sanitize($text); }
	$dirty = preg_replace('/\r/', '', $text);
	$clean = preg_replace('/\n{3,}/', '<br/><br/>', preg_replace('/\r/', '', $dirty)); 
	return nl2br($clean);
}

function __br($text, $sanitize = true) { echo _br($text, $sanitize); }

function _text($text, $sanitize = true)
{
	//$text = utf8_decode($text);
	if($sanitize) { $text = _sanitize($text); }
	$text = preg_replace('#\*([^*\n]*)\*#', '<b>$1</b>', $text);
	$text = preg_replace('#"([^"\n]*)"#', '<i>$1</i>', $text);
	$text = preg_replace('#\b__([^_\n]*)__\b#', '<u>$1</u>', $text);
	$text = preg_replace('#\b(https?://[a-zA-Z0-9.?&=;\-_/%]*)#', '<a href="$1" target="_blank">$1</a>', $text);
	$text = preg_replace('#\b((?<!http://)www.[a-zA-Z0-9.?&=;\-_/%]*)#', '<a href="http://$1" target="_blank">$1</a>', $text);
	
	//return utf8_encode($text);
	return _br($text, false);
}

function __desc($text, $limit = 400, $sanitize = true) { echo _desc($text, $limit, $sanitize); }

function _desc($text, $limit = 400, $sanitize = true) {
	require_once('Gen/Str.php');
	$text = _unmark($text, false);
	$text = Gen_Str::shorten($text, $limit);
	if($sanitize) { $text = _sanitize($text); }
	return $text;
}

function __text($text, $sanitize = true) { echo _text($text, $sanitize); }

function _markdown($text, $sanitize = false)
{
	if($sanitize) { $text = _sanitize($text); }
	require_once('Gen/Markdown.php');
	return Gen_Markdown::parse($text);
}

function __markdown($text, $sanitize = false) { echo _markdown($text, $sanitize); }

function __unmark($text, $sanitize = true) { echo _unmark($text, $sanitize); }

function _unmark($text, $sanitize = true)
{
	if($sanitize) { $text = _sanitize($text); }
	$text = preg_replace('@[\*_#\[\]!]@', '', $text);
	return $text;
}

function _code($text, $language = 'php') {
	require_once('Gen/Css.php');
	require_once('Gen/Html.php');
	$text = str_replace('[php]', '<?php', $text);
	$text = str_replace('[/php]', '?>', $text);
	$text = htmlentities((string) $text);
	$patterns = array(
		'php' => array(
			'#\b('
				.'(class)|(abstract)|(final)|(static )|(public)|(private)|(protected)'
				.'|(function)|(extends)|(implements)|(parent)|(self)'
				.'|(int)|(string)|(bool)|(float)|(null)|(array)|(const)|(instanceof)'
				.'|(isset)|(foreach)|(while)|(as)|(if)|(else)|(return)|(endif)|(endforeach)'
				.'|((include)|(require)(_once)?)|(echo)|(new)'
				.'|(try)|(catch)|(throw)'
			.')\b#',
			'#(\$\w+)#',
			"#((->[a-zA-Z0-9_]+)+)#",
			"#('[^']*')#",
			"#\b([0-9.]+)\b#",
			"#(//.*)#",
			'#((&lt;[?]php)|(\?&gt;))#',
		),
		'sql' => array(
			'#\b((select)|(from)|(on)|((left|right)? *(inner|outer)? *join)'
				.'|(where)|(groupby)|(limit)|(orderby)'
			.')\b#',
		),
		'html' => array(
			'#(&lt;/?('.implode('|', Gen_Html::$tags).')(.*)&gt;)#',
		),
		'css' => array(
			'#\.([a-z0-9_-]+)#',
			'@(#[a-z0-9_-]+)\s*:@',
			'#('. implode('|', Gen_Css::$properties) .')\s*:#',
			'#:('. implode('|', Gen_Css::$pseudoClasses) .')#',
		)
	);
	$replaces = array(
		'php' => array(
			'<span class="php-key">$1</span>',
			'<span class="php-var">$1</span>',
			'<span class="php-prop">$1</span>',
			'<span class="php-string">$1</span>',
			'<span class="php-int">$1</span>',
			'<span class="comment">$1</span>',
			'<span class="php-tag">$1</span>',
		),
		'sql'	=> array(
			'<span class="sql">$1</span>',
		),	
		'html' => array(
			'<span class="html-tag">$1</span>',
		),
		'css' => array(
			'.<span class="css-class">$1</span>:',
			'<span class="css-id">$1</span>',
			'<span class="css-prop">$1</span>:',
			':<span class="css-pseudo">$1</span>:',
		)
	);
	$text = preg_replace($patterns[$language], $replaces[$language], $text);
	$text = preg_replace('#(/\*[a-z0-9\w\s:()@<>="/*$\.-_]*\*/)#m', '<span class="comment">$1</span>', $text);
	return '<pre class="code">'.$text.'</pre>';
}

function __code($text, $language = 'php') { echo _code($text, $language); }

function _paginate($name = 'default', array $data = array(), $total, $current_page, $adj=3)
{
	$prev = $current_page - 1; // numéro de la page précédente
	$next = $current_page + 1; // numéro de la page suivante
	$n2l = $total - 1; // numéro de l'avant-dernière page (n2l = next to last)
	$url = _url($name, $data);

	/* Initialisation : s'il n'y a pas au moins deux pages, l'affichage reste vide */
	$pagination = '';

	/* Sinon ... */
	if ($total > 1)	{
		/* Concaténation du <div> d'ouverture à $pagination */
		$pagination .= '<ul class="pagination">';

		/* previous */
		if ($current_page == 2) {
			$pagination .= '<li><a href="'.$url.'" title="' . _t("previous page") .'">&laquo;</a></li>';
		} elseif ($current_page > 2) {
			$data['page'] = $prev;
			$prev_url = _url($name, $data);
			$pagination .= '<li><a href="'.$prev_url.'" title="'. _t("page précédente") .'">&laquo;</a></li>';
		} else {
			$pagination .= '<li class="disabled"><span>&laquo;</span></li>';
		}
		
		/* ///////////////
		Début affichage des pages, l'exemple reprend le cas de 3 numéros de pages adjacents (par défaut) de chaque côté du numéro courant
		- CAS 1 : il y a au plus 12 pages, insuffisant pour faire une troncature
		- CAS 2 : il y a au moins 13 pages, on effectue la troncature pour afficher 11 numéros de pages au total
		/////////////// */

		/* CAS 1 */
		if ($total < 5 + ($adj * 2))
		{
			/* Ajout de la page 1 : on la traite en dehors de la boucle pour n'avoir que index.php au lieu de index.php?p=1 et ainsi éviter le duplicate content */
			$pagination .= ($current_page == 1) ? '<li class="active"><span>1</span></li>' : '<li><a href="'.$url.'">1</a></li>';

			/* Pour les pages restantes on utilise une boucle for */
			for ($i = 2; $i<=$total; $i++)
			{
				if ($i == $current_page) {
					$pagination .= '<li class="active"><span>'.$i.'</span>';
				} else {
					$data['page'] = $i;
					$page_url = _url($name, $data);
					$pagination .= '<li><a href="'.$page_url.'">'.$i.'</a>';
				}
			}
		}

		/* CAS 2 : au moins 13 pages, troncature */
		else
		{
			/*
			Troncature 1 : on se situe dans la partie proche des premières pages, on tronque donc la fin de la pagination.
			l'affichage sera de neuf numéros de pages à gauche ... deux à droite (cf figure 1)
			*/
			if ($current_page < 2 + ($adj * 2))
			{
				/* Affichage du numéro de page 1 */
				$pagination .= ($current_page == 1) ? '<li class="active"><span>1</span></li>' : '<li><a href="'.$url.'">1</a></li>';
				
				/* puis des huit autres suivants */
				for ($i = 2; $i < 4 + ($adj * 2); $i++)
				{
					if ($i == $current_page)
						$pagination .= "<li class=\"active\"><span>{$i}</span></li>";
					else {
						$data['page'] = $i;
						$page_url = _url($name, $data);
						$pagination .= "<li><a href=\"{$page_url}\">{$i}</a></li>";
					}
				}

				/* ... pour marquer la troncature */
				$pagination .= '<li class="disabled"><span>...</span></li>';

				/* et enfin les deux derniers numéros */
				$data['page'] = $n2l;
				$n2l_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$n2l_url}\">{$n2l}</a></li>";
				
				$data['page'] = $total;
				$total_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$total_url}\">{$total}</a></li>";
			}

			/*
			Troncature 2 : on se situe dans la partie centrale de notre pagination, on tronque donc le début et la fin de la pagination.
			l'affichage sera deux numéros de pages à gauche ... sept au centre ... deux à droite (cf figure 2)
			*/
			elseif ( (($adj * 2) + 1 < $current_page) && ($current_page < $total - ($adj * 2)) )
			{
				/* Affichage des numéros 1 et 2 */
				$pagination .= "<li><a href=\"{$url}\">1</a></li>";
				
				$data['page'] = 2;
				$second_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$second_url}\">2</a></li>";

				$pagination .= '<li class="disabled"><span>...</span></li>';

				/* les septs du milieu : les trois précédents la page courante, la page courante, puis les trois lui succédant */
				for ($i = $current_page - $adj; $i <= $current_page + $adj; $i++)
				{
					if ($i == $current_page)
						$pagination .= "<li class=\"active\"><span>{$i}</span></li>";
					else {
						$data['page'] = $i;
						$page_url = _url($name, $data);
						$pagination .= "<li><a href=\"{$page_url}\">{$i}</a></li>";
					}
				}

				$pagination .= '<li class="disabled"><span>...</span></li>';

				/* et les deux derniers numéros */
				$data['page'] = $n2l;
				$n2l_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$n2l_url}\">{$n2l}</a></li>";
				
				$data['page'] = $total;
				$total_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$total_url}\">{$total}</a></li>";
			}

			/*
			Troncature 3 : on se situe dans la partie de droite, on tronque donc le début de la pagination.
			l'affichage sera deux numéros de pages à gauche ... neuf à droite (cf figure 3)
			*/
			else
			{
				/* Affichage des numéros 1 et 2 */
				$pagination .= "<li><a href=\"{$url}\">1</a></li>";
				
				$data['page'] = 2;
				$second_url = _url($name, $data);
				$pagination .= "<li><a href=\"{$second_url}\">2</a></li>";

				$pagination .= '<li class="disabled"><span>...</span></li>';

				/* puis des neufs dernières */
				for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++)
				{
					if ($i == $current_page)
						$pagination .= "<li class=\"active\"><span>{$i}</span></li>";
					else {
						$data['page'] = $i;
						$page_url = _url($name, $data);
						$pagination .= "<li><a href=\"{$page_url}\">{$i}</a></li>";
					}
				}
			}
		}
		/* Fin affichage des pages */

		/* next page */
		if ($current_page == $total)
			$pagination .= "<li class=\"disabled\"><span title=\"" . _t("next page") . "\">&raquo;</span></li>\n";
		else {
			$data['page'] = $next;
			$next_url = _url($name, $data);
			$pagination .= "<li><a href=\"{$next_url}\" title=\"" . _t("next page") . "\">&raquo;</a></li>\n";
		}
		/* Fin affichage du bouton [suivant] */

		/* </ul> de fermeture */
		$pagination .= "</ul>\n";
	}

	/* Fin de la fonction, renvoi de $pagination au programme */
	return ($pagination);
}

function __paginate($name = 'default', array $data = array(), $total, $current_page, $adj=3)
{
	echo _paginate($name, $data, $total, $current_page, $adj);
}

function __f(&$first, $separator = ' ') {
	echo $first ? $separator . $first : '';
	$first = false;
}

function _tag($tag, $content = null, array $attributes = array())
{
	$str = '<' . $tag;
	foreach($attributes as $name => $value) {
		$str .= ' ' . $name . '="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"';
	}
	if(null === $content) {
		$str .= '/>';
	} else {
		$str.= '>'
			 . implode("\n", (array) $content)
			 . '</' . $tag . '>';
	}
	return $str;
}

function __tag($tag, $content = null, array $attributes = array())
{
	echo _tag($tag, $content, $attributes);
}

function _input($type, $name = null, $attributes = array())
{
	$attributes['type'] = $type;
	return _tag('input', null, $attributes);
}

function __input($type, $name = null, $attributes = array()) { echo _input($type, $name, $attributes); }

function _p($txt)
{
	return _tag('pre', print_r($txt, true));
}

function __p($txt) { echo _p($txt); }

function _date($date, $format = 'smart_date', $timezone = false, $lang = null) {
	if(null === $date) {
		return '';
	} elseif ($date instanceof Gen_Entity_Date) {
		return $date->format($format);
	}
	require_once('Gen/Date.php');
	if($format == 'smart_date') {
		return Gen_Date::smartDate($date);
	}
	return Gen_Date::format($date, $format, $timezone, $lang);
}

function __date($date, $format = 'smart_date', $timezone = false, $lang = null) { echo _date($date, $format, $timezone, $lang); }

function _time($time, $format = 'H:i', $timezone = false, $lang = null) {
	if(null === $time) {
		return '';
	} elseif ($time instanceof Gen_Entity_Date) {
		return $time->format($format);
	}
	return Gen_Date::format($date, $format, $timezone, $lang);
}

function __time($time, $format = 'H:i', $timezone = false, $lang = null) { echo _time($time, $format, $timezone, $lang); }

function _dasherize($text) {
	require_once('Gen/Str.php');
	return Gen_Str::urlDasherize($text);
}

function _img($src, array $attributes = array()) {
	$attributes['src'] = $src;
	return _tag('img', null, $attributes);
}

function __img($src, array $attributes = array()) { echo _img($src, $attributes); }

function _select($name, array $data, array $attributes = array())
{
	$labelAttribute = null;
	if(isset($attributes['label_attribute'])) {
		$labelAttribute = $attributes['label_attribute'];
		unset($attributes['label_attribute']);
	}
	
	require_once('Gen/Form/Select.php');
	$select = new Gen_Form_Select($attributes);
	$select->setName($name);
	if($labelAttribute !== null) {
		$select->setLabelAttribute($labelAttribute);
	}
	$select->setDatasource($data);
	return $select;
}

function __select($name, array $data, array $attributes = array()) { echo _select($name, $data, $attributes); }