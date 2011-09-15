#include "mysql_db.h"
#include "globals.h"

int fast = 0;
int verbose = 0;
int unplugged = 0;
int all_catalogs = 0;
int dump = 0;

unsigned long location_id;

mysql_db* db;

void init_globals()
{
    db = new mysql_db;
        db->connect( "localhost", "web", "gronk", "library" );
}
