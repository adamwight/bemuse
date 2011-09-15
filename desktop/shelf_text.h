#include "headings_static.h"
#include "classifications_lc.h"
#include "text_with_headings.h"
#include "toggle_set.h"
#include <string.h>

class shelf_text : public text_with_headings
{
public:
    shelf_text(GtkTextView* text)
        : text_with_headings(text)
    {
        load_data();

        g_signal_connect(heading_tag, "event", G_CALLBACK(heading_cb), this);
    }

    static gboolean heading_cb(GtkTextTag* tag, GObject *text_view,
        GdkEvent* event, GtkTextIter* where, gpointer shelf)
    {
        if (event->type == GDK_BUTTON_RELEASE)
        {
            gtk_text_iter_backward_to_tag_toggle(where, tag);
            GtkTextIter* sol = gtk_text_iter_copy(where);
            gtk_text_iter_forward_to_tag_toggle(where, tag);
            gchar* title = gtk_text_iter_get_text(sol, where);

            ((shelf_text*)shelf)->toggle_heading(title);
            g_free(title);
        }
        //always pass event on
        return FALSE;
    }

protected:
    string contents;
    regex_t head_re;
    gboolean hidden_text;
    char cur_heading[128];

    void load_data()
    {
        long bookmark = get_bookmark();
        clear_text();

        hidden_text = FALSE;
        char* sql = "SELECT n, title_full, author, responsible, call_lc FROM physical JOIN book ON physical.book_id=book.n ORDER BY call_lc, title_sort";
        MYSQL_RES* rs = db->query(sql);

        while (MYSQL_ROW row = mysql_fetch_row(rs))
        {
            update_heading(row);
            book_row(row);
        }

        GtkTextIter scroll;
        gtk_text_buffer_get_iter_at_line(buffer, &scroll, bookmark);
        GtkTextMark* mark =
            gtk_text_buffer_create_mark(buffer, NULL, &scroll, FALSE);
        gtk_text_view_scroll_to_mark(text, mark, 0, TRUE, 0, 0);
        //gtk_text_view_scroll_to_iter(text, &scroll, 0, FALSE, 0, 0);
        //delete
    }

    void update_heading(MYSQL_ROW row)
    {
        if (row[4] == NULL || *row[4] == '\0')
            new_heading("Uncategorized");

        new_heading(classifications_lc.get_heading(row[4]));
    }

    void new_heading(const char* heading)
    {
        if (!heading)
            return;
        if (strncmp(heading, cur_heading, 128) != 0)
        {
            insert_heading(heading);
            hidden_text = toggled.is_toggled(heading);
            strncpy(cur_heading, heading, 128);
        }
    }

    void book_row(MYSQL_ROW row)
    {
        char s[1024];
        snprintf(s, 1024, "%s %s %s\n", row[1], row[2], row[4]);
        if (!hidden_text)
            insert_text(s);
    }

    toggle_set toggled;
    void toggle_heading(const char* heading)
    {
        toggled.toggle(heading);
        load_data();
    }
};
