#!/usr/bin/env python
# -*- coding: utf-8 -*-

import gtk
import os
import ldap
import commands

def responseToDialog(entry, dialog, response):
	dialog.response(response)
	
def getText(msj):
	#base this on a message dialog
	dialog = gtk.MessageDialog(
		None,
		gtk.DIALOG_MODAL | gtk.DIALOG_DESTROY_WITH_PARENT,
		gtk.MESSAGE_QUESTION,
		gtk.BUTTONS_OK_CANCEL,
		None)
	dialog.set_markup('<b>Ingrese la contraseña para la configuración del proxy:</b>')
	#create the text input field
	entry = gtk.Entry()
	entry.set_visibility(False)
	#allow the user to press enter to do ok
	entry.connect("activate", responseToDialog, dialog, gtk.RESPONSE_OK)
	
	#create a horizontal box to pack the entry and a label
	hbox = gtk.HBox()
	hbox.pack_start(gtk.Label("Contraseña:"), False, 5, 5)
	hbox.pack_end(entry)
	#some secondary text
	dialog.format_secondary_markup("<i>Usuario actual: " + os.getlogin() +"</i> " + msj)
	#add it and show it
	dialog.vbox.pack_end(hbox, True, True, 0)
	dialog.show_all()
	#go go go
	dialog.run()
	text = entry.get_text()
	dialog.destroy()
	return text

def comprobar_ldap(usuario,clave):
	try:
	 	l=ldap.initialize("ldaps://newton.fundacite-sucre.gob.ve:636")
	 	l.protocol_version = ldap.VERSION3
	 	username="uid=" + usuario + ",ou=people,dc=fundacite-sucre,dc=gob,dc=ve"
	 	l.simple_bind_s(username, clave)		
	 	# Cerramos la conexion
	 	l.unbind_s()
	 	return 1
	except ldap.LDAPError, e:
	 	return 0


if __name__ == '__main__':
	usuario = os.getlogin()
	password_save = commands.getstatusoutput("gsettings get org.gnome.system.proxy.http authentication-password")
	password_save = password_save[1][1:-1]
	
	if comprobar_ldap(usuario,password_save) == 0:
		msj = ''
		while 1:
			password = getText(msj)
			if password == "":
				break
			msj = '\n<small>No coincide la contraseña para el usuario.</small>'
			if comprobar_ldap(usuario,password) == 1:
				print("gsettings set org.gnome.system.proxy mode 'manual'")
				os.system("gsettings set org.gnome.system.proxy mode 'manual'")
				
				print("gsettings set org.gnome.system.proxy ignore-hosts \"['localhost', '127.0.0.0/8','*.fundacite-sucre.gob.ve']\"")
				os.system("gsettings set org.gnome.system.proxy ignore-hosts \"['localhost', '127.0.0.0/8','*.fundacite-sucre.gob.ve']\"")
				
				print("gsettings set org.gnome.system.proxy use-same-proxy true")
				os.system("gsettings set org.gnome.system.proxy use-same-proxy true")
				
				print("gsettings set org.gnome.system.proxy.http host '172.17.2.16'")
				os.system("gsettings set org.gnome.system.proxy.http host '172.17.2.16'")
				
				print("gsettings set org.gnome.system.proxy.http port 3128")
				os.system("gsettings set org.gnome.system.proxy.http port 3128")
				
				print("gsettings set org.gnome.system.proxy.http enabled true")
				os.system("gsettings set org.gnome.system.proxy.http enabled true")
				
				print("gsettings set org.gnome.system.proxy.http use-authentication true")
				os.system("gsettings set org.gnome.system.proxy.http use-authentication true")
				
				print("gsettings set org.gnome.system.proxy.http authentication-user '" + usuario + "'")
				os.system("gsettings set org.gnome.system.proxy.http authentication-user '" + usuario + "'")
				
				print("gsettings set org.gnome.system.proxy.http authentication-password '" + password + "'")
				os.system("gsettings set org.gnome.system.proxy.http authentication-password '" + password + "'")
				
				break

