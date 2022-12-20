<?php
	
	require "include/util.inc.php";	
	
	
	/**
	* Fonction qui vérifie l'id utilisateur et le mot de passe
	* @param $id_user id utilisateur
	* @param $password mot de passe
	* @return true si c'est les bons identifiants, sinon false
	* */
	
	function connect_user(string $id_user, string $password) : bool {
			$connect = connect_server_web();
			$tmp = pg_query($connect, "SELECT * FROM manager WHERE manager.id_manager='".$id_user."';");
			//On récupère la première ligne car on aura un seul tuple après la sélection		
			$result = pg_fetch_all($tmp);
			
			$tmp2 = $result[0];
			$password_hash = $tmp2["mdp"];
			if(password_verify($password, $password_hash)){
				return true;			
			}
			return false;
	}
	
	
	
	/***********************Récuperation des infos de la BD*********************/	
	/**
	* Fonction qui retourne le nom et le prénom d'un manager
	* @param $id_user le nom d'utilisateur
	* @return $nom_prenom un tableau de deux élements qui contient le nom et le prénom	
	*/
	
	
	function get_name_user(string $id_user) : array {
		$connect = connect_server_web();
		if($connect === 0){
			echo "<p>Error : Unable to open database</p>\n";
			return array("", "");
		}
		/** Requête: select nom, prenom from manager, employe where manager.id_manager=employe.id_employe and manager.id_manager='M00035';
***/
		$tmp = pg_query($connect, "SELECT nom, prenom FROM manager, employe WHERE manager.id_manager=employe.id_employe
			AND manager.id_manager='".$id_user."';");
		//On récupère la première ligne car on aura un seul tuple après la sélection		
		$result = pg_fetch_row($tmp);
		if($result == NULL){
			$result = array("");		
		}
		return $result;
	}
	
	/**
	* Fonction qui convertit un tableau de stock en tableau html
	* @param $table tableau du stock
	* @return $output tableau html qui représente le stock	
	* */
	
	function str_stock(array $table) : string {
	
		$output = "<div id=\"stocklist\">";
		
		// Affichage du heading
		$table2 = $table[0];
		$output .= "<div class=\"div-stock-title\">
							<ul>";
		foreach($table2 as $key => $value) {				
			$output .="						<li>".$key."</li>\n";
		}
		$output .=		"</ul>
						</div>";

		for($i = 0; $i <count($table); $i++) {
			$table2 = $table[$i];
			
			// Affichage du heading
			
			//Affichage du contenu			
			$output .= "<div class=\"div-stock\">
								<ul>";
			foreach($table2 as $key => $value) {
				if($value == NULL){
					$output .="		<li>null</li>\n";
				}				
				else {
					$string = $value;			
					if(strlen($value) >= 20) {
						$string = substr($value, 0, 20)."...";		
					}
					
					$output .="		<li>".$string."</li>";
				}			
			}
			$output .=		"</ul>
							</div>";
		}
		$output .="</div>";
		return $output;
	}
	
	/**
	* Fonction qui convertit un tableau de livraisons en tableau html
	* @param $table tableau du stock
	* @param $headers nom et prenom du manager connecté
	* @param $modify attribut qui donne une date de réception de la livraison
	* @return $output tableau html qui représente le stock	
	* */
	
	function str_livraison(array $table, string $headers, bool $modify = false) : string {
	
		
		if($modify){
			$output = "<div id=\"deliverylistmodify\">";
		}
		else {
			$output = "<div id=\"deliverylist\">";
		}
		
		// Affichage du heading
		$table2 = $table[0];
		
		$output .= "<div class=\"div-delivery-title\">
							<ul>";
		// Affichage du heading		
		foreach($table2 as $key => $value) {				
			$output .="						<li>".$key."</li>\n";
		}
		$output .=		"</ul>
						</div>";

		for($i = 0; $i < count($table); $i++) {
			$table2 = $table[$i];
						
			//Affichage du contenu
			$output .= "<div class=\"div-delivery\">
								<ul>";
			foreach($table2 as $key => $value) {
				if($value == NULL){
					$output .="		<li>null</li>\n";
				}				
				else {
					$string = $value;			
					if(strlen($value) >= 20) {
						$string = substr($value, 0, 20)."...";		
					}
					
					$output .="		<li>".$string."</li>";
				}			
			}
			$output .=		"</ul>";
			
			if($modify){
				$output .="	\n<form action=\"palettes.php?".$headers."&livraison=".$table2["id_livraison"]."\" method=\"post\" >
									<input type=\"checkbox\" value=\"true\" name=\"reception\" required/>
									<input type=\"submit\" value=\"Réceptionner\" />						
								</form>";
			}
			
			$output .=		"</div>";
		}
		$output .="</div>";
		return $output;
	}
	
	/**
	* Fonction qui convertit un tableau de livraisons avec le fournisseur en tableau html
	* @param $table tableau du stock
	* @return $output tableau html qui représente le stock	
	* */
	
	function str_livraison_fournisseur(array $table) : string {
	
		$output = "<div id=\"delsupplist\">";
		
		// Affichage du heading
		$table2 = $table[0];	
		$output .= "<div class=\"div-delsupp-title\">
							<ul>";
		foreach($table2 as $key => $value) {			
			$output .="						<li>".$key."</li>\n";
		}
		$output .=		"</ul>
						</div>";

		for($i = 0; $i <count($table); $i++) {
			$table2 = $table[$i];
			
			// Affichage du heading
			
			//Affichage du contenu			
			$output .= "<div class=\"div-delsupp\">
								<ul>";
			foreach($table2 as $key => $value) {
				if($value == NULL){
					$output .="		<li>null</li>\n";
				}				
				else {
					$output .="		<li>".$value."</li>\n";
				}			
			}
			$output .=		"</ul>
							</div>";
		}
		$output .="</div>";
		return $output;
	}
	
	/**
	* Fonction qui récupèrent les produits d'une catégorie en JSON depuis dummy JSON
	* @param $category categorie des produits
	* @return $array un tableau associatif qui contient les produits listés avec l'api
	* */
	
	/*function get_products(string $category) : array{
		$url = "https://dummyjson.com/products";
		
				
		if(is_token_expired(TOKENFILE)){
			generate_save_token();
		}
		
		$token = read_file(TOKENFILE);

		if(strcmp($category, "all") !== 0){
		
			#$array = get_JSON_array($url."?token=".$token."&limit=".$limit);
				
		}
		else {
			#$array = get_JSON_array($url."/category/".$category."?token=".$token."&limit=".$limit);
		}
		return $array;	
	}*/
	
	
	/**
	* Fonction qui récupèrent les produits de toutes les catégories en JSON depuis dummy JSON
	* @param $headers nom et prenom du manager connecté
	* @return $array un tableau associatif qui contient les produits listés avec l'api
	* */
	
	function str_products(string $headers) : string {
		
		/*$products = get_products($category);*/
		$array = get_token_resource();
		
		$products = $array["products"];
		$nb_products = $array["limit"];
		
		$output ="<div class=\"section-recherche-div\">\n";		
		
		for($i=0; $i<$nb_products; $i++){
			$product = (array)$products[$i]; 
				
				
			// Récupération des infos importantes du produit
			
			$product_id = $product["id"];
			$product_title = $product["title"];
			$product_price = $product["price"];
			
			$product_discount = $product["discountPercentage"];
			$discount_price = discount_price($product_price, $product_discount);
			$discount_price = number_format($discount_price, 2, ",", "");
			
			
			$images = $product["images"];
			$product_picture = $images[0];
		
			$output .= "<div class=\"item\">\n
								<a href=\"fiche_produit.php?id=".$product_id."&".$headers."\" ><img src=\"".$product_picture."\" alt=\"".$product_title."\" /></a>\n
							 	<ul>\n
									<li class=\"title\"><a href=\"fiche_produit.php?id=".$product_id."&".$headers."\" >".$product_title."</a></li>\n
									<li class=\"title\"><s>".$product_price."$</s> <strong  style=\"color: red\">".$discount_price."$</strong></li>\n
								</ul>\n
							</div>\n";
			
		}
		return $output;
	}
	
	/**
	* Fonction qui récupèrent les produits de toutes les catégories en JSON depuis dummy JSON
	* @param $id l'id d'un produit
	* @param $headers nom et prenom du manager connecté
	* @return $array un tableau associatif qui contient les produits listés avec l'api
	* */
	
	function str_product_details(string $id, string $headers) : string {
		
		/*$products = get_products($category);*/
		$products = get_token_resource();
		$output= "<p>Élement non trouvé</p>";
		
		if(search_product(intval($id), $products)){
			
			$product = search_product(intval($id), $products);
			// Récupération des infos importantes du produit
			$product_title = $product["title"];
			$product_discount = $product["discountPercentage"];
			$product_price = $product["price"];
			$product_rating = $product["rating"];
			$product_brand = $product["brand"];
			$product_category = $product["category"];
			
			$discount_price = discount_price($product_price, $product_discount);
			$discount_price = number_format($discount_price, 2, ",", "");
			
			$images = $product["images"];
			$product_picture = $images[0];
		
			$output = "	<div id=\"div-artist\">
								<img src=\"".$product_picture."\" alt=\"".$product_title."\"/>\n
								<ul>
									<li id=\"artist\">".$product_title."</li>
									<li>Prix : <s>".$product_price."$</s> <strong  style=\"color: red\">".$discount_price."$</strong></li>
									<li>Note utilisateur : ".$product_rating."</li>
									<li>Marque : ".$product_brand."</li>
									<li>Catégorie : ".$product_category."</li>
									<li>
										<form action=\"cart.php?".$headers."&id=".$id."\" method=\"post\" >
											<label for=\"number\">Saisissez le nombre de produits à commander</label>
											<input type=\"number\" name =\"nb\" id =\"number\" min=\"1\" max=\"1000\" required>
											<input type=\"submit\" value=\"Ajouter au panier\" >
										</form>							
									</li>
								</ul>
							</div>";
		}
		return $output;
	}
	
	/**
	* Fonction qui affiche le contenu du panier
	* @param $headers nom et prenom d'utilisateur
	* @return $output une structure html du panier
	*/
	
	function show_cart(){		
		if(empty(read_file(CARTJSON))) {
			echo "<p>Le panier est vide</p>";
		}
		else {
			//Ouverture du fichier (panier)
			$array = json_to_array(CARTJSON);
			$nb_products = $array["nb_products"];
			
			$products = get_token_resource();
			$output= "<div class=\"section-recherche-div\">";
							
			
			//On cherche un produit à partir de son id
			for($i=0; $i<$nb_products; $i++) {
				$id = $array["products"][$i]["id"];
				
				if(search_product($id, $products) !== false){
					$nb = $array["products"][$i]["nb"];
					
					$product = search_product($id, $products);
					// Récupération des infos importantes du produit
					$product_title = $product["title"];
					$product_discount = $product["discountPercentage"];
					$product_price = $product["price"];
					$product_rating = $product["rating"];
					$product_brand = $product["brand"];
					$product_category = $product["category"];
					
					$discount_price = discount_price($product_price, $product_discount);
					$discount_price = number_format($discount_price, 2, ",", "");
					
					$images = $product["images"];
					$product_picture = $images[0];
				
					$output .= "	<div class=\"item\">
										<img src=\"".$product_picture."\" alt=\"".$product_title."\"/>\n
										<ul>
											<li id=\"artist\">".$product_title."</li>
											<li>Prix : <s>".$product_price."</s> <strong  style=\"color: red\">".$discount_price."</strong></li>
											<li>Note utilisateur : ".$product_rating."</li>
											<li>Marque : ".$product_brand."</li>
											<li>Catégorie : ".$product_category."</li>
											<li>Quantité : ".$nb."</li>
										</ul>
									</div>";
				}
			}
		$output .= "</div>";
		}
		return $output;
	}
	
	/**
	* Fonction qui insert une commande dans la base de données
	* @param $id_manager
	* */
	function insert_commande(string $id_manager){

		//Connexion à la Base de données		
		$connect = connect_server_web();

		//Génération d'un $id de commande
		$id_commande = generate_id("SELECT id_commande FROM commande");
		
		//Extraction des produits dans le fichier qui contient tous les produits		
		$array = get_token_resource();
		// ex: $id = 'C00000007'
		//Préparation de la commande
		
		/*
		INSERT INTO Commande VALUES (id_commande, date_commande);
		INSERT INTO Produit VALUES (id_produit, nom_p, description, prix, allee, categorie, marque, url_image, pourcent_promo, note_client)
		INSERT INTO Livraison VALUES (id_livraison, date_envoi , date_recep_prevue, date_recep, #id_manager,#id_commande);
				
		
		*/
		
		//insertion de "commande"
		$request1 = "INSERT INTO Commande VALUES ('".$id_commande."', now());";
		if(pg_query($connect, $request1)){
			//On récupère les produits du panier 
			$tmp = json_to_array(CARTJSON);
			$products = $tmp["products"];
			$nb_products = $tmp["nb_products"];
			// On récupère les catégories
			$categories = json_decode(read_file("data/categories.json"));
			
			//On récupère le nombre de produits pour gérer la livraison plus tard
			$nb_exemp_produits = 0;
			
			for($i=0; $i<$nb_products; $i++) {
			//On récupère les id et nb de chaque produit
				$cart_element = $products[$i];
				
				$id = $cart_element["id"];
				$nb = $cart_element["nb"];
				
				$nb_exemp_produits += $nb;
	
	
				//On récupère le produit depuis le fichier PRODUCTSJSON
				$product = search_product($id, $array);
				if(generate_id_product($product["id"])) {
					//Insertion produit
					$request = "INSERT INTO Produit(id_produit, nom_p, description, prix, allee, emplacement, marque,
					url_image, pourcent_promo, note_client, categorie) VALUES (";
					$id_produit = generate_id_product($product["id"]);
					$nom_p = $product["title"];
					$description = $product["description"];
					$prix = $product["price"];
					$categorie = $product["category"];
					$allee = array_search($categorie, $categories);
					$emplacement = get_emplacement();
					$marque = $product["brand"];
					
					$images = $product["images"];				
					$url_image = $images[0];
					
					$pourcent_promo = $product["discountPercentage"];
					$note_client = $product["rating"];
					
					
					$request .= "'".$id_produit."', '".$nom_p."', '".$description."', ".$prix.", '".$allee."', '".$emplacement
									."', '".$marque."', '".$url_image."', ".$pourcent_promo.", ".$note_client.", '".$categorie."');";
					echo $request;
					
					if(!pg_query($connect, $request)){
						return false;
					}
				}
				else {
					$id_produit = "p".$product["id"];		
				}
				
				
				#INSERT INTO satisfait VALUES(#id_produit,#id_fournisseur,#id_commande, nb_produits);
							
				$request2 = "INSERT INTO satisfait VALUES('".$id_produit."', '".get_fournisseur()."', '".$id_commande."', ".$nb.");";
				if(pg_query($connect ,$request2)) {
				
					#INSERT INTO Livraison VALUES (id_livraison, date_envoi , date_recep_prevue, date_recep, #id_manager,#id_commande);
					$tmp = $nb_exemp_produits;
					$livraisons = 0; 
					//On crée un tableau qui contient le nombre de livraison
					while($tmp > 0) {
						$livraisons++;
						$request3 = request_livraison($id_commande, $id_manager);
						if(!pg_query($connect ,$request3)) {
							return false;						
						}
						$tmp -=100;
					}
					return true;
				}
				else {
					return false;
				}
			}
		}		
		else {
			return false;
		}
	}
	
	
	
	/**
	* Fonction qui réalise une requête de livraison
	* @param $id_commande
	* @param $id_manager
	* @return $request requête de livraison
	* */

	function request_livraison(string $id_commande, string $id_manager) {
		
		
		#INSERT INTO Livraison VALUES (id_livraison, date_envoi , date_recep_prevue, date_recep, #id_manager,#id_commande);
		
		
		/******* id_livraison***********/
		//Géneration d'un id aléatoire
		$id_livraison = generate_rand_id("SELECT id_livraison FROM livraison");
		
		
		/******* date_envoi********/
		$order_request = "SELECT date_commande from commande WHERE id_commande='".$id_commande."';"; 
		//On récupère la date de la commande
		$date_commande = (get_request_result($order_request))[0]["date_commande"];
		
		$month = intval(substr($date_commande, 5, 2));
		$day = intval(substr($date_commande, 8, 2));
		$year = intval(substr($date_commande, 0, 4));
		$date_envoi = date("Y-m-d",mktime(0, 0, 0, $month, $day+2, $year));
		/****** date_recep_prevue*****/
		$date_recep_prevue = date("Y-m-d",mktime(0, 0, 0, $month, $day+9, $year));
		
		$request = "INSERT INTO Livraison VALUES ('".$id_livraison."', NULL, '".$date_envoi."'
		, '".$date_recep_prevue."', '".$id_manager."', '".$id_commande."');";
		
		return $request;
	}


	/**
	* Fonction qui insère une palette dans la base de données
	* @param $id_liv l'id de la livraison
	* */

	function insert_palette(string $id_pal, string $zone, string $id_recep, string $id_liv) {
		$connect = connect_server_web();
		$request = "INSERT INTO palette(id_palette, zone_pre_stockage, id_receptionnaire, id_livraison) VALUES
						('".$id_pal."', '".$zone."', '".$id_recep."', '".$id_liv."')";
		if(@pg_query($connect, $request)){
			return true;
		}
		return false;
	}

	/**
	* Fonction qui valide une livraison
	* @param $id_liv id de livraison
	* */
	function validate_delivery(string $id_liv) {
		$connect = connect_server_web();
		$request= "UPDATE livraison SET date_recep = now() WHERE id_livraison='".$id_liv."';";
		if(@pg_query($connect, $request)){
			return true;
		}
		return false;	
	}













	
	
?>