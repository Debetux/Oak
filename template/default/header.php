<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Notes de Lancelot</title>
	<link rel="stylesheet" href="<?php echo TEMPLATE_DIRECTORY; ?>style.css">
	
</head>
<body>
	<h1>Notes</h1>

	<ul id="menu" class="menu">
		<li><a href="./">Accueil</a></li>
		<li><a href="./?archive">Archives</a></li>
		<?php if($pages): ?>
			<?php foreach ($pages['creation'] as $key => $value): ?>
				<li><a href="./?page=<?php echo $pages['name'][$key]; ?>"><?php echo $pages['name'][$key]; ?></a></li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>