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
define('TEMPLATE_DIRECTORY', './template/'); # with final slash
define('CACHE_DIRECTORY', './cache/'); # with final slash
define('SITE_URL', 'http://localhost/me/Oak/');
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "french");

# Fonctions
function fetch_notes() {
	# Récupère les notes dans le dossier défini plus haut.
	$files = scandir(NOTES_DIRECTORY);
	$notes = array();

	# $regex = '#^([a-zA-Z0-9].*)\.ma?r?k?do?w?n?$#';
	$regex = '#^([a-zA-Z0-9].*)\.md$#';

	foreach ($files as $file) :
		if (preg_match($regex, $file)) :
			$file_name = preg_replace($regex, "$1", $file);
			$notes['name'][] = $file_name;
			$notes['creation'][] = filectime(NOTES_DIRECTORY.$file);
			$notes['modification'][] = filemtime(NOTES_DIRECTORY.$file);
		endif;
	endforeach;

	
	krsort($notes['creation']);
	return $notes;
}

function readable_date($date){
	# Met la date dans un format lisible.
	//return date('d-m-Y H:i:s', $date);
	return htmlentities(strftime('%A %d %B, %Hh%m', $date));
}

function fetch_note($name){
	$note['name'] = $name;
	$note['content'] = SmartyPants(Michelf\Markdown::defaultTransform(file_get_contents(NOTES_DIRECTORY.$name.'.md')));
	$note['creation'] = filectime(NOTES_DIRECTORY.$name.'.md');
	return $note;
}

function serve_cache($cachefile){
	$cachetime = 1;
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

if (empty($_GET)): # Liste des articles

	# Start cache;
	$cachefile = CACHE_DIRECTORY.'cached-index.html';
	serve_cache($cachefile);

		$notes = fetch_notes();
		include(TEMPLATE_DIRECTORY.'header.php');
		include(TEMPLATE_DIRECTORY.'notes_list.php');
		include(TEMPLATE_DIRECTORY.'footer.php');

	write_cache($cachefile);
	# End cache

elseif (! empty($_GET['note']) && file_exists(NOTES_DIRECTORY.$_GET['note'].'.md')): # Une note en particulier
	
	# Start cache;
	$cachefile = CACHE_DIRECTORY.'cached-'.mb_strtolower($_GET['note']).'.html';
	serve_cache($cachefile);

		$note_name = urldecode($_GET['note']);
		$note = fetch_note($note_name);
		include(TEMPLATE_DIRECTORY.'header.php');
		include(TEMPLATE_DIRECTORY.'single_note.php');
		include(TEMPLATE_DIRECTORY.'footer.php');

	write_cache($cachefile);
	# End cache

elseif (isset($_GET['archive'])):
	# Start cache;
	$cachefile = CACHE_DIRECTORY.'cached-archive.html';
	serve_cache($cachefile);

		$notes = fetch_notes();
		include(TEMPLATE_DIRECTORY.'header.php');
		include(TEMPLATE_DIRECTORY.'archive.php');
		include(TEMPLATE_DIRECTORY.'footer.php');

	write_cache($cachefile);
	# End cache
else:
	//header('HTTP/1.0 404 Not Found');
endif;