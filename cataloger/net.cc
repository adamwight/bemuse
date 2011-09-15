/*
 * $Id: net.cc,v 1.7 2006/02/14 03:22:24 adamw Exp $
 */

#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <mysql/mysql.h>
#include <netdb.h>
#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/time.h>
#include <iostream>
#include <fcntl.h>

#include "globals.h"

char* fetch(const string& url, int* buflen, int timeout);

char* fetch(const string& url, int* buflen, int timeout)
{
    if (verbose) printf( "fetching %s\n", url.c_str() );

    const int FETCH_CHUNK = 4096;
    char					headers[2048];
    char					server[1024],
						path[2048];
    char 					*i = NULL;
    sockaddr_in					sin;
    int 					rb,
						p;
    unsigned long				tb, total_length = 0;
    int 					sock;
    struct hostent 				*hp;
    char 					buffer[FETCH_CHUNK+1024];
    fd_set 					fds_read;
    fd_set 					fds_write;
    struct 					timeval tv;
    char					*ret = NULL;

    if (timeout < 0) timeout=0;
    else if (timeout > 360) timeout=360;

    server[0] = 0;
    path[0] = 0;

    sscanf( url.c_str(), "http://%s", headers);
    i = index( headers, '/' );
    *i = ' ';
    sscanf(headers,"%s%s",server,path);

    sprintf( headers,
	"GET /%s HTTP/1.0\r\n"
	"Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, "
		"application/vnd.ms-excel, application/msword, "
		"application/vnd.ms-powerpoint, application/x-comet, */*\r\n"
	"User-Agent: Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)\r\n"
	"Host: %s\r\n"
	"Connection: Keep-Alive\r\n\r\n",
	path, server );

    if ((hp = gethostbyname(server)) == NULL)
	return NULL;
    else
    {
	bzero(&sin, sizeof(sin));
	memcpy((char *) &sin.sin_addr, hp->h_addr, sizeof(struct in_addr));
	sin.sin_family = AF_INET;
	sin.sin_port = (unsigned short) htons( 80 );		
    }

    if ((sock = socket(PF_INET, SOCK_STREAM, 0)) < 0)
	return NULL;
    if (connect(sock, (struct sockaddr *)&sin, sizeof(struct sockaddr_in)) < 0)
	return NULL;

    FD_ZERO(&fds_write);
    FD_SET(sock, &fds_write);
    tv.tv_sec = 5; // 5 sec timeout
    tv.tv_usec = 0;

    if (select( sock + 1, NULL, &fds_write, NULL, &tv) < 1)
    {
	close(sock);
	return NULL;
    }

    write( sock, headers, strlen(headers) );

    FD_ZERO( &fds_read );
    FD_SET( sock, &fds_read );
    tv.tv_sec = timeout; // 5 sec timeout
    tv.tv_usec = 0;

    if (select(sock+1, &fds_read, NULL, NULL, &tv) < 1)
    {
	shutdown(sock,2);
	close(sock);
	fprintf(stderr,"timeout waiting for server\n");
	return NULL;
    }

    tb=0;
    p=0;
    
    do
    {
	FD_ZERO(&fds_read);
	FD_SET(sock, &fds_read);
	tv.tv_sec = timeout; // 5 sec timeout
	tv.tv_usec = 0;

	if (select(sock+1, &fds_read, NULL, NULL, &tv) < 1)
	{
	    shutdown(sock,2);
	    close(sock);
	    fprintf(stderr, "timeout waiting to read\n");
	    return ret;
	}

	rb = read( sock, buffer, FETCH_CHUNK - 1 );
	tb += rb;
	if (tb > 0)
	{
	    if (!ret)
	    {
		char *loc = strstr( buffer, "Content-Length: " );
		if (loc)
		{
		    total_length = strtoul( loc + 16, NULL, 10 );

		    ret = (char *)malloc( total_length + 32 ); // XXX
		    if (!ret)
		    {
			fprintf( stderr, "No memory available.\n" );
			return NULL;
		    }
		}

		ret = (char *)malloc( tb + 1 );
	    }
	    else if (!total_length)
	    {
		ret = (char *)realloc( ret, tb + 1 );
	    }
	    else if (tb > total_length)
	    {
		fprintf( stderr, "content-length < reality\n" );
		return NULL;
	    }

	    memcpy( ret + p, buffer, rb );
	}

	p = tb;
    } while (rb > 0);

    shutdown( sock, 2 );
    close( sock );

    if (buflen) *buflen = tb;
    
    if ((tb > 0) && (ret != NULL))
    {
	ret[tb] = 0;

	if (dump)
	{
	    char fname[256];
	    snprintf( fname, 256, "dump-%d.html", dump++ );
	    int fd = open( fname, O_CREAT | O_TRUNC | O_WRONLY, 00666 );
	    if (fd >= 0) write( fd, ret, tb );
	}

	return ret;
    }
    else
    {
	return NULL;
    }
}
