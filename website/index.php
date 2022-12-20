<?php

	declare(strict_types=1);

	require ("include/functions.inc.php");
	
	
	$title = "DStocKOIN";
	$keywords = "DStocKOIN, Accueil, destockage, Administrateur, base de données";
	$content = "Page d'accueil de l'interface administrateur de l'entreprise DStocKOIN";
	
	include ("include/header.inc.php");	
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Bienvenue dans l'interface administrateur du site de DStocKoin</h2>
				<p class="p-details">Dans ce site vous pouvez en tant qu'administrateur effectuer des commandes en comparant
				les prix des produits, et en programmant les livraisons. Aussi vous pouvez consulter les livraisons prévues
				et les commandes effectuées ainsi que le stock de la Base de données de DStocKoin</p>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>