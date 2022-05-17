#!/bin/sh

umount /media/nfs/$USER
umount /media/publico

cp /etc/fstab_bak /etc/fstab


exit 0
