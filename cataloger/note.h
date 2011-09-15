#ifndef NOTE_H
#define NOTE_H

#include "db_entity.h"
#include <string>
using namespace std;

class note : public db_entity
{
public:
    note( note* next );
    virtual ~note();

    unsigned long db_get_id();
    static note* db_get( unsigned long book_id );
    void commit( unsigned long book_id );

    note& operator=(const note& n);

    string contents, type;
    unsigned long db_id;

    note* next;
};

#endif
