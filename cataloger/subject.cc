#include <stdlib.h>
#include <stdio.h>

#include "subject.h"

#include "mysql_db.h"
extern mysql_db* db;

subject_heading::subject_heading( subject_heading* next )
{
    this->next = next;
    db_id = 0;
}

subject_heading::~subject_heading()
{
    if (next) delete next;
}

unsigned long subject_heading::db_get_id()
{
    string query =
	"SELECT n FROM subject "
	    "WHERE topic='"	    + db->quote(topic) + "' AND "
		  "subordinate='"   + db->quote(subordinate) + "' AND "
		  "location='"	    + db->quote(location) + "' AND "
		  "date='"	    + db->quote(date) + "' AND "
		  "title='"	    + db->quote(title) + "' AND "
		  "affiliation='"   + db->quote(affiliation) + "' AND "
		  "form='"	    + db->quote(form) + "' AND "
		  "general='"	    + db->quote(general) + "' AND "
		  "chronological='" + db->quote(chronological) + "' AND "
		  "geographic='"    + db->quote(geographic) + "'";

    MYSQL_RES *result = db->query( query.c_str() );

    if (MYSQL_ROW row = mysql_fetch_row(result))
    {
	db_id = strtoul( row[0], NULL, 10 );
    }

    return db_id;
}

/* static */ subject_heading* subject_heading::db_get( unsigned long book_id )
{
    subject_heading* head = NULL;

    char query[256];
    snprintf( query, 256,
	"SELECT n, topic, subordinate, location, date, title, "
	       "affiliation, form, general, chronological, geographic "
	    "FROM subject_link, subject "
	    "WHERE book_id=%lu AND subject_id=n", book_id );

    MYSQL_RES *result = db->query( query );

    while (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	head = new subject_heading( head );

	head->db_id         = strtoul( row[0], NULL, 10 );
	head->topic         = (row[1] ? row[1] : "");
	head->subordinate   = (row[2] ? row[2] : "");
	head->location      = (row[3] ? row[3] : "");
	head->date          = (row[4] ? row[4] : "");
	head->title         = (row[5] ? row[5] : "");
	head->affiliation   = (row[6] ? row[6] : "");
	head->form          = (row[7] ? row[7] : "");
	head->general       = (row[8] ? row[8] : "");
	head->chronological = (row[9] ? row[9] : "");
	head->geographic    = (row[10] ? row[10] : "");
    }

    return head;
}

void subject_heading::commit( unsigned long book_id )
{
    string command;
    char tail[64] = "";

    if (get_id())
    {
	command = "UPDATE";
	snprintf( tail, 64, " WHERE n=%lu", db_id );
    }
    else
    {
	command = "INSERT INTO";
    }

    char id[16], sid[16];
    snprintf( id, 16, "%lu", book_id );
    snprintf( sid, 16, "%lu", db_id );

    // XXX split into book_subject (linkage) and subject_heading

    string query =
	command + " subject SET "
	    "topic='"	      + db->quote(topic) + "', "
	    "subordinate='"   + db->quote(subordinate) + "', "
	    "location='"      + db->quote(location) + "', "
	    "date='"          + db->quote(date) + "', "
	    "title='"         + db->quote(title) + "', "
	    "affiliation='"   + db->quote(affiliation) + "', "
	    "form='"	      + db->quote(form) + "', "
	    "general='"	      + db->quote(general) + "', "
	    "chronological='" + db->quote(chronological) + "', "
	    "geographic='"    + db->quote(geographic) + "'" + tail;

    db->execute( query.c_str() );
    if (!db_id) db_id = db->last_insert_id();

    query = "REPLACE INTO subject_link SET book_id=";
    query += id;
    query += ", subject_id=";
    query += sid;
    db->execute( query.c_str() );
}
