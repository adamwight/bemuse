/*
 * $Id: catalog_amazon.cc,v 1.9 2006/04/04 02:59:30 adamw Exp $
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#include "catalog_amazon.h"
#include "net.h"
#include "html_parser.h"
#include "source.h"

// TODO: write get_image

source src_amazon(8); // XXX FUCK

int catalog_amazon::get_by_isbn(book& b)
{
    if (b.isbn.empty()) return 0;

    string url = "http://www.amazon.com/exec/obidos/ASIN/";
    url += b.isbn;
    char *page = fetch_redirected(url);

    if (strstr( page, "404 Not Found" ) || strstr( page, "Error Page" ))
    {
	failure_reason = "amazon 404/error";
        free( page );
        return 0;
    }

    html_parser h = html_parser(page);

    h.snip(">([^<]+)</b><br />by", b.title);
    striptags(b.title);

    h.snip(">([^<]+)</a>", b.author);
    striptags(b.author);
    b.src = &src_amazon;

    free( page );

    results.add(b);

    return 1;
}

char* catalog_amazon::fetch_redirected(const string& url)
{
    int pagelen;
    char* page = fetch( url, &pagelen, 20 );
    if (!page)
    {
	failure_reason = "null page from " + url;
        return NULL;
    }

    //        printf( "got page len %d:\n%s\n", pagelen, page );

    // doesn't check that we are in the header
    if (char* loc = strstr( page, "Location: " ))
    {
	loc += 10;
	int len = index( loc, '\r' ) - loc;
	char* buf = new char[len + 1];
	strncpy( buf, loc, len );
	buf[len] = 0;
	free( page );
	string new_url = buf;
	delete buf;
	return fetch_redirected(new_url);
    }

    return page;
}
