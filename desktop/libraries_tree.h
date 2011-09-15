#include "catalog_z3950.h"
#include "catalog_parallel.h"

class libraries_tree
{
    enum {
        NAME_COLUMN,
        N_COLUMNS
    };

public:
    libraries_tree(GtkTreeView* tree)
    {
        GtkListStore* store = gtk_list_store_new(N_COLUMNS, G_TYPE_STRING);
        GtkTreeIter tit;

        catalog_parallel* cat = new catalog_parallel();

        for (vector<catalog_z3950*>::iterator sit = cat->server_list.begin(); sit != cat->server_list.end(); sit++)
        {
            gtk_list_store_append(store, &tit);
            gtk_list_store_set(store, &tit, NAME_COLUMN, (*sit)->get_server()->get_name().c_str(), -1);
        }

        GtkTreeViewColumn* column = gtk_tree_view_column_new_with_attributes("Library", gtk_cell_renderer_text_new(), "text", NAME_COLUMN, NULL);
        gtk_tree_view_append_column(tree, column);
        gtk_tree_view_set_model(tree, GTK_TREE_MODEL(store));
    }
};

