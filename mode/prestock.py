import psycopg2
from mode import main

def responsable(id_pal, id_emp):
    """
    :param id_pal: id du produit
    :param id_emp: id du receptionnaire
    :return: retourne le receptionnaire responsable de la palette
    """
    con = None
    res = False
    try:
        con = main.connexion_db()
        cur = con.cursor()

        cur.execute("SELECT id_receptionnaire from palette where id_palette = %s", (id_pal,))
        
        recp=str(cur.fetchone())
        recp = recp.replace('(','')
        recp = recp.replace(')','')
        recp = recp.replace(',','')
        recp = recp.replace("'",'')
        recp = recp.replace(' ', '')

        if(recp == id_emp):
            res = True

        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()
    return res

def emplacement(id_pal):
    """
    :param id_pal: id de la palette
    :return: retourne la zone de pré-stockage si la palette existe
                sinon retourne le message d’erreur “Palette n'existe pas”

    """
    con = None
    rows = None
    try:
        con = main.connexion_db()
        cur = con.cursor()
        cur.execute("SELECT zone_pre_stockage from palette where id_palette = %s", (id_pal,))
        rows=str(cur.fetchone())
        rows = rows.replace('(','')
        rows = rows.replace(')','')
        rows = rows.replace(',','')
        rows = rows.replace("'",'')
        rows = rows.replace(' ', '')

        if (rows is None):
            rows = "Palette n'existe pas"

        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()
    return rows


def est_endommage(id_pal):
    """
    Met à jour la BD si la palette est déclarée comme endommagée
    :param id_pal: id de la palette
    """
    con = None
    try:
        con = main.connexion_db()
        cur = con.cursor()
        cur.execute("UPDATE palette SET est_endommage='TRUE' where id_palette = %s", (id_pal,))
        con.commit()
        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()