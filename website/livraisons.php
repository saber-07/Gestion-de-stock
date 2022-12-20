<?php

	declare(strict_types=1);

	require ("include/functions.inc.php");
	
	$title = "Livraisons - DStocKOIN";
	$keywords = "DStocKOIN, livraisons";
	$content = "Page des livraisons de DstocKOIN";
	
	$request = "SELECT * FROM Livraison";
	$delivery_type = "Toutes les livraisons";
	$array = get_request_result($request);
	
	$output = "	<div id=\"deliverylist\">
						<p>Pas d'élements trouvés</p>									
					</div>";
	include ("include/header_connected.inc.php");
	
	if(isset($_POST["delivery"]) && !empty($_POST["delivery"])) {
		
		$delivery = $_POST["delivery"];
		//On affiche les cas de 		
		switch ($delivery) {
			case "all":
				$request = "SELECT * FROM Livraison";
				$delivery_type = "Toutes les livraisons";
				$array = get_request_result($request);
				if(!empty($array)){
					$output = str_livraison($array, $headers);
				}
				break;
			case "received":
				$request = "SELECT * FROM Livraison WHERE date_recep IS NOT NULL";
				$delivery_type = "Livraisons reçues";
				$array = get_request_result($request);
				if(!empty($array)){
					$output = str_livraison($array, $headers);
				}
				break;
			case "not received":
				$request = "SELECT * FROM Livraison WHERE date_recep IS NULL";
				$delivery_type = "Livraisons non reçues";
				$array = get_request_result($request);
				if(!empty($array)){
					$output = str_livraison($array, $headers);
				}
				break;
			case "received late":
				$request = "SELECT * FROM Livraison WHERE date_recep>date_recep_prevue;";
				$delivery_type = "Livraisons en retard";
				$array = get_request_result($request);
				if(!empty($array)){
					$output = str_livraison($array, $headers);
				}
				break;
			case "not received and late":
				$request = "SELECT * FROM Livraison l, Satisfait s,Fournisseur f WHERE l.id_commande = s.id_commande AND s.id_fournisseur = f.id_fournisseur AND l.date_recep IS NULL and l.date_recep_prevue < NOW();";
				$delivery_type = "Livraisons non reçues et en retard";
				$array = get_request_result($request);
				if(!empty($array)){
					$output = str_livraison_fournisseur($array, $headers);
				}
				break;
		}
	}
	else {
		if(!empty($array)){
			$output = str_livraison($array, $headers);	
		}
	}
	
	
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil"><?php echo $delivery_type ?></h2>
				<fieldset class="fieldset-delivery">
					<form action="#" method="post" class="delivery-form">
						<div>
							<select name="delivery" id="delivery">
							    <option value="" disabled>---Choisissez un filtre---</option>
							    <option value="all">Toutes les livraisons</option>
							    <option value="received">Reçues</option>
							    <option value="not received">Pas reçues</option>
							    <option value="not received and late">Pas reçues et en retard</option>
							    <option value="received late">Reçues en retard</option>
							</select>
						</div>
						<div>
							<input type="submit" value="Filtrer"/>
						</div>
					</form>
				</fieldset>
				<?php echo $output ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>