#include <string.h>

class text_with_headings
{
public:
    text_with_headings(GtkTextView* text)
    {
        this->text = text;
        buffer = gtk_text_view_get_buffer(text);

        heading_tag = gtk_text_buffer_create_tag(buffer, "heading",
            "weight", PANGO_WEIGHT_BOLD, NULL);
    }

protected:
    GtkTextTag* heading_tag;
    GtkTextView* text;
    GtkTextBuffer* buffer;

    void insert_heading(const char* heading)
    {
        insert_text(heading, heading_tag);
        insert_text("\n");
    }

    void insert_text(const char* s,
        GtkTextTag* tag = NULL)
    {
        char* c = g_convert(s, -1, "utf8", "iso88591", NULL, NULL, NULL);
        GtkTextIter iter;
        gtk_text_buffer_get_end_iter(buffer, &iter);
        gtk_text_buffer_insert_with_tags(buffer, &iter, c, -1, tag, NULL);
        g_free(c);
    }

    long get_bookmark()
    {
        gint x, y;
        gtk_text_view_window_to_buffer_coords(text, GTK_TEXT_WINDOW_WIDGET,
            0, 0, &x, &y);
        GtkTextIter scroll;
        gtk_text_view_get_iter_at_location(text, &scroll, x, y);
        return gtk_text_iter_get_line(&scroll);
    }

    void clear_text()
    {
        GtkTextIter start, end;
        gtk_text_buffer_get_start_iter(buffer, &start);
        gtk_text_buffer_get_end_iter(buffer, &end);
        gtk_text_buffer_delete(buffer, &start, &end);
    }
};

