#!/usr/bin/python
#coding=utf-8
import urllib2,urllib
import MySQLdb
from bs4 import BeautifulSoup as  bts
from md5 import md5
import time

db = MySQLdb.connect('localhost','wechat','wechat','wechat',init_command="set names utf8")
#headers = {
#        'User-Agent':'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6'
#}
url='http://neihanshequ.com'

def isExist(content):
    digit = md5(content.encode('utf-8'))
    sql = 'select id from joke where md5="{0}"'.format(digit.hexdigest())
    cursor = db.cursor()
    result=cursor.execute(sql)
    #print result
    if result>0:
        return True,digit
    return False,digit

def scrap_funny():
#    req = urllib2.Request(url,headers=headers)
    req = urllib2.Request(url)
    content = urllib2.urlopen(req).read()
    soup = bts(content,'html.parser')

    for item in soup.findAll('li',class_='share-wrapper right'):#.get('data-text'):
        #print item.encode('utf-8')
        content = item.get('data-text').replace("\"","'").replace(";","\;")

        flag,digit =isExist(content)

        if flag :
            print 'in db',content.encode('utf-8')
            continue

        cursor = db.cursor()
        sql = 'insert into joke(content,md5) values("{0}","{1}")'.format(content.encode('utf-8'),digit.hexdigest())
        #print sql
        cursor.execute(sql)
        db.commit()
        #sp2 = bts(item)
        #print sp2.li.get('data-text','html.parser')

if __name__ == '__main__':
    while True:
        time.sleep(10)
        scrap_funny()

