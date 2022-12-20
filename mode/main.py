import psycopg2
import json

def connexion_db():
    """
     Met en place une connexion à la base de donnée
    """
    with open('conf.json') as f:
        conf = json.load(f)
        # recuperer les informations de connexion a la BD et au server du fichier de configuration
        if("psql" in conf):
            if(("host" in conf["psql"]) and ("database" in conf["psql"]) and ("user" in conf["psql"]) and ("password" in conf["psql"])):
                host = conf["psql"]["host"]
                database = conf["psql"]["database"]
                user = conf["psql"]["user"]
                password = conf["psql"]["password"]
            else:
                print("erreur, parametres de psql manquants dans le fichier de configuration")
                exit()
        else:
            print("parametre \"psql\" introuvable dans le fichier de configuration")
            exit()
        
    return psycopg2.connect(host=host, 
                            database=database, 
                            user=user,
                            password=password)




def sidentifier(id):
    """
    Nous permet de determiner le mode adapté de l’utilisateur identifié
    :param id: Identifiant de l'utilisateur
    :return: un message de bienvenue.
    """
    con = None
    rows = None

    try:
        con = connexion_db()
        cur = con.cursor()
        cur.execute("SELECT id_employe FROM employe where id_employe=%s", (id,))
        rows = cur.fetchone()

        if rows is None:
            rows = "0"
        else:
            cur.execute("SELECT nom, prenom FROM employe where id_employe=%s", (id,))
            rows = cur.fetchone()
            rows = str(rows)
            rows = rows.replace('(','')
            rows = rows.replace(')','')
            rows = rows.replace(',','')
            rows = rows.replace("'",'')
            rows = f"Bonjour {rows}"
        
        cur.close()
    except (Exception, psycopg2.DatabaseError) as error:
        print(error)
    finally:
        if con is not None:
            con.close()

    return rows
