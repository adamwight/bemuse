/*
 * $Id: catalog.cc,v 1.23 2006/04/04 02:48:58 adamw Exp $
 */

#include <stdio.h>
#include <mysql/mysql.h>
#include <string.h>

#include "catalog.h"
#include "catalog_amazon.h"
#include "catalog_z3950.h"
#include "globals.h"
#include "query.h"

string catalog::result_string() const
{
    char s[32];
    snprintf(s, 32, "%lu", db_query->db_id);
    string result_string = s;

    return result_string;
}

/* static */ void catalog::urlescape( string& s )
{
    string out;
    const char must_escape[] = "%;/?:@&=+\"#<>"; // rfc 1945
    const char* c = s.c_str();

    while (*c)
    {
	if (*c == ' ') out += '+';
	else if (index( must_escape, *c ))
	{
	    char hex[4];
	    snprintf( hex, 4, "%%%02X", *c );
	    out += hex;
	}
	else out += *c;

	c++;
    }

    s = out;
}

/* static */ void catalog::striptags( string& s )
{
    string out;
    const char* c = s.c_str();

    while (*c)
    {
	if (*c == '<')
	{
	    c = index( c, '>' );
	    if (!c) break;
	}
	else out += *c;

	c++;
    }

    s = out;
}

int catalog::do_query(query* q)
{
    db_query = q;
    book tmp = q->get_book();
    return get_info(tmp);
}
