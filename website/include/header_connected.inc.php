<?php

	if((isset($_GET["nom"]) && !empty($_GET["nom"])) && (isset($_GET["prenom"]) && !empty($_GET["prenom"])) && (isset($_GET["manager"]) && !empty($_GET["manager"]))){
		$name_user= array();
		$name_user[0] = $_GET["nom"];	
		$name_user[1] = $_GET["prenom"];
		$name_user[2] = $_GET["manager"];
		$headers = 'nom='.$name_user[0].'&amp;prenom='.$name_user[1].'&amp;manager='.$name_user[2];
	}

?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<!-- métadonnées de la page -->
		<meta charset="utf-8" />
		<meta name="author" content="Omar CHAKER &amp; Aymen ZEMMOURI &amp; Saber ABDERRAHMANE" />
		<meta name="date" content="2022-12-03T07:54:38+0100" />
		<meta name="contenu" content="<?php echo $content; ?>" />
		<meta name="keywords" content="<?php echo $keywords; ?>" />
		<title><?php echo $title; ?></title>
		<!-- Liaison avec la feuille de style -->
		<link rel="stylesheet" href="style/styles.css" />
		<link rel="icon" href="img/Logo_site.png" />
	</head>
	<body class="body">
		<header>
			<img src="img/Logo_dskoin.png" alt="Logo du site" />
			<nav>
				<ul>
					<li><a href="valider_livraisons.php?<?php echo $headers; ?>">Livraisons en attente</a></li>
					<li><a href="livraisons.php?<?php echo $headers; ?>">Livraisons</a></li>
					<li><a href="stock.php?<?php echo $headers; ?>">Stock</a></li>
					<li><a href="produits.php?<?php echo $headers; ?>">Produits</a></li>
					<li><a href="index.php">Déconnexion</a></li>
					<li><strong><?php echo $name_user[0]." ".$name_user[1]; ?></strong></li>
					<li><a href="cart.php?<?php echo $headers; ?>"><img src="img/cart.png" alt="Panier" /></a></li>
				</ul>
			</nav>
		</header>