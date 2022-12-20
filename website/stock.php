<?php

	declare(strict_types=1);

	require "include/functions.inc.php";
	
	$title = "Stock - DStocKOIN";
	$keywords = "DStocKOIN, stock, produit, description, nom_p";
	$content = "Page qui liste les types de produit et leur stock";
	
	$request = "SELECT id_produit, nom_p, description, prix, allee, emplacement, categorie,
		marque, url_image, pourcent_promo, note_client, \"Contenu\"-\"Vendu\" AS \"stock\" FROM
		(SELECT produit.id_produit,nom_p,description,prix,allee,emplacement,categorie,marque,url_image,pourcent_promo,note_client,
		(CASE WHEN Contenu.S1 is NULL THEN 0 ELSE Contenu.S1 END) AS \"Contenu\",
		(CASE WHEN Vendu.S2 is NULL THEN 0 ELSE Vendu.S2 END) AS \"Vendu\" FROM Produit
		LEFT JOIN (SELECT id_produit, SUM(Contient.nb_exemp_contenu) AS S1 FROM Contient GROUP BY id_produit) AS Contenu ON Contenu.id_produit = Produit.id_produit
		LEFT JOIN (SELECT id_produit, SUM (nb_exemp_vendu) AS S2 FROM Vend
		GROUP BY id_produit) AS Vendu ON Vendu.id_produit=Produit.id_produit) AS t;";
	
	$array = get_request_result($request);
	
	$output = str_stock($array);
	
		
	include ("include/header_connected.inc.php");
	
?>
		<main>
			<section class="section-accueil">
				<h2 class="h2-accueil">Tous les produits</h2>
				<?php echo $output; ?>
			</section>
		</main>
<?php
		
	include ("include/footer.inc.php");
				
?>