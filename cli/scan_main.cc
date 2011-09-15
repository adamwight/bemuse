/*
 * $Id: scan_main.cc,v 1.2 2006/02/09 10:34:26 adamw Exp $
 *
 * unfinished, standalone barcode scanning
 */

#include <unistd.h>
#include <stdio.h>

//#include "scandata.h"
#include "isbn.h"

void checksums( );
int convert_ean_to_isbn( const char *ean, char *isbn );
char checkdigit_ean( const char *ean );

void scanloop( )
{
//	strcpy( buf, ".C3nZC3nZC3nWCxjWE3D1C3nX.cGf2.ENr7C3v7D3T3ENj3C3zYDNnZ." );
    get_location();

    while (1)
    {
        char buf[2048];
        long len = read( 0, buf, 2047 );
        buf[len] = 0;

        if (len < 5) break;

        scandata d( buf );
        d.print( );
	if (d.valid())
	{
            query = 
		"SELECT * FROM physical,location "
		     "WHERE ean='" + ean + "' "
		     "AND location.n=physical.location_id "
		     "AND location.description='" + loc_desc + "'";
            MYSQL_RES *result = db->query( query.c_str() );

            if (mysql_fetch_row( result ))
            {
                mysql_free_result( result );
                if (ib5[0])
                {
		    query = "UPDATE book SET ib5='" + ib5 + "' "
				"WHERE ean='" + ean + "'";
                    db->execute( query.c_str() );
                    fprintf( stdout, "(recorded ib5 code)\n" );
                }
                fprintf( stderr, "*** book already exists ***\n" );
            } else {
		char q[2048]
                snprintf( q, 2048,
			  "INSERT INTO books (ean,isbn,location_id) "
				 "VALUES ('%s','%s',%lu)",
			  ean, isbn, location_id );
                db->execute( q );
                snprintf( q, 2048,
			  "INSERT INTO book (isbn,ib5) VALUES ('%s',%s)",
			  isbn, ib5 );
                db->execute( q );
                printf( "adding book with barcode '%s'\n", data );
                metacatalog::get_info( isbn );
            }
        }
        else fprintf( stderr, "*** unknown ctype ***\n" );
    }
}

void get_location()
{
    printf( "location description: " );
    string loc_desc;
    cin >> loc_desc;
    loc_desc = db->quote( loc_desc );

    string query =
	"SELECT n FROM location WHERE description='" + loc_desc + "'";
    MYSQL_RES *result = db->query( query.c_str() );

    if (MYSQL_ROW row = mysql_fetch_row( result ))
    {
        location_id = strtoul( row[0], NULL, 10 );
    }
    else
    {
	query = "INSERT INTO location SET description='" + loc_desc + "'";
	db->execute( query.c_str() );
        location_id = db->last_insert_id();
    }
    mysql_free_result( result );
}

void checksums( )
{
    printf( "checking ean checksums and generating isbns...\n" );

    MYSQL_RES *result = db->query( "SELECT ean FROM books WHERE isbn IS NULL" );

    while (MYSQL_ROW row = mysql_fetch_row( result ))
    {
        char *ean = row[0];
        char isbn[11];

        if (!convert_ean_to_isbn( ean, isbn ))
        {
            printf( "the checksum on ean %s is wrong...\n", ean );
            continue;
        }

        char query[256];
        snprintf( query, 256, "UPDATE books SET isbn='%s' WHERE ean='%s'",
                  isbn, ean );
        db->execute( query );
    }

    mysql_free_result( result );
    printf( "done\n" );
}
