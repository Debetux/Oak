<span id="contenu"></span>
<h2><?php echo $note['name']; ?></h2>
<?php echo $note['content']; ?>
<p class="skip">Publié le <?php echo strftime('%d.%m.%Y', $note['creation']); ?>. <a href="<?php echo SITE_URL.'?note='.urlencode($note['name']); ?>" title="Accès permanent à « <?php echo $note['name']; ?> »">Lien permanent</a>. <a href="#haut">Retourner en haut</a>.</p>