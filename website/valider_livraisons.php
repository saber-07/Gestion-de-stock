<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "Livraisons à valider - DStocKOIN";
	$keywords = "DStocKOIN, commandes, commander";
	$content = "Page des commandes effectuées par le manager de l'entreprise";
	
	
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
				<h2 class="h2-accueil">Livraisons à valider</h2>
				<?php echo $output; ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>