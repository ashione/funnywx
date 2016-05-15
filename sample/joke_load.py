#!/usr/bin/python
# -*_ coding : utf-8 -*_
import MySQLdb
import numpy as np
import sys
reload(sys)
sys.setdefaultencoding('utf-8')

db = MySQLdb.connect('localhost','wechat','wechat','wechat',init_command="set names utf8")
cursor = db.cursor()
print cursor.execute("select * from joke")
data = cursor.fetchall();
print data

#txt = np.loadtxt('./joke_init.txt',dtype=np.str)
#print txt

#txt =  open("joke_init.txt").read()
##print type(txt)
#txtlist = txt.split('\n')
#for it in txtlist:
#    if it != '':
#        itarr = it.split('.')
#        itcp= itarr[1].replace('"',"'").strip(' ').decode('utf-8')
#        print itcp
#        cursor = db.cursor()
#        sql = 'insert into joke(content) values("{0}")'.format(itcp.encode('utf-8'))
#        print sql
#        cursor.execute(sql)
#        db.commit()
