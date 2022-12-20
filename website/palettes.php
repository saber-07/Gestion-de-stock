<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "Ajout de palettes - DstocKOIN";
	$keywords = "DstocKOIN, palettes";
	$content = "Page qui contient un formulaire qui remplit une palette";
	
	
	$livraison = "";
	if(isset($_GET["livraison"]) && !empty($_GET["livraison"])) {
		$id_liv = $_GET["livraison"];
		$livraison = "&livraison=".$id_liv;
		if(isset($_POST["id_palette"]) && !empty($_POST["id_palette"]) && isset($_POST["zone"]) && !empty($_POST["zone"]) && isset($_POST["id_recep"]) && !empty($_POST["id_recep"])) {
			$id_pal = $_POST["id_palette"];
			$zone = $_POST["zone"];
			$id_recep = $_POST["id_recep"];
			if(insert_palette($id_pal, $zone, $id_recep, $id_liv)){
				$output = "<p>Palette insérée</p>";
			}
			else {
				$output = "<p>Palette déjà existante !</p>";		
			}	
		}	
	}
	
	include ("include/header_connected.inc.php");
	
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Affecter les palettes</h2>
				<fieldset class="fieldset-palette">
					<form action="palettes.php?<?php echo $headers.$livraison; ?>" method="post" class="palette-form">
						<div>
							<label for="id_palette">Id de palette</label>
							<input type="text" name="id_palette" id="id_palette" required />
						</div>
						<div>
							<label for="zone">Zone de Pré-stockage</label>
							<select name="zone" id="zone">
							    <option value="" disabled>---Choisissez un filtre---</option>
							    <?php 
							    	for($i=0; $i<10; $i++){
								   	echo "<option value=\"&#".($i+65).";\">&#".($i+65).";</option>";						    	
							    	}
							    
							    ?>
							</select>
						</div>
						<div>
							<label for="receptionnaire">Id du réceptionnaire</label>
							<select name="id_recep" id="receptionnaire">
							    <option value="" disabled>---Choisissez un filtre---</option>
							    <?php
							    	$request = "SELECT e.id_employe FROM employe e, receptionnaire r WHERE e.id_employe=r.id_receptionnaire AND e.id_manager='".$name_user[2]."';";
							    	$array = get_request_result($request);
							    	for($i=0; $i<count($array); $i++){
								   	echo "<option value=\"".$array[$i]["id_employe"]."\">".$array[$i]["id_employe"]."</option>";						    	
							    	}
							    
							    ?>
							</select>
						</div>
						<div style="text-align: center;">
							<input type="submit" value="Ajouter" style="font-size: 15px; padding: 5px;" />
							<?php if(isset($output)) {echo $output;}?>
						</div>
					</form>
				</fieldset>
				<p class="p-receptionner"><a href="valider_livraisons.php?<?php echo $headers.$livraison; ?>">Terminer la réception des palettes</a></p>
			</section>
		</main>
	<?php
	
		include ("include/footer.inc.php");
			
	?>