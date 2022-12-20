<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "DStocKOIN";
	$keywords = "dstockoin, abcd";
	$content = "Page d'accueil du site MooZeeQ";
	
	
	include ("include/header_connected.inc.php");
	
	
	if(isset($_GET["livraison"]) && !empty($_GET["livraison"])){
		$id_liv = $_GET["livraison"];
		validate_delivery($id_liv);
	}
	if(isset($_POST["order"]) && !empty($_POST["order"])){
		insert_commande($name_user[2]);
		empty_file(CARTJSON);
	}
	
	$array = get_request_result("SELECT * FROM livraison WHERE date_recep IS NULL");
	if(!empty($array)){
		$output = str_livraison($array, $headers, true);
	}
	else {
		$output = "<p class=\"p-details\">Aucune commande n'est disponible, voulez-vous effectuer une commande ?</p>
			<p class=\"p-commander\"><a href=\"produits.php?".$headers."\">Commander</a></p>";
	}			
	
	
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Toutes les commandes</h2>
				<?php echo $output; ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>