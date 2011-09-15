#include <stdio.h>

#include "source.h"
#include "mysql_db.h"
#include "globals.h"

string source::get_name()
{
    char s[256];
    snprintf(s, 256, "SELECT host FROM z_server WHERE n=%lu", get_id());
    return db->query_cell(s);
}
