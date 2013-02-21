<?php

/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

# Include PHP-Markdown parser
require_once('Markdown.php');
require_once('smartypants.php');

# Constants
define('VERSION', '0.01');
define('NOTES_DIRECTORY', './notes/'); # with final slash
define('PAGES_DIRECTORY', './pages/'); # with final slash
define('TEMPLATE_DIRECTORY', './template/default/'); # with final slash
define('CACHE_DIRECTORY', './cache/'); # with final slash
define('SITE_URL', 'http://localhost/me/Oak/');
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "french");

# Fonctions
function fetch_files($directory) {
	# Récupère les fichiers dans le dossier défini plus haut.
	$files = scandir($directory);
	$notes = array();

	# $regex = '#^([a-zA-Z0-9].*)\.ma?r?k?do?w?n?$#';
	$regex = '#^([a-zA-Z0-9].*)\.md$#';

	foreach ($files as $file) :
		if (preg_match($regex, $file)) :
			$file_name = preg_replace($regex, "$1", $file);
			$notes['name'][] = $file_name;
			$notes['creation'][] = filectime($directory.$file);
			$notes['modification'][] = filemtime($directory.$file);
		endif;
	endforeach;

	
	krsort($notes['creation']);

	if(!empty($notes))
		return $notes;
	else
		return false;
}

function readable_date($date){
	# Met la date dans un format lisible.
	//return date('d-m-Y H:i:s', $date);
	return htmlentities(strftime('%A %d %B, %Hh%m', $date));
}

function fetch_file($name, $directory){
	$note['name'] = $name;
	$note['content'] = SmartyPants(Michelf\Markdown::defaultTransform(file_get_contents($directory.$name.'.md')));
	$note['creation'] = filectime($directory.$name.'.md');
	return $note;
}

function serve_cache($cachefile, $cachetime = 3600){
	# Serve from the cache if it is younger than $cachetime
	if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
		echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
		readfile($cachefile);
		exit;
	}
	ob_start(); #Start the output buffer
}

function write_cache($cachefile){
	# Cache the contents to a file
	$cached = fopen($cachefile, 'w');
	fwrite($cached, ob_get_contents());
	fclose($cached);
	ob_end_flush(); # Send the output to the browser
}

/****************************************************************************************************/

# Init pages :
$pages = fetch_files(PAGES_DIRECTORY);
# Start cache;

if (empty($_GET)): # Liste des articles
	
	$cache_name = 'index';
	$cachefile = CACHE_DIRECTORY.'cached-'.$cache_name.'.html';
	serve_cache($cachefile);
	$notes = fetch_files(NOTES_DIRECTORY);
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'notes_list.php');
	include(TEMPLATE_DIRECTORY.'footer.php');

elseif (! empty($_GET['note']) && file_exists(NOTES_DIRECTORY.$_GET['note'].'.md')): # Une note en particulier

	$cache_name = mb_strtolower(urldecode($_GET['note']));
	$cachefile = CACHE_DIRECTORY.'cached-'.$cache_name.'.html';
	serve_cache($cachefile);
	$note_name = urldecode($_GET['note']);
	$note = fetch_file($note_name, NOTES_DIRECTORY);
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'single_note.php');
	include(TEMPLATE_DIRECTORY.'footer.php');

elseif (isset($_GET['archive'])):

	$cache_name = 'archive';
	$cachefile = CACHE_DIRECTORY.'cached-'.$cache_name.'.html';
	serve_cache($cachefile);
	$notes = fetch_files(NOTES_DIRECTORY);
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'archive.php');
	include(TEMPLATE_DIRECTORY.'footer.php');

elseif (!empty($_GET['page']) && file_exists(PAGES_DIRECTORY.$_GET['page'].'.md')):

	$cache_name = mb_strtolower(urldecode($_GET['page']));
	$cachefile = CACHE_DIRECTORY.'cached-'.$cache_name.'.html';
	serve_cache($cachefile);
	$note_name = urldecode($_GET['page']);
	$note = fetch_file($note_name, PAGES_DIRECTORY);
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'page.php');
	include(TEMPLATE_DIRECTORY.'footer.php');

elseif (isset($_GET['rss'])):

	$cache_name = 'rss';
	$cachefile = CACHE_DIRECTORY.'cached-'.$cache_name.'.html';
	serve_cache($cachefile);
	header('Content-type: application/xml; charset=UTF-8');
	
else:
	header('HTTP/1.0 404 Not Found');
	exit();
endif;

write_cache($cachefile);
# End cache