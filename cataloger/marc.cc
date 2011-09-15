/*
 * $Id: marc.cc,v 1.21 2007/09/21 23:41:02 adamw Exp $
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "marc.h"
#include "html_parser.h"
#include "note.h"
#include "subject.h"

#include "yaz/yaz-iconv.h"

extern int verbose, dump;

char marc::field_sep = 0;

void convert(const string& src, string& dst, yaz_iconv_t& iconv)
{
    size_t len, rem;
    len = src.length();
    char buf[256], sbuf[256];
    strncpy(sbuf, src.c_str(), sizeof(sbuf)-1);
    char* p = buf;
    char *sp = sbuf;
    rem = sizeof(buf)-1;
    yaz_iconv(iconv, &sp, &len, &p, &rem);
    *p = 0;
    dst = buf;
}

void convert_from_marc8(const string& src, string& dst)
{
    static yaz_iconv_t iconv = yaz_iconv_open("UTF8", "MARC8");

    convert(src, dst, iconv);
}

marc::marc()
{
    memset( place, 0, 4 );
    memset( date_1, 0, 4 );
    memset( date_2, 0, 4 );
    memset( &last_transaction_time, 0, sizeof(time_t) );
    memset( &entry_time, 0, sizeof(time_t) );
}

marc::~marc()
{
}

void marc::get_subfields( const char* s, subfield_spec* sub )
{
    int skip = 2;
    if (field_sep == '|') skip = 3;

    char* p = (char*)s;

    while ((p = index( p, field_sep )) != NULL)
    {
	char* end = index( &p[1], field_sep );
	if (!end) end = index( p, 0 );
	else if (*end == ' ') end--;
	int length = (end - p) - skip;

	subfield_spec* cur = sub;
	while (cur->code)
	{
	    if (cur->code == p[1])
	    {
		if (length)
                {
                    string encoded(&p[skip], length);
                    charset_convert(encoded, *cur->contents);
                }
		break;
	    }

	    cur++;
	}

	if (!cur->code && (verbose > 1))
	{
	    fprintf( stderr, "unknown subfield %c, '%*s'\n",
		     p[1], length, &p[skip] );
	}

	p += length + skip;
    }
}

// convert strings to UTF-8
void marc::charset_convert(const string& src, string& dst)
{
    //static yaz_iconv_t iconv = yaz_iconv_open("ISO-8859-1", "MARC8");
    switch (encoding)
    {
        case ENC_UTF8:
            dst = src;
            break;
        case ENC_MARC8:
        default:
            convert_from_marc8(src, dst);
            break;
    }
}

void marc::print()
{
    book::print();

    printf( "\n" );
    printf( "last transaction: %s", ctime( &last_transaction_time ) );
    printf( "entry time      : %s", ctime( &entry_time ) );
    printf( "date_type: %c date_1: %.4s date_2: %.4s\n",
	     date_type,    date_1,     date_2 );
    printf( "place: %.3s\n", place );
}

void marc::parse_raw( const char* data )
{
    //if (dump)
    {
	FILE* f = fopen("marc_raw", "w");
	if (f) {
	    fputs(data, f);
	    fclose(f);
	} // else don't trip
    }

    field_sep = 0x1f;

    encoding = data[9];
    if (verbose)
    printf("Encoding: '%c'\n", encoding);

    char* p = (char*)data + 24; // skip leader for now
    const char* contents_begin = strchr( data, 0x1e ) + 1;

    while (p[0] != 0x1e)
    {
	int code, length, offset;
	sscanf( p, "%3d%4d%5d", &code, &length, &offset );

	char* contents = new char[length + 1];
	strncpy( contents, contents_begin + offset, length - 1 );
	contents[length - 1] = 0;
	p += 12;

	parse_entry( code, contents );

	delete contents;
    }

    if (verbose > 1) print();
}

void marc::parse_loc_html( const char* page )
{
    field_sep = '|';
    html_parser h( page );

    h.snip( "LC Control Number:", 1 );

    do
    {
	char* code     = h.snip( "<TH[^>]*>([0-9][0-9][0-9])</TH>" );
	char* contents = h.snip( "^(.*)</TD>" );

	if (!code)		       break;
	if (!contents) { free( code ); break; }

	h.strip_html( contents );

	parse_entry( atoi( code ), contents );

	free( code );
	free( contents );
    }
    while (!h.done());

    if (verbose > 1) print();
}

void marc::parse_entry( int code, const char* contents )
{
    switch (code)
    {
    case   5: parse_005( contents ); break;
    case   8: parse_008( contents ); break;
    case  10: parse_010( contents ); break;
    case  20: parse_020( contents ); break;
    case  50: parse_050( contents ); break;
    case  82: parse_082( contents ); break;
    case 100: parse_100( contents ); break;
    case 245: parse_245( contents ); break;
    case 260: parse_260( contents ); break;
    case 300: parse_300( contents ); break;
    case 500: parse_500( contents ); break;
    case 520: parse_520( contents ); break;
    case 600: case 610: case 611: case 630: case 648: case 650: 
    case 651: case 653: case 654: case 655: case 656: case 657: case 658:
	      parse_6xx( contents ); break;
    case 852: parse_852( contents ); break;
    default:
	if (verbose > 1) printf( "unhandled marc %03d [%s]\n", code, contents );
	break;
    }
}

/* date and time of last transaction */
void marc::parse_005( const char* s )
{
    struct tm t;

    sscanf( s, "%4d%2d%2d%2d%2d%2d",
	    &t.tm_year, &t.tm_mon, &t.tm_mday,
	    &t.tm_hour, &t.tm_min, &t.tm_sec );

    t.tm_year -= 1900;
    t.tm_mon -= 1;
    last_transaction_time = mktime( &t );
}

/* general information */
void marc::parse_008( const char* s )
{
    struct tm t;
    memset( &t, 0, sizeof(t) );

    char l[4];
    sscanf( s, "%2d%2d%2d%c%4c%4c%3c%*17c%3c",
	    &t.tm_year, &t.tm_mon, &t.tm_mday,
	    &date_type, date_1, date_2, place, l );

    l[3] = 0;
    if (*l) language = l;

    if (t.tm_year <= 3) t.tm_year += 100; // XXX rethink cutoff
    t.tm_mon -= 1;
    entry_time = mktime( &t );
}

/* lc control number */
void marc::parse_010( const char* s )
{
    subfield_spec sub[] = { { 'a', &lccn },
			    { 0, NULL } };

    get_subfields( s, sub );
    strip_initials( lccn, " " );
}

/* isbn */
void marc::parse_020( const char* s )
{
    subfield_spec sub[] = { { 'a', &isbn },
			    { 0, NULL } };

    get_subfields( s, sub );
    if (isbn.size() > 10) isbn.erase( 10 );
}

/* library of congress call number */
void marc::parse_050( const char* s )
{
    subfield_spec sub[] = { { 'a', &lc_class_number },
			    { 'b', &lc_item_number },
			    { 0, NULL } };

    get_subfields( s, sub );
    strip_finals( lc_class_number, " " );
    strip_initials( lc_class_number, " " );
}

/* dewey call number */
void marc::parse_082( const char* s )
{
    subfield_spec sub[] = { { 'a', &d_class_number },
			    { 'b', &d_item_number },
			    { 0, NULL } };

    get_subfields( s, sub );
}

/* personal name */
void marc::parse_100( const char* s )
{
    subfield_spec sub[] = { { 'a', &author },
			    { 'd', &author_dates },
			    { 'q', &fuller_name },
			    { 0, NULL } };

    get_subfields( s, sub );

    strip_finals( author, " ,." );
}

/* title */
void marc::parse_245( const char* s )
{
    subfield_spec sub[] = { { 'a', &title },
			    { 'b', &subtitle },
			    { 'c', &responsible },
			    { 0, NULL } };

    get_subfields( s, sub );

    strip_finals( title, " .,/:" );
    strip_initials( subtitle, " :" );
    strip_finals( subtitle, " ./" );
    strip_finals( responsible, " ." );
}

/* publishing info */
void marc::parse_260( const char* s )
{
    subfield_spec sub[] = { { 'a', &pub_place },
			    { 'b', &pub_name },
			    { 'c', &pub_dates },
			    { 0, NULL } };

    get_subfields( s, sub );

    strip_finals( pub_place, " :," );
    strip_finals( pub_name, " :," );
    strip_finals( pub_dates, " ." );
}

/* physical description */
void marc::parse_300( const char* s )
{
    subfield_spec sub[] = { { 'a', &extent },
			    { 'b', &other_phys },
			    { 'c', &dimensions },
			    { 0, NULL } };

    get_subfields( s, sub );
    strip_finals( extent, " :" );
}

/* general note */
void marc::parse_500( const char* s )
{
    note_head = new note( note_head );

    note_head->type = "general";
    subfield_spec sub[] = { { 'a', &note_head->contents },
			    { 0, NULL } };

    get_subfields( s, sub );
}

/* summary note */
void marc::parse_520( const char* s )
{
    note_head = new note( note_head );

    note_head->type = "summary";
    subfield_spec sub[] = { { 'a', &note_head->contents },
			    { 0, NULL } };

    get_subfields( s, sub );
}

/* subject */
void marc::parse_6xx( const char* s )
{
    subject_head = new subject_heading( subject_head );

    subfield_spec sub[] = { { 'a', &subject_head->topic },
			    { 'b', &subject_head->subordinate },
			    { 'c', &subject_head->location },
			    { 'd', &subject_head->date },
			    { 't', &subject_head->title },
			    { 'u', &subject_head->affiliation },
			    { 'v', &subject_head->form },
			    { 'x', &subject_head->general },
			    { 'y', &subject_head->chronological },
			    { 'z', &subject_head->geographic },
			    { '2', &subject_head->term_source },
			    { 0, NULL } };

    get_subfields( s, sub );
}

/* location */
void marc::parse_852( const char* s )
{
    string class_number, item_number;

    subfield_spec sub[] = { { 'h', &class_number },
			    { 'i', &item_number },
			    { 0, NULL } };

    get_subfields( s, sub );

    strip_finals( class_number, " " );
    strip_initials( class_number, " " );

    if (lc_class_number.empty())
    {
	lc_class_number = class_number;
	lc_item_number = item_number;
    }
}

void marc::strip_initials( string& s, char* chars )
{
    const char* p = s.c_str();

    while (*p && index( chars, *p )) { p++; }

    if (p > s) s.erase( 0, (int)(p - s.c_str()) );
}

void marc::strip_finals( string& s, char* chars )
{
    int len = s.length();
    if (!len) return;

    const char* p = s.c_str() + (len - 1);

    do {
	if (index( chars, *p )) { s.resize( --len ); p--; }
	else break;
    }
    while (!s.empty());
}
