#coding:utf-8
# sourcery skip: assign-if-exp, use-named-expression
import socket
from mode import main, vente, stock, prestock
import time
import datetime
import re
import json 
    

def check(code, reg, conn):
    """
    Fonction qui check si un code est non prévu par le protocole applicatif puis le trace et le rejette
    :param code: représente le code injecté a traité
    :param reg: représente l'expression require qui permet de savoir si une entrée est prévu ou pas
    """
    valid = re.findall(reg, code)
    if (not valid):
        with open("log",'a',encoding = 'utf-8') as f:
            f.write(f"{datetime.datetime.now()}\t(ip, port) = {adr} a envoyé du code non prévu : {code} \n")
            f.close()
        conn.close()

adress, port = ('',5005)

with open('conf.json') as f:
    conf = json.load(f)

    # configuration reseau
    if("socket" in conf):
        if("ip" in conf["socket"]):
            ip = conf["socket"]["ip"]
        else:
            print("erreur, parametre ip manquant dans le fichier de configuration")
            exit()
        if("port" in conf["socket"]):
            port = int(conf["socket"]["port"])
    else:
        print("parametre \"socket\" manquant dans la fichier de configuration")

#gestion port deja utilisé
try : 
    """
        Création de la socket
    """
    Socket = socket.socket(socket.AF_INET,socket.SOCK_STREAM)
    Socket.bind((adress,port))
    print("le serveur a démmaré...")
except socket.error as error:
    if error.errno == 48:
        print("Impossible de lancer le serveur, le port est deja utilisé")
        Socket.close()
        exit()
    else:
        raise

while True:
    """
        Début de notre échange réseau
    """
    #time.sleep(5)
    Socket.listen()
    conn, adr = Socket.accept()
    conn.settimeout(100)

    """ 1er échange 's'identifier """

    """ Réception de l'id de l'employé """

    id_emp = conn.recv(10)
    id_emp = id_emp.decode("utf-8").replace('\n','')
    print(f"[CLIENT]: {id_emp}")
    check(id_emp, "[cmr]\d+", conn)



    if(id_emp == ""):
        print("client deconnecte\n")
        continue


    """ L'envoie du message de bienvenu si l'employé est présent dans la BD"""
    id_emp=id_emp.replace('\n','')
    data=str(main.sidentifier(id_emp))
    print(f"[SERVER]: {data}") 

    if(data[0]=="0"):
        conn.close()
        continue

    #--------------------------------------

    """On attribue un mode a notre employé selon la codification de son id, afin d'utiliser le mode qui lui est adapté"""


    if (id_emp.startswith("c")):

        """  Mode vente (si l'employé est caissier) """
        data = f"{data} vous etes dans le mode vente\n" 
        conn.send(data.encode("utf-8"))
        f = '%Y-%m-%d %H:%M:%S'
        date = time.localtime()
        date = time.strftime(f, date)
        total = 0

        while True:
            """ Réception des produit scanné par le caissier """

            code = conn.recv(30)
            code = code.decode("utf8").replace('\n','')
            if(code == ""):
                print("client deconnecte")
                break
            print(f"[CLIENT]: {code}")
            check(code, "(p\d+)|(FIN)", conn)

            """ Envoie des information du produit scanné, à la reception de 'FIN', on arrête l'échange """
            if (code != "FIN"):
                """Si le caissier n'a pas terminé on renvoie les infos des produis scannés"""
                print(date)
                data = vente.vente(code, id_emp, date)

                if(not data):
                    """si on trouve pas le produit dans la base de donnée"""
                    data = "Erreur, produit introuvable"
                else:
                    """sinon on calcule le total"""
                    total = total + float(data[1])
                    data = str(data)
            else:
                """Si le client envoie 'FIN', on affiche le total"""
                data = f"Le total est :{str(total)}"
                conn.send(data.encode("utf-8"))
                break

            print(f"[SERVER]: {data}")
            conn.send(data.encode("utf-8"))

    elif (id_emp.startswith("m")):

        """ Mode stock (si l'employé est un manutentionnaire) """
        data = f"{data} vous etes dans le mode stock\n"
        conn.send(data.encode("utf-8"))

        """ Reception de la palette """
        pal = conn.recv(30)
        pal = pal.decode("utf8").replace('\n','')
        if(pal == ""):
            print("client deconnecte")
            continue
        print(f"[CLIENT]: {pal}")
        check(pal, "P[0-9]+", conn)

        """ Envoie des informations de la palette """
        data = str(stock.selectionner(pal, id_emp))
        print(f"[SERVER]: {data}")
        conn.send(data.encode("utf-8"))

        if(data=="palette selectionnee"):
            while(True):
                """ Réception des produit a scanné """
                prod = conn.recv(30)
                prod = prod.decode("utf8").replace('\n','')
                if(prod == ""):
                    print("client deconnecte")
                    break
                print(f"[CLIENT]: {prod}")
                check(prod, "(p\d+)|(FIN)", conn)

                if prod=="FIN":
                    data="OK"
                    print(f"[SERVER]: {data}")
                    conn.send(data.encode("utf-8"))
                    break

                """ Envoie  des informations des produits  """
                data = str(stock.stocker(pal ,prod))
                print(f"[SERVER]: {data}")
                conn.send(data.encode("utf-8"))


    elif (id_emp.startswith("r")):
        """ Mode pre-stock (pour les réceptionnaires) """
        data = f"{data} vous etes dans le mode prestock"
        conn.send(data.encode("utf-8"))

        """ Réceptions des palettes  """
        pal = conn.recv(30)
        pal = pal.decode("utf8").replace('\n','')
        if(pal == ""):
            print("client deconnecte")
            continue
        print(f"[CLIENT]: {pal}")
        check(pal, "P[0-9]+", conn)

        if prestock.responsable(pal, id_emp):
            """  Envoyer la question sur l'état de la palette  """
            data = "La palette est-elle endommagée ?"
            print(f"[SERVER]: {data}")
            conn.send(data.encode("utf-8"))

            """ Réception de la réponse """
            data = conn.recv(5)
            data = data.decode("utf8").replace('\n','')
            if(data == ""):
                print("client deconnecte")
                continue
            print(f"[CLIENT]: {data}")
            check(data, "oui|non", conn)

            if (data=="oui"):
                data = "Information enregistré"
                prestock.est_endommage(pal)
            else:
                """ Envoie des information de la palette  """
                data = str(prestock.emplacement(pal))

        else:
            data = "vous n'etes pas chargé de cette palette"

        print(f"[SERVER]: {data}")
        conn.send(data.encode("utf-8"))

    else:
        print("employe non reconnu")

    conn.close()
Socket.close()