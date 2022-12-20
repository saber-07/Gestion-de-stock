<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "Panier - DStocKOIN";
	$keywords = "Panier, produit, prix, catégorie";
	$content = "Page du panier de l'utilisateur";
	
	if(isset($_POST["nb"]) && !empty($_POST["nb"]) && isset($_GET["id"]) && !empty($_GET["id"])){
				
		$id = (int)$_GET["id"];
		$nb = (int)$_POST["nb"];
		add_to_cart($id, $nb);
	}
	
	include ("include/header_connected.inc.php");
	
	if(empty(read_file(CARTJSON))){
		$output = "<p class=\"p-details\">Le panier est vide, voulez vous effectuer une commande ?</p>
				<p class=\"p-commander\"><a href=\"produits.php?".$headers."\">Commander</a></p>";
	}
	else {
		$output = show_cart();
		$output .= "<fieldset class=\"fieldset-connexion\">
					<form action=\"valider_livraisons.php?".$headers."\" method=\"post\" >
						<input type=\"checkbox\" value=\"true\" name=\"order\" required/>
						<input type=\"submit\" value=\"Commander\" />
					</form>
				</fieldset>";
	}
	
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Panier</h2>
				<?php echo $output; ?>
				
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>