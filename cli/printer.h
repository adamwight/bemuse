#ifndef PRINTER
#define PRINTER

#include "book.h"
#include "metacatalog.h"
#include <string>
using namespace std;

class printer
{
public:
    virtual void announce_add_success(book& b, string& criteria) = 0;
    virtual void fail(string& s) = 0;
    virtual void results(metacatalog& c) = 0;
    virtual void results(book& b) = 0;
};

#endif
