#ifndef GLOBALS_H
#define GLOBALS_H

#include "mysql_db.h"

extern int verbose;
extern mysql_db* db;
extern int unplugged;
extern unsigned long location_id;
extern int all_catalogs;
extern int dump;
extern int fast;

extern void init_globals();

#endif
