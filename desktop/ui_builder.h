
class ui_builder
{
    GtkBuilder* xml;

public:
    ui_builder(const char* filename)
    {
        xml = gtk_builder_new();
        GError* err = NULL;
        gtk_builder_add_from_file(xml, filename, &err);
        if (err)
            fprintf(stderr, "Went bad: %s\n", err->message);
    }

    GtkWidget* get(const char* name)
    {
        return GTK_WIDGET(gtk_builder_get_object(xml, name));
    }
};
