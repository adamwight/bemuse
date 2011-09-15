#include "html_printer.h"

#include <string>
#include <iostream>
using namespace std;

#include "book.h"
#include "metacatalog.h"

extern int verbose;

void html_printer::announce_add_success(book& b, string& criteria)
{
    return;
//XXX don't really want to do this
    cout << "added <u>" << b.title << "</u> by " << b.author
	 << " (" << criteria << ")\n";

    if (verbose) b.print();
}

void html_printer::fail(string& s)
{
    cout << "<font color=red>failed: " << s << "</font>\n";
}

void html_printer::results(book& b)
{
    cout << "1\n";
    cout << b.db_id << "\n";
}

void html_printer::results(metacatalog& c)
{
    //cout << c.n_results() << "\n";
    cout << c.result_string() << "\n";
}
