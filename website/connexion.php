<?php

	declare(strict_types=1);
	
	
	require "include/functions.inc.php";
	$title = "Connexion - DStocKOIN";
	$keywords = "DStocKOIN, connexion, id, mot de passe";
	$content = "Page de connexion";
	
	
	$formulaire="<h2 class=\"h2-accueil\">Connexion</h2>
					<fieldset class=\"fieldset-connexion\">
						<form action=\"connexion.php\" method=\"post\" class=\"connexion-form\">
						<div>
							<label>id : </label>
							<input type=\"text\" name=\"id\" size=\"10\" placeholder=\"Id administrateur, Ex : M00035\" required=\"required\" />
						</div>
						<div>
							<label>Mot de passe : </label>
							<input type=\"password\" name=\"password\" size=\"10\" placeholder=\"Mot de passe...\" required=\"required\" />
						</div>					
							<input type=\"submit\" value=\"Connexion\"/>
						</form>
					</fieldset>";
	
	if(isset($_POST["password"]) && !empty($_POST["password"]) && isset($_POST["id"]) && !empty($_POST["id"])){
		$id = $_POST["id"];
		$pw = $_POST["password"];
		
		if(connect_user($id, $pw)){
			$name_user = get_name_user($id);
			$headers = "nom=".$name_user[0]."&prenom=".$name_user[1]."&manager=".$id;
			$formulaire="<h2 class=\"h2-accueil\">Connecté</h2>
							<p class=\"p-details\">Bonjour, bienvenue ".$name_user[0]." ".$name_user[1]."</p>";
			include ("include/header_connected.inc.php");		
		}
		else {
			$formulaire.= "<p class=\"p-details\">Mot de passe ou id erroné !!</p>";
			include ("include/header.inc.php");
		}
	}
	else {
		include ("include/header.inc.php");
	}
	
	
?>
		<main>
			<section class="section-accueil">
				<?php echo $formulaire; ?>
			</section>
		</main>
		<?php
		
			include ("include/footer.inc.php");
				
		?>