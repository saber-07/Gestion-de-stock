#include <netinet/in.h> //structure for storing address information
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h> //for socket APIs
#include <sys/types.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <stdbool.h>
#include <regex.h>
#include <errno.h>
#include "json-c/json.h"
#include <fcntl.h>
#include <sys/select.h>
#include <poll.h>

#define PORT 5005

int connect_wait (int sockno, struct sockaddr * addr, size_t addrlen, struct timeval * timeout){ //fonction qui gere le cas d'un timeout lors de la connexion
        int res, opt;
        // recuperer les flags du socket
        if ((opt = fcntl (sockno, F_GETFL, NULL)) < 0) 
            return -1;
        
        // definir le socket sur non-blocking
        if (fcntl (sockno, F_SETFL, opt | O_NONBLOCK) < 0)
            return -1;

        // essayer de se connecter
        if ((res = connect (sockno, addr, addrlen)) < 0) {
            if (errno == EINPROGRESS) {
                fd_set wait_set;

                // make file descriptor set with socket
                FD_ZERO (&wait_set);
                FD_SET (sockno, &wait_set);

                // wait for socket to be writable; return after given timeout
                res = select (sockno + 1, NULL, &wait_set, NULL, timeout);
            }
        }
        // connection was successful immediately
        else {
            res = 1;
        }
        // reinitialiser les flags socket
        if (fcntl (sockno, F_SETFL, opt) < 0) {
            return -1;
        }
        // une erreur s'est produite lors de connect ou select
        if (res < 0) {
            return -1;
        }
        // selecionner timeout
        else if (res == 0) {
            errno = ETIMEDOUT;
            return 1;
        }
        else {
            socklen_t len = sizeof (opt);
            // verifier les erreurs dans la couche du socket
            if (getsockopt (sockno, SOL_SOCKET, SO_ERROR, &opt, &len) < 0) {
                return -1;
            }
            // si il y a eu erreur
            if (opt) {
                errno = opt;
                return -1;
            }
        }
        return 0;
    }

void check_timeout(char* chaine, int socket){ //fonction pour prendre en compte le timeout lors de la reception
	if(strcmp(chaine,"")==0){
		close(socket);
		exit(1);
	}
}

int main(int argc, char const* argv[])
{	
	FILE *fp; //pointeur qui sera utilisé pour lire le fichier JSON
    char buff[1024];
    struct json_object *parsed_json; //un objet json_object
    struct json_object *ip = NULL; //un objet json_object qui va contenir l'adresse ip lu dans le fichier de configuration
    struct json_object *port = NULL; //un objet json_object qui va contenir le port lu dans le fichier de configuration
	int p = PORT; //le port 
	char add[15]; //l'adresse ip
	
    fp = fopen("conf_c.json","r"); //ouvrir le fichier conf.json en lecture
    fread(buff,1024,1,fp); //lire le contenu du fichier et le mettre dans le buffer buff
    fclose(fp); //fermer le fichier
    parsed_json = json_tokener_parse(buff); //parser le contenu du fichier en un format json_object
    json_object_object_get_ex(parsed_json,"ip",&ip); //recuperer la propriete ip du fichier json et la mettre dans la varibale ip
    json_object_object_get_ex(parsed_json,"port",&port); //recuperer la propriete port du fichier json et la mettre dans la varibale port

	if(port != NULL){
		p = json_object_get_int(port); //convertir le port en int et le copier dans la variable add
	}
    strcpy(add,json_object_get_string(ip)); //convertir l'adresse en string et la copier dans la variable adds
	int sockD = socket(AF_INET, SOCK_STREAM, 0); //creation du socket
	struct sockaddr_in serverAddr;
    memset(& serverAddr, '\0', sizeof(serverAddr));

	//definition des variables qui vont stocker les donne envoyes par le client
    char prod[30];
	char id[10];
	char buffer[1024];
	char pal[30];
	char res[5];

	regex_t reegex; // variable qui stocke l'espression reguliere initiale
	char msgbuf[100]; // variable qui stocke le message d'erreur du regex
	int value; // variable qui stocke le retour de la fonction regcomp et regexec qui verifie si la donne du client corresspond a une expression reguliere definie selon le cas

	serverAddr.sin_family = AF_INET; //definition de la famille d'adresse du socket
	serverAddr.sin_port= htons(p); //definition du port
    serverAddr.sin_addr.s_addr = inet_addr(add); //definition de l'adresse 

	struct timeval timeout; //structure pour le timeout
    timeout.tv_sec  = 60;  // apres 60 seconds connect_wait() va etre timeout
    timeout.tv_usec = 0;

    int connectStatus = connect_wait(sockD, (struct sockaddr*)&serverAddr, sizeof(serverAddr), &timeout);

	//definition du timeout pour la reception et l'envoi
    if (setsockopt (sockD, SOL_SOCKET, SO_RCVTIMEO, &timeout, sizeof timeout) < 0)
        printf("setsockopt failed\n");

    if (setsockopt (sockD, SOL_SOCKET, SO_SNDTIMEO, &timeout,sizeof timeout) < 0)
        printf("setsockopt failed\n");

	if (connectStatus == -1){ //gestion des erreurs de connexion 
		if(errno == 61){
			printf("erreur, port ferme\n");
		}else if(errno == 51){
			printf("erreur, ip non atteignable\n");
		}else{
			printf("numero de l'erreur %d\n",errno);
		}
        exit(0);
    }else if(connectStatus == 1){
		printf("connexion trop lente ...");
	}

	else {
        printf("[+]Connected to Server.\n");
		/* S'identifier */
		printf("identifiez-vous :\n");
		fgets(id, 10, stdin); //recuperer la reponse du client
		
		//verifier l'id de l'employe
		value = regcomp( &reegex, "^[m|c|r][0-9]*", 0); // fonction pour verifier les regex
		value = regexec( &reegex, id, 0, NULL, 0); // verifier l'id avec l'expression reguliere

		if (value == 0) // Si l'expression match avec la regex
			send(sockD, id, strlen(id), 0);
		else if (value == REG_NOMATCH) { // sinon
			printf("Valeur non valide.\n");
			close(sockD); //on ferme la connexion
			exit(1); //on quitte le programme
		}

		// Recv data
		memset(buffer, '\0', sizeof(buffer)); //vider le buffer
		recv(sockD, buffer, 1024, 0);
		check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
		buffer[strcspn(buffer, "\n")] = 0;

		if(buffer[0] == '0'){ //si la reponse du server commence avec un 0, alors l'identifiant de l'employe n'a pas ete reconnu
			printf("Employe non reconnu, veuillez vous reconnecter");
			close(sockD); //on ferme la connexion et on quitte
			exit(1);
		}
		printf("Reponse : '%s'\n",buffer);
		if(id[0] == 'c') //si l'identifiant de l'employe commence avec un c, alors c'est un caissier, donc mode vente
		{
			/* Mode vente */
			while(1){
				// scanner et envoyer l'id du produit 
				printf("entrez un produit:\n");
				fgets(prod, 30, stdin);
				prod[strcspn(prod, "\n")] = 0; //supprime les caractere de saut de ligne 

				if(strcmp(prod, "FIN")==0){ //si fin d
					send(sockD, prod, strlen(prod), 0);
					// Recv info prod
					memset(buffer, '\0', sizeof(buffer)); //vider le buffer
					recv(sockD, buffer, 1024, 0);
					check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
					buffer[strcspn(buffer, "\n")] = 0;
					printf("%s\n", buffer);
					break;
				}	
				//verifier le format de l'id du produit
				value = regcomp( &reegex, "p[0-9]*", 0); // fonction pour verifier les regex
				value = regexec( &reegex, prod, 0, NULL, 0); // verifier le prod avec l'expression reguliere

				if (value == 0) { // Si l'expression match avec la regex
					send(sockD, prod, strlen(prod), 0); //envoyer l'id du produit
					memset(buffer, '\0', sizeof(buffer)); //vider le buffer
					recv(sockD, buffer, 1024, 0); // Recevoir info produit
					check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
					buffer[strcspn(buffer, "\n")] = 0;
					printf("Reponse : %s\n", buffer);
				}
				else if (value == REG_NOMATCH) { // sinon
					printf("Valeur non valide.\n");
					close(sockD);
					break;
				}
			} 
			printf("Fin de la connexion");
			close(sockD);
			exit(1);
		}
		else if (id[0] == 'm') //si l'identifiant de l'employe commence avec un m, alors c'est un manutentionnaire, donc mode stock
		{
			// scanner et envoyer l'id de la palette
			printf("Donner la palette: \n");
			fgets(pal, 30, stdin);
			//verifier le format de l'id de la palette
			value = regcomp( &reegex, "P[0-9]*", 0); // fonction pour verifier les regex
			value = regexec( &reegex, pal, 0, NULL, 0); // verifier l'id de la palette recu avec l'expression reguliere

			if (value == 0) { // Si l'expression match avec la regex
				send(sockD, pal, strlen(pal), 0);
			}
			else if (value == REG_NOMATCH) { // sinon
				printf("Valeur non valide.\n");
				close(sockD);
				exit(1);
			}
			memset(buffer, '\0', sizeof(buffer)); //vider le buffer
			recv(sockD, buffer, 1024, 0); //recevoir la reponse du server
			check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (le cas d'un timeout)
			buffer[strcspn(buffer, "\n")] = 0; 
			printf("Message: %s\n", buffer);
			
			if(strcmp(buffer, "palette selectionnee")==0)
				while(1){
					printf("rentrez ");
					// scanner et envoyer l'id des produits scannes
					printf("Donner le produit:\n");
					fgets(prod, 30, stdin);
					send(sockD, prod, strlen(prod), 0);  

					// Recevoir info produit
					memset(buffer, '\0', sizeof(buffer)); //vider le buffer
					recv(sockD, buffer, 1024, 0); //recevoir la reponse du server
					check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
					buffer[strcspn(buffer, "\n")] = 0;

					//si le client envoie FIN, le server lui renvoie OK et le mode stock est termine
					if((strcmp(buffer, "OK")==0)){
						printf("Reponse : %s\n",buffer);
						break;
					}
					printf("Message: (allée, emplacement)=\"%s\"\n", buffer); //affichage de la reponse du server, l'emplacement du produit
				}
			printf("Fin de la connexion");
			close(sockD);
			exit(1);
		}
		else if (id[0] == 'r') //si l'identifiant de l'employe commence avec un r, alors c'est un receptionnaire, donc mode prestock
		{
			// scanner et envoyer l'id de la palette
			printf("Donner la palette: \n");
			fgets(pal, 30, stdin);
			//verification du format de l'id de la palette
			value = regcomp( &reegex, "P[0-9]*", 0);
			value = regexec( &reegex, pal, 0, NULL, 0);

			if (value == 0) {
				send(sockD, pal, strlen(pal), 0);
			}
			else if (value == REG_NOMATCH) { 
				printf("Valeur non valide.\n");
				close(sockD);
				exit(1);
			}
			//recevoir "palette endommagee ?" ou "palette n'existe pas" 
			memset(buffer, '\0', sizeof(buffer)); //vider le buffer
			recv(sockD, buffer, 1024, 0);
			check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
			buffer[strcspn(buffer, "\n")] = 0;
			printf("Reponse : %s\n", buffer);

			if (strcmp(buffer, "vous n'etes pas chargé de cette palette")!=0)
				{ //recevoir "la palette est-elle endommagee ?"
				do{ //scan de la reponse du receptionnaire, oui ou non
					fgets(res, 5, stdin);
				}while(strcmp(res,"oui")==0 || strcmp(res,"non")==0); 

				send(sockD, res, strlen(res), 0);//envoyer 
				memset(buffer, '\0', sizeof(buffer)); //vider le buffer
				recv(sockD, buffer, 1024, 0);
				check_timeout(buffer,sockD); //appel a la fonction qui permet de verifier si la donnee recu est vide (dans le cas d'un timeout)
				buffer[strcspn(buffer, "\n")] = 0;
				if (strcmp(buffer, "Information enregistré")==0)
					printf("Response : %s\n", buffer);
				else
					printf("Reponse : La zone de pre-stockage de cette palette est la zone : %s\n", buffer);
			}
			printf("Fin de la connexion");
			close(sockD);
			exit(1);
		}
		else 
			printf("vous n'etes pas censé utiliser ces fonctionalités");
		
		regfree(&reegex); //vider le buffer associe a la fonction pour verifier les regex
	}
	return 0;
}
