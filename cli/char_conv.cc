#include <iostream>
#include <stdlib.h>
#include "mysql_db.h"
#include "globals.h"
#include <yaz/marcdisp.h>
#include <yaz/yaz-iconv.h>
#include "char_conv.h"

extern void test_marc8();
extern void test_utf8();

//exten void convert(const string& src, string& dst, yaz_iconv_t& iconv);
//exten void convert(const string& src, string& dst, yaz_iconv_t& iconv);

void loop_titles()
{
    // marc uses both encodings ISO-8859-1 and MARC8
    string query =
	"SELECT n, title FROM book WHERE n IN (250,298, 21742, 22397)";

    MYSQL_RES *result = db->query( query.c_str() );

    while (MYSQL_ROW row = mysql_fetch_row(result))
    {
	unsigned long db_id = strtoul( row[0], NULL, 10 );
	string title(row[1]);
        string convd;
        convert_from_marc8(title, convd);
	//title = buf;

	cout << db_id << ": " << convd << "\n";
    }
}

int main()
{
    init_globals();
    loop_titles();
    //test_marc8();
    //test_utf8();
}
