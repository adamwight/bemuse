#ifndef HTML_PRINTER
#define HTML_PRINTER

#include "printer.h"

class html_printer : public printer
{
public:
    void announce_add_success(book& b, string& criteria);
    void fail(string& s);
    void results(metacatalog& c);
    void results(book& b);
};

#endif
