
class main_window
{
public:
    main_window(GtkWidget* window)
    {
        g_signal_connect (window, "destroy",
                          G_CALLBACK (gtk_widget_destroyed), &window);
    
        gtk_window_set_default_size (GTK_WINDOW (window), 650, 400);
        gtk_widget_show_all (window);
    }
};
