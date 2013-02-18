<p id="contenu" class="skip">Pour faire une recherche, tapez « Ctrl » (« Cmd » sous Mac OS X) et « F » simultanément.</p>
<?php 
foreach ($notes['creation'] as $key => $value):
	$note = fetch_file($notes['name'][$key], NOTES_DIRECTORY);
	include('single_note.php');
endforeach; 
?>

