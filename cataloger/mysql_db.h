/*
 * $Id: mysql_db.h,v 1.8 2006/04/04 03:01:32 adamw Exp $
 */

#ifndef MYSQL_H
#define MYSQL_H

#include <string>
#include <vector>
#include <mysql/mysql.h>
using namespace std;

class mysql_db
{
public:
    mysql_db();
    ~mysql_db();

    void connect( const string& host, const string& user,
		  const string& passwd, const string& db );
    MYSQL_RES* query( const char* );
    string query_cell( const char* );
    void execute( const char* );
    unsigned long last_insert_id();

    mysql_db* clone();

    string quote( const string& s );

protected:
    string host, user, passwd, db;
    int id;

    void bare_query(const char*);
    MYSQL* conn(); // get this thread's connection
};

#endif
