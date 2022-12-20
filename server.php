<?php

$username = "user=saber07_gs";
$password = "password=Gestion2Stock";
$host = "host=postgresql-saber07.alwaysdata.net";
$database = "dbname=saber07_gestion_de_stock";
$port = "port=5432";

$con_string = "$host  $port $database $username $password";
$connect = pg_connect($con_string);
if (!$connect) {
    echo "Error : Unable to open database\n";
  }

$result = pg_query($connect, "select id_produit from produit");
if (!$result) {
  echo "Une erreur s'est produite.\n";
  exit;
}

//var_dump(pg_fetch_all($result));
while ($row = pg_fetch_row($result)) {
  echo "produit: $row[0]\n";
}
  
?>