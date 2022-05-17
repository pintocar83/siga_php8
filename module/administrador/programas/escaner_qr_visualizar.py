#!/usr/bin/env python
 
import sys
import gtk
import webkit
 
 
class SimpleBrowser:
 
    def __init__(self):
        self.window = gtk.Window(gtk.WINDOW_TOPLEVEL)
        self.window.set_position(gtk.WIN_POS_CENTER_ALWAYS)
        self.window.connect('delete_event', self.close_application)
        self.window.set_default_size(600, 400)
        self.window.maximize();
 
        vbox = gtk.VBox(spacing=5)
        vbox.set_border_width(5)
 
        self.scrolled_window = gtk.ScrolledWindow()
        self.webview = webkit.WebView()
        self.scrolled_window.add(self.webview)
 
        vbox.pack_start(self.scrolled_window, fill=True, expand=True)
        self.window.add(vbox)
 
    def _txt_url_activate(self, entry):
        self._load(entry.get_text())
 
    def _load(self, url):
        self.webview.open(url)
 
    def open(self, url):
        self.window.set_title('Escaner QR - %s' % url)
        self._load(url)
 
    def show(self):
        self.window.show_all()
 
    def close_application(self, widget, event, data=None):
        gtk.main_quit()
 
if __name__ == '__main__':
    url = sys.argv[1]
    # PyWebkitGTK necesita habilitar el soporte de los hilos en PyGTK
    gtk.gdk.threads_init()
    browser = SimpleBrowser()
    browser.open(url)
    browser.show()
    gtk.main()
