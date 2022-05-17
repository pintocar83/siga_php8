#!/usr/bin/env python
import os

if __name__ == '__main__':
	os.system("zbarcam /dev/video1 | ./escaner_qr_procesar.py")
