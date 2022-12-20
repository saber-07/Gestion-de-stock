import psycopg2
from mode import main

def vente(id_produit, id_emp, date):
    """
    Insert le produit s’il n'existe pas dans la table vend sinon il incrémente son nombre
    (si le produit inséré n’existe pas dans la base de donnée ça retourne un message d’erreur)
    :param id_produit: id du produit
    :param id_emp: id du caissier
    :param date: date et heure du début de la vente
    :return: les informations du produit scanné
    """
    con = None
    rows = None
    if id_produit!= "FIN":
        try:
            con = main.connexion_db()
            cur = con.cursor()
            cur.execute("SELECT * FROM Vend WHERE (id_produit = %s AND id_caissier = %s AND date_vente = %s)", (id_produit, id_emp, date))
            rows = cur.fetchone()

            existe = prod_existe(id_produit)

            if existe:
                if rows is None:
                    cur.execute("INSERT INTO Vend VALUES(%s, %s, 1, %s)", (id_emp, id_produit, date))
                else:
                    cur.execute("UPDATE Vend SET nb_exemp_vendu=nb_exemp_vendu+1 WHERE id_produit = %s AND id_caissier = %s", (id_produit, id_emp))
                con.commit()

                cur.execute("SELECT nom_p, prix FROM Produit WHERE id_produit=%s", (id_produit,))
                rows = cur.fetchone()
                cur.close()
            else:
                rows = existe

            print(rows)
                
        except (Exception, psycopg2.DatabaseError) as error:
            print(error)
        finally:
            if con is not None:
                con.close()
    return rows


def prod_existe(id_produit):
    """
    :param id_produit:  id du produit
    :return: retourne si le produit existe dans la base de donnée ou pas
    """
    con = None
    try:
        con = main.connexion_db()
        cur = con.cursor()
        cur.execute("SELECT * from produit where id_produit = %s", (id_produit,))
        return bool(cur.fetchone())
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()