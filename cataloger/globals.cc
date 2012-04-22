//#include <yaml.h>

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
    /*
    FILE *file;
    yaml_parser_t parser;
    yaml_document_t document;
    int done = 0;
    int count = 0;
    int error = 0;
    
    file = fopen("config.yml", "rb");
    yaml_parser_initialize(&parser);
    
    yaml_parser_set_input_file(&parser, file);
    
    while (!done)
    {
        if (!yaml_parser_load(&parser, &document)) {
            error = 1;
            break;
        }
    
        done = (!yaml_document_get_root_node(&document));
//XXX
    
        yaml_document_delete(&document);
    
        if (!done) count ++;
    }
    
    yaml_parser_delete(&parser);
    
    fclose(file);
    */

    db = new mysql_db;
        db->connect( "localhost", "web", "gronk", "library" );
}
