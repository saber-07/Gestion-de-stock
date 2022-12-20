<?php
	declare(strict_types=1);

	require "include/functions.inc.php";

	$title = "Fiche Produit - DStocKOIN";
	$keywords = "DStocKOIN, produit, prix, note utilisateur, commande";
	$content = "Page qui contient la fiche descriptive d'un produit";
	
	$output ="<p>Erreur sur la fiche descriptive....</p>";
	
	include ("include/header_connected.inc.php");
	
	if(isset($_GET["id"]) && !empty($_GET["id"])){
		$id = $_GET["id"];
		$output = str_product_details($id, $headers);
	}


	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Fiche du produit</h2>
				<?php echo $output; ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>