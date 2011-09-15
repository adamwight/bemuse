#ifndef SUBJECT_H
#define SUBJECT_H

#include "db_entity.h"
#include <string>
using namespace std;

class subject_heading : public db_entity
{
public:
    subject_heading( class subject_heading* next );
    virtual ~subject_heading();

    unsigned long db_get_id();
    static subject_heading* db_get( unsigned long book_id );
    void commit( unsigned long book_id );

    string topic, subordinate, location, date, title, affiliation,
	   form, general, chronological, geographic;
    string term_source;

    class subject_heading* next;
};

#endif
