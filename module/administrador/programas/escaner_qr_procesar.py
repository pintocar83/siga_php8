#!/usr/bin/env python
import os
import sys
import re

url_re = re.compile(r'^(QR-Code:)?((https?):((//)|(\\\\))+([\w\d:#@%/;$()~_?\+-=\\\.&](#!)?)*)$', re.M)

def lookup(decode):
    match = url_re.search(decode)
    if match is None:
        print "MATCH IS NONE: " + decode,
        return
    url = match.group(2)
    print "ABRIR: " + url
    os.system("./escaner_qr_visualizar.py \"" + url + "\" &")    
    sys.stdout.flush()

if __name__ == "__main__":
    del sys.argv[0]
    if len(sys.argv):
        for decode in sys.argv:
            lookup(decode)
    if not sys.stdin.isatty():
        while 1:
            decode = sys.stdin.readline()
            if not decode:
                break
            lookup(decode)
