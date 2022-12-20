<?php
		
	//Définition de la constante TOKENFILE qui désigne le fichier ou on enregistre le token de l'API dummy JSON
	DEFINE("TOKENFILE", "./data/token.txt");
	//Définition de la constante PRODUCTSJSON qui désigne le fichier ou on enregistre les produits de l'API dummy JSON
	DEFINE("PRODUCTSJSON", "./data/products.json");
	//Définition de la constante CARTJSON qui désigne le fichier ou on enregistre le panier des produits 
	DEFINE("CARTJSON", "./data/cart.json");
	
	/**
	* Fonction qui écrit dans un fichier txt
	* @param $filepath le chemin du fichier
	* @param $write texte à ajouter au fichier
	* */
	
	function write_file(string $filepath, string $write) : void {
		if($fichier=@fopen($filepath,"c+b")){
			fwrite($fichier ,$write);
			fclose($fichier);		
		}
		else {
			echo "Erreur d'ouverture de fichier";
		}
	}
	
	/**
	* Fonction qui efface le contenu d'un fichier txt
	* @param $filepath le chemin du fichier
	* */
	
	function empty_file(string $filepath) : void { 		
		if(!empty(file_get_contents($filepath))){
			$fichier = fopen($filepath, "w");
			fclose($fichier);
		}
		else {
			echo "Erreur d'ouverture de fichier";
		}
	}	
	
	/**
	* Fonction qui lit un fichier txt
	* @param $filepath le chemin du fichier
	* @return $output contenu du fichier
	* */
	
	function read_file(string $filepath) {
		// Le @ permet de ne pas afficher l'erreur et renvoie le statut que nous définissons en dessous
		$output = false;		
		if(!empty(file_get_contents($filepath))){
			$output = file_get_contents($filepath);
		}
		return $output;
	}
	
	/**
	* Fonction qui récupère le flux JSON à l'aide de la fonction file_get_contents()
	*
	* @param $url l'url du flux JSON
	* @return $array qui contient les données brutes du flux JSON
	* */
	
	function get_JSON_array(string $url) : array {
		//On transforme la chaine de caractères récuperée en un tableau associatif
		
		ini_set("allow_url_fopen", true);
		
		$json = file_get_contents($url, true);
		
		$array = json_decode($json, true);
		
		return $array;
	}
	
	
	/**
	* Fonction qui génère un Token pour accéder à l'API DummyJson
	* @return $result un objet de type PgSql\Connection
	* */
	
	function first_connection() {
		//Récupérer l'utilisateur 0
		$url_users = "https://dummyjson.com/users/22";
		$user = get_JSON_array($url_users);
		
		$options = array(
			"username"=> $user["username"],
			"password"=> $user["password"]
			);
		
		$params = json_encode($options);
		$ch = curl_init("https://dummyjson.com/auth/login");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		return $result;
	}
	
	
	/**
	* Fonction qui récupère un token et le stocke dans un fichier
	* */	
	
	function generate_save_token() : void {
		$connect = first_connection();
		
		$array = (array)json_decode($connect);
		
		$token = $array["token"];
		
		
		if(empty(read_file(TOKENFILE))){
			write_file(TOKENFILE, $token);	
		}
		else {
			empty_file(TOKENFILE);
			write_file(TOKENFILE, $token);
		}
	}
	
	/**
	* Fonction qui indique si un token est expiré
	* @return $token un token 
	* */
	
	function is_token_expired(string $filetoken) : bool {
		$token = read_file($filetoken);
		
		$url = "https://dummyjson.com/auth";
		
		$ch = curl_init($url);	
		
		$options = array(
			'Content-type: application/json',
			'Authorization: Bearer '.$token
			);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		$message = (array)json_decode($result);
		curl_close($ch);
		
		$boolean = false;
		
		if(isset($message["message"]) && (strcmp($message["message"], "Token Expired!") == 0)){
			$boolean = true;
		}
		return $boolean;
	}
	
	
	
	/**
	* Fonction qui récupère une ressource si un token est expiré
	* @param $resource resource de l'API
	* @param $filepath chmin d'un fichier json
	* @return $array un tableau contenant la ressource extraite. 
	* */
	
	function get_token_resource(string $resource = "products", string $filepath = PRODUCTSJSON) : array {
		if(empty(read_file($filepath)) || is_token_expired(TOKENFILE)){
			generate_save_token();
			$token = read_file(TOKENFILE);
	
			$url = "https://dummyjson.com/auth/".$resource."?limit=100";
			
			$ch = curl_init($url);	
			
			$options = array(
				'Content-type: application/json',
				'Authorization: Bearer '.$token
				);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			write_file($filepath, $result);
			curl_close($ch);
		}
		$array = (array)json_decode(read_file($filepath));
		
		return $array;
	}
	
	
	/**
	* Fonction qui se connecte à la BD
	* @return une ressource de type PgSql\Connection
	* */
	
	function connect_server_web() {
		$username = "user=saber07_gs";
		$password = "password=Gestion2Stock";
		$host = "host=postgresql-saber07.alwaysdata.net";
		$database = "dbname=saber07_gestion_de_stock";
		$port = "port=5432";
		
		$con_string = $host." ".$port." ".$database." ".$username." ".$password;
		$connect = pg_connect($con_string);
		return $connect;
	}
	
	/**
	* Fonction qui récupère le resultat d'une requête SQL avec les fonction de PgSql
	* @param $request une requête SQL dans la syntaxe de Postgres SQL
	* @return $result un tableau associatif contenant le résultat de la requête
	* */
	
	function get_request_result(string $request) : array {
		$connect = connect_server_web();
		if($connect== 0){
			echo "<p>Error : Unable to open database</p>\n";
			return array("");
		}
		$tmp = pg_query($connect, $request);
		$result = pg_fetch_all($tmp);
		if($result == false){
			$result = array();
		}
		return $result;
	}
	
	/**
	* Fonction qui encrypte un mot de passe
	* @param $password un mot de passe
	* @return $encrypted_password mot de passe encrypté
	* */
	
	function encrypt_password(string $password) : string {

		$md5 = md5($password);

		$encrypted_password = crypt($password, '$1$'.$md5.'$');

		return $encrypted_password;
	}
	
	/**
	* Fonction qui cherche un produit avec son id dans un tableau de produits
	* @param $value valeur à chercher
	* @return $array tableau qui représente les détails d'un produit
	* */
	
	function search_product(int $value, array $array) : array{
	$i=0;
		$limit = $array["limit"];
		
		$array_products = $array["products"];
		$array = (array)$array_products[$i];
		
		while(($i < $limit) && $value != $array["id"]) {
			var_dump($value != $array["id"]);
			$array = (array)$array_products[$i];
			$i++;
		}
		if($i >= $limit){
			echo "Cet élement n'existe pas dans la base de données";
			return false;
		}
		return (array)$array;
	}
	
	/**
	* Fonction qui calcule le prix baissé avec une promotion d'un produit
	* @param $original_price prix original
	* @param $discount_percentage pourcentage de promotion (1< x < 99)
	* @return $discount_price prix avec la promotion
	* */
	
	function discount_price(float $original_price, float $discount_percentage) : float {
		$discount_price = $original_price*((100 - $discount_percentage)/100);
		return $discount_price;
	}
	
	
	
	/**
	* Fonction qui compare les ID d'une table et génère un ID plus grand que max(ID)
	* @param $request une requête PgSql
	* @return $new_id id max+1
	* */
	
	function generate_id(string $request) : string{
		$result = get_request_result($request);
		$array_id = array();
		$integer_id = array();
		
		for($i=0; $i<count($result); $i++){
			$array = $result[$i];
			foreach($array as $key => $value){
				$array_id[$i] = $value;
			}
		}
		
		$first_char = substr($array_id[0], 0, 1);
		$len_string = strlen($array_id[0]);
		
		//Séparation des IDs
		for($i=0; $i<count($array_id); $i++) {
			$id =	$array_id[$i];
			//construction d'un tableau d'entiers
			$sub_string = strtok($id, $first_char);
			$integer_id[$i] = intval($sub_string);
		}
		//On extrait le max
		$tmp = max($integer_id);
		//Géneration du nouveau id
		$new_id = $first_char;
		//
		$integer_id_new = $tmp + 1;

		$length = 9;
		$new_id .= substr(str_repeat(0, $length).$integer_id_new, - $length);
		
		return $new_id;
		
	}
	
	/**
	* Fonction qui génère un id_produit d'un produit
	* @param $request une requête PgSql
	* @return $new_id id d'un produit
	* */
	
	function generate_id_product(string $id_product) : string{
		$request = get_request_result("SELECT id_produit FROM produit WHERE id_produit='p".$id_product."';");
		if($request){
			return false;
		}
		
		//Géneration du nouveau id
		$new_id = 'p'.$id_product;
		return $new_id;
		
	}
	
	/**
	* Fonction qui ajoute un nombre de produits d'un type de produit dans un fichier JSON
	* @param $id l'id du produit
	* @param $nb le nombre d'exemplaires du produit
	* */
	
	
	function add_to_cart(int $id, int $nb) : void{
		if(empty(read_file(CARTJSON))){
			$array_init = json_encode(array("products" => array(), "nb_products" => 0));
			write_file(CARTJSON, $array_init);
		}
		
		$tmp = read_file(CARTJSON);
		$array = (array)json_decode($tmp);
		
		// On récupère le nombre de produits
		$nb_products = $array["nb_products"];
		
		// On récupère le tableau du fichier json
		$products = (array)$array["products"];
		
		//Construction d'un tableau à partir d'une structure json
		$tmp = array();
		for($i=0; $i<$nb_products; $i++) {
			$tmp[$i] = (array)$products[$i];
		}
		
		//Boucle qui parcours le tableau à la recherche de l'existence de l'id 
		$i=0;
		while($i<$nb_products && $tmp[$i]["id"] != $id){
			$i++;
		}
		
		// Si le produit existe dans le tableau
		if($i < $nb_products) {
			$tmp[$i]["nb"] += $nb;
			for($i=0; $i<$nb_products; $i++) {
				$products[$i] = $tmp[$i];
			}
			$array["products"] = $products;
		}
		// Si le produit n'existe pas dans le tableau
		else {
			//création de la ligne produit		
			$product = array("id" => $id, "nb" => $nb);		
			
			//Ajout du produit dans la table des produits
			$array["products"][$nb_products] = (array)$product;
	
			//Incrémentation du nombre de produit dans le panier
			$array["nb_products"] = $nb_products + 1;
			
		}
		//On encode en JSON pour écrire dans le fichier
		$array_json = json_encode($array);
		
		// On remplace le contenu du fichier par le nouveau
		empty_file(CARTJSON);
		write_file(CARTJSON, $array_json);
	}
	
	/**
	* Fonction qui convertit le fichier cart.json en tableau 
	* @param $cartfile le fichier json "panier"
	* @return $array un tableau géneré depuis un fichier json
	* */
	
	function json_to_array(string $cartfile = CARTJSON) : array {
		$json = read_file($cartfile);
		$array = (array)json_decode($json);

		$products = (array)$array["products"];
		$nb_products = $array["nb_products"];
		$tmp = array();
		
		for($i=0; $i<$nb_products; $i++) {
			$tmp[$i] = (array)$products[$i];
		}
		
		
		
		for($i=0; $i<$nb_products; $i++) {
			$products[$i] = $tmp[$i];
		}
		
		$array["products"] = $products;
		
		return $array;
		
	}
	
	/**
	* Fonction qui calcule l'emplacement d'un produit
	* @return $emplacement un emplacement libre
	*/
	function get_emplacement() {
	
		$request = "SELECT emplacement FROM produit";
		
		
		$result = get_request_result($request);
		$array_emp = array();
		
		for($i=0; $i<count($result); $i++){
			$array = $result[$i];
			foreach($array as $key => $value){
				$array_emp[$i] = $value;
			}
		}
		
		//On extrait le max
		$max = max($array_emp);
		
		$new_emp = $max + 1;
		
		return $new_emp;
	}
	
	
	/**
	* Fonction qui sélectionne un id_fournisseur au hasard
	* @return $id_fournisseur
	* */
	
	function get_fournisseur(){
	
		$request = "SELECT * FROM fournisseur ORDER BY RANDOM() LIMIT 1;";
		
		$connect = connect_server_web();
		$result = pg_fetch_all(pg_query($connect, "SELECT id_fournisseur FROM fournisseur ORDER BY RANDOM() LIMIT 1"));
		
		return $result[0]["id_fournisseur"];
	}
	
	
	/**
	* Fonction qui compare les ID d'une table et génère un ID aléatoire
	* @param $request une requête PgSql
	* @return $new_id id aléatoire
	* */
	
	function generate_rand_id(string $request){
		$result = get_request_result($request);
		$array_id = array();
		$integer_id = array();
		
		for($i=0; $i<count($result); $i++){
			$array = $result[$i];
			foreach($array as $key => $value){
				$array_id[$i] = $value;
			}
		}
		
		$first_char = substr($array_id[0], 0, 1);
		$len_string = strlen($array_id[0]);
		
		//Séparation des IDs
		for($i=0; $i<count($array_id); $i++) {
			$id =	$array_id[$i];
			//construction d'un tableau d'entiers
			$sub_string = strtok($id, $first_char);
			$integer_id[$i] = intval($sub_string);
		}
		//On extrait le max
		/*$tmp = max($integer_id);*/
				
		$tmp = 30;
		while(in_array($tmp, $integer_id)){
			$tmp = rand(1, 9999);
		}
		//Géneration du nouveau id
		$new_id = $first_char;
		//
		$new_id .= $tmp;
		
		return $new_id;
		
	}
	
	
	
	
	
?>