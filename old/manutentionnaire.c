#include <netinet/in.h> //structure for storing address information
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h> //for socket APIs
#include <sys/types.h>
#include <arpa/inet.h>
#include <unistd.h>


int main(int argc, char const* argv[])
{
	int sockD = socket(AF_INET, SOCK_STREAM, 0);

	struct sockaddr_in serverAddr;
    memset(& serverAddr, '\0', sizeof(serverAddr));

    char buffer[1024];
	char pal[1024];
	char prod[1024];
	char *s = (char*)malloc(10*sizeof(char));


	serverAddr.sin_family = AF_INET;
	serverAddr.sin_port= htons(5005); // use some unused port number
	// servAddr.sin_addr.s_addr = INADDR_ANY;
    serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");

	int connectStatus= connect(sockD, (struct sockaddr*)&serverAddr, sizeof(serverAddr));

	if (connectStatus == -1){
		printf("Error...\n");
        exit(0);
    }

	else {
        printf("[+]Connected to Server.\n");

		// Send palette
		printf("Donner la palette: \n");
		//strcpy(pal, "P200");
		scanf("%s",pal);
        send(sockD, pal, strlen(pal), 0);

		// Recv info palette
        memset(buffer, '\0', sizeof(buffer));
		recv(sockD, buffer, 1024, 0);
		printf("Message: %s\n", buffer);
		
		if(strcmp(buffer, "None")!=0)
			do{
				int a = 0;
				// Send produit
				printf("Donner le produit:\n");
				scanf("%s",prod);
				//fgets(prod, sizeof(prod), stdin);                                                                                  
				send(sockD, prod, strlen(prod), 0);

				// Recv info produit
				memset(buffer, '\0', sizeof(buffer));
				recv(sockD, buffer, 1024, 0);
				printf("Message: (allée, emplacement)=%s\n", buffer);
			}while(strcmp(prod, "FIN")!=0);
		
		printf("fin");

        close(sockD);
	}

	return 0;
}