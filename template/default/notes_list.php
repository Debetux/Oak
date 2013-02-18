<ul id="contenu" class="journal">
<?php 
foreach ($notes['creation'] as $key => $value):
	echo '<li><a href="./?note='.urlencode($notes['name'][$key]).'">'.$notes['name'][$key].'</a>'
	.' - '.
	readable_date($notes['creation'][$key]).'</li>';
endforeach; 
?>
</ul>
<p class="skip"><a href="#haut">Retourner en haut</a></p>