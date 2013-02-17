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

	
	ksort($notes['creation']);
	return $notes;
}

function readable_date($date){
	# Met la date dans un format lisible.
	//return date('d-m-Y H:i:s', $date);
	return htmlentities(strftime('%A %d %B, %Hh%m', $date));
}

function fetch_note($name){
	return SmartyPants(Michelf\Markdown::defaultTransform(file_get_contents(NOTES_DIRECTORY.$name.'.md')));
}

/****************************************************************************************************/

if(empty($_GET)):
	$notes = fetch_notes();
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'notes_list.php');
	include(TEMPLATE_DIRECTORY.'footer.php');
elseif(! empty($_GET['note'])):
	$note_name = urldecode($_GET['note']);
	$note = fetch_note($note_name);
	include(TEMPLATE_DIRECTORY.'header.php');
	include(TEMPLATE_DIRECTORY.'single_note.php');
	include(TEMPLATE_DIRECTORY.'footer.php');
endif;