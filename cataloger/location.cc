#include <stdio.h>

#include "location.h"

#include "mysql_db.h"
extern mysql_db* db;

location::location(unsigned long id)
{
    this->location_id = id;
}

void location::add(book& b)
{
    char query[1024];

    b.commit();

    snprintf( query, 1024,
              "INSERT INTO physical SET book_id=%lu, "
				       "location_id=%lu, "
				       "home_location=%lu",
              b.db_id, location_id, location_id );
    db->execute( query );
}
