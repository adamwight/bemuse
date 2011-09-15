/*
 * $Id: html_parser.h,v 1.6 2006/02/14 03:21:46 adamw Exp $
 *
 * See html_parser.cc for a description.
 */

#ifndef HTML_PARSER_H
#define HTML_PARSER_H

#include <sys/types.h>
#include <regex.h>
#include <string>
using namespace std;

class html_parser
{
public:
    html_parser( const char* page );

    char* snip( const char* regex, int just_advance = 0 ); // deprecate
    void skip(const char* regex);
    void snip(const char* regex, string& dst);
    int done();

    void err_assert( int errno );
    static void strip_html( char* s );

private:
    const char* page;
    char* page_marker;
    regex_t re;
};

#endif
