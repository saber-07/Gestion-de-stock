<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "Produits - DStocKOIN";
	$keywords = "DStocKOIN, produits, destockage";
	$content = "Page du catalogue de produits à commander";
	
	include ("include/header_connected.inc.php");
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Produits disponibles</h2>
				<?php echo str_products($headers); ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>