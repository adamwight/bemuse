/*
 * $Id: html_parser.cc,v 1.6 2006/02/14 03:21:46 adamw Exp $
 *
 * html_parser objects are used to simply and efficiently pull data
 * out of a char buffer using regular expressions.  The calling
 * functions retain almost all control over the procedure.
 */

#include <stdlib.h>
#include <string.h>
#include <stdio.h>

#include "html_parser.h"

html_parser::html_parser( const char* page )
{
    this->page  = page;
    page_marker = (char*)page;
}

//
// Use a regex to cut a single string out of the document.
// The just_advance flag doesn't return anything, otherwise snip
// returns malloc'ed data which must be free'd.
//
char* html_parser::snip( const char* regex, int just_advance )
{
    char* out = NULL;
    regmatch_t match[2];

    err_assert( regcomp( &re, regex, REG_EXTENDED | REG_NEWLINE ) );
    int res = regexec( &re, page_marker, 2, match, 0 );
    regfree( &re );

    if (res) return NULL;

    if (!just_advance)
    {
	// the caller wants the first capture buffer
	int len = match[1].rm_eo - match[1].rm_so + 1;
	out = (char*)malloc( len + 1 );
	memcpy( out, page_marker + match[1].rm_so, len );
	out[len - 1] = 0;
    }

    // advance through the page to the end of our regex
    page_marker += match[0].rm_eo;

    return out;
}

void html_parser::skip(const char* regex)
{
    snip(regex, 1);
}

void html_parser::snip(const char* regex, string& dst)
{
    char* out = snip(regex);
    dst = out;
    free(out);
}

//
// Tear all the tags out of a string.
// TODO: erase <head> data as well.
//
/* static */ void html_parser::strip_html( char* s )
{
    for (char* p = s; p && p[0]; )
    {
	p = index( p, '<' );
	if (!p) return;

	char* end = index( p, '>' );

	if (end)
	{
	    memmove( p, end + 1, strlen( end ) );
	}
	else
	{
	    p[0] = '\0';
	    return;
	}
    }
}

int html_parser::done()
{
    // have we parsed the whole page?
    return (*page_marker == 0);
}

//
// Let the programmer down politely if anything has gone awry.
//
void html_parser::err_assert( int errno )
{
    if (!errno || errno == REG_NOMATCH) return;

    char errstr[256];
    regerror( errno, &re, errstr, 256 );
    fprintf( stderr, "html_parser died on: %s\n", errstr );
    fprintf( stderr, "page offset %d\n", page_marker - page );

    exit( errno );
}
