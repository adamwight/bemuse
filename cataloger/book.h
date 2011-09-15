/*
 * $Id: book.h,v 1.20 2006/04/04 02:48:34 adamw Exp $
 */

#ifndef BOOK_H
#define BOOK_H

#include <string>
using namespace std;

#include "db_entity.h"

class book : public db_entity
{
public:
    book();
    book(unsigned long id) : note_head(NULL), subject_head(NULL) { db_id = id; }
    virtual ~book();

    int db_get();
    unsigned long db_get_id();
    virtual void commit();

    void print();

    void refine( book& m );
    void calculated_fields();
    int informed() const;

    string isbn, lccn, title, subtitle, title_sort, title_full,
           author, author_dates, fuller_name, responsible,
           call_dewey, d_class_number, d_item_number,
           call_lc, lc_class_number, lc_item_number,
           extent, dimensions, other_phys,
           pub_place, pub_name, pub_dates, language;

    class note*		    note_head;
    class subject_heading*  subject_head;
    class query*	    source_query;
    class source*	    src;

protected:
    static void erase_prefix( string& s, const char* list[] );

private:
    string source_query_id, source_id;
};

class not_a_book : public book
{
public:
    virtual void commit() {
    }
};
#endif
