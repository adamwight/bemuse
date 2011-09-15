/*
 * $Id: scandata.cc,v 1.10 2007/02/20 07:29:05 adamw Exp $
 */

#include <unistd.h>
#include <stdio.h>
#include <string.h>

#include "scandata.h"
#include "isbn.h"

scandata::scandata( const char *d )
{
    char *rawdata = new char[strlen( d ) + 1];
    strcpy( rawdata, d );

    char *s;
    if ((s = index( rawdata, '\n' ))) *s = 0;
    if ((s = index( rawdata, '\r' ))) *s = 0;

    raw = rawdata;
    s = rawdata;
    if (*s == '.') s++;
    decoder.decode(strsep(&s, "."), serial);
    decoder.decode(strsep(&s, "."), code_type);
    decoder.decode(strsep(&s, "."), data);

    parse_data();

    delete rawdata;
}

void scandata::print( )
{
    printf( "raw    = %s\n", raw.c_str( ) );
    printf( "serial = %s\n", serial.c_str( ) );
    printf( "type   = %s\n", code_type.c_str( ) );
    printf( "data   = %s\n", data.c_str( ) );
}

bool scandata::valid()
{
    return is_valid;
}

void scandata::parse_data()
{
    is_valid = true;
    if ((code_type == "IBN") || (code_type == "IB5"))
	parse_isbn();
    else
	is_valid = false;
}

void scandata::parse_isbn()
{
    memcpy( ean, data.c_str(), 13 );
    ean[13] = 0;
    if (code_type == "IB5")
    {
	char five[8];
	memcpy( five, data.c_str() + 13, 5 );
	five[5] = 0;
	this->ib5 = five;
    } else {
	ib5 == "";
    }

    ean_to_isbn();
}

void scandata::ean_to_isbn()
{
    char isbn[11];
    if (ean[12] != checkdigit_ean( ean ))
    {
        fprintf( stderr, "the checksum on ean %s is wrong...\n", ean );
        is_valid = false;
	return;
    }

    strncpy( isbn, ean + 3, 9 );
    isbn[9] = checkdigit_isbn(isbn);
    isbn[10] = 0;

    this->isbn = isbn;
}

char scandata::checkdigit_ean( const char *ean )
{
    int sum = 0;

    for (int i = 0; i < 12; i++) sum += (ean[i] - '0') * (1 + (i % 2) * 2);

    sum = sum % 10;
    sum = (10 - sum) % 10;

    return (sum + '0');
}
