#ifndef LOCATION_H
#define LOCATION_H

#include "book.h"

class location
{
    unsigned long location_id;

public:
    location(unsigned long id);

    void add(book& b);
};

#endif
