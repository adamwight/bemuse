#include <stdio.h>
#include <gtk/gtk.h>

#include "globals.h"

#include "ui_builder.h"
#include "libraries_tree.h"
#include "main_window.h"
#include "shelf_text.h"

const char* GTKCONFIG = "library.ui";

main(int argc, char **argv)
{
    gtk_set_locale ();
    gtk_init (&argc, &argv);

    init_globals();

    ui_builder* ui = new ui_builder(GTKCONFIG);
    new libraries_tree(GTK_TREE_VIEW(ui->get("libraries")));
    new shelf_text(GTK_TEXT_VIEW(ui->get("shelf")));
    new main_window(GTK_WIDGET(ui->get("main")));

    gtk_main ();

    return 0;
}
