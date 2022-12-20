psql -h postgresql-saber07.alwaysdata.net -U saber07_gs -W -d saber07_gestion_de_stock --


-- Add a new column 'stock' to table 'produit' in schema 'public'
ALTER TABLE produit
    ADD stock  int DEFAULT 0;

-- Add a new column 'stock' to table 'produit' in schema 'public'
ALTER TABLE produit
    ADD pays  int DEFAULT NULL;