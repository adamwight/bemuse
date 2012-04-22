/*
 * $Id: marc.h,v 1.17 2007/09/21 23:41:02 adamw Exp $
 */

#ifndef MARC_H
#define MARC_H

#include <time.h>

#include "book.h"

class marc : public book
{
public:
    time_t last_transaction_time, entry_time;
    char place[4];
    char date_type, date_1[4], date_2[4];

public:
    marc();
    ~marc();

    void print();

    void parse_raw( const char* data );
    void parse_loc_html( const char* page );

protected:
    struct subfield_spec
    {
	char code;
	string* contents;
    };

    static char field_sep;
    char encoding;
    enum { ENC_MARC8 = '#', ENC_UTF8 = 'a' };

    void parse_entry( int code, const char* contents );

    void parse_005( const char* s );
    void parse_008( const char* s );
    void parse_010( const char* s );
    void parse_020( const char* s );
    void parse_050( const char* s );
    void parse_082( const char* s );
    void parse_100( const char* s );
    void parse_245( const char* s );
    void parse_260( const char* s );
    void parse_300( const char* s );
    void parse_500( const char* s );
    void parse_520( const char* s );
    void parse_6xx( const char* s );
    void parse_852( const char* s );

    void get_subfields( const char* s, subfield_spec* sub );
    static void strip_initials( string& s, const char* chars );
    static void strip_finals( string& s, const char* chars );

    void charset_convert(const string& src, string& dst);
};

#endif
