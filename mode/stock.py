import psycopg2
from mode import main


def selectionner(id_palette, id_manu):
    """
    Afin de permettre au manutentionnaire de sélectionner la palette si elle existe dans la base de donné,
    On vérifie une palette si elle existe dans la base de donnée et la sélectionne et l’affect a au manutentionnaire
    qui l’a scanné en l’ajoutent dans la table stock sinon renvoie un message d’erreur.
    :param id_palette: id de la palette
    :param id_manu: identifiant du manutentionnaire
    :return: si la palette existe dans la base de donnée ou pas.
    """
    con = None
    rows = None
    try:
        con = main.connexion_db()
        cur = con.cursor()
        cur.execute("SELECT * FROM palette WHERE id_palette=%s",(id_palette,))
        rows = cur.fetchone()
        if rows is None:
            return "Erreur, palette introuvable"

        cur.execute("SELECT * FROM stocke WHERE id_palette=%s",(id_palette,))
        rows2 = cur.fetchone()
        if rows2 is not None:
            return "Palette deja stocke"

        cur.execute("INSERT INTO stocke VALUES(%s, %s)", (id_manu, id_palette))
        con.commit()
        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
        return "vous avez deja scanne la palette"
    finally:
        rows = "palette selectionnee"

        if con is not None:
            con.close()

    return rows

def stocker(id_palette, id_produit):
    """
     L'allée et l’emplacement du stockage du produit et ajoute le produit dans la table “Contient” si elle n'existe pas,
    sinon incrément le nombre d’exemplaires contenu si la palette ou le produit n’existe pas ça retourne un message d’erreur
    :param id_palette: id de la palette
    :param id_produit: id du produit
    :return: l’allée et l’emplacement du stockage du produit
    """
    con = None
    rows = None
    try:
        con = main.connexion_db()
        cur = con.cursor()
        cur.execute("SELECT id_produit FROM contient WHERE id_palette=%s And id_produit=%s", (id_palette, id_produit))
        rows = cur.fetchone()

        if rows is None:
            cur.execute("INSERT INTO contient VALUES(%s, %s, 1)", (id_palette, id_produit))
        else:
            cur.execute("UPDATE contient SET nb_exemp_contenu=nb_exemp_contenu+1 WHERE id_palette=%s And id_produit=%s", (id_palette, id_produit))

        con.commit()
        
        cur.execute("SELECT allee, emplacement FROM produit WHERE id_produit=%s", (id_produit, ))
        rows = cur.fetchone()
        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()

    return rows