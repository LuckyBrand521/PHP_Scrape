#!/usr/bin/env python
# -*- coding: utf-8 -*-
##### Pre-processing CSV file dataset #####
#### import libraries required ####
import csv

def takeFirst(val):
    return val[0]

def takeSecond(val):
    return val[1]

def sortList(full_list):
    for sub_list in full_list:
        sub_list.sort(key = takeSecond)
        sub_list.sort(key = takeFirst)
    return full_list

datalist = []
powerlist = []
def generateTupleList(filename):
    try:
        with open(filename) as csvfile:
            reader = csv.reader(csvfile)
            for row in reader:
                row_list = []
                for index in range(16):
                    new_tuple = (float(row[index]), float(row[index+16]), float(row[index+32]))
                    row_list.append(new_tuple)
                datalist.append(row_list)
                powerlist.append(row[48])
    except:
        print("File not exists!")

## Create new sorted csv file

def createNewFile(filename, content, powervallist):
    with open(filename, "w") as csvfile:
        fieldnames = []
        # writer = csv.DictWriter(csvfile, fieldnames = fieldnames)
        writer = csv.writer(csvfile)
        
        new_rows = []
        index = 0;
        for row in content:
            new_row = []
            for i in range(3):
                for _eachtuple in row:
                    new_row.append(_eachtuple[i])
            new_row.append(float(powervallist[index]))
            index = index + 1
            new_rows.append(new_row)
            writer.writerow(new_row)
        # writer.writerows(new_rows)

###### Sort the list of values of csv file ######

generateTupleList('Perth_Data.csv')

datalist = sortList(datalist)

## create new file
createNewFile('new_Perth_Data.csv', datalist, powerlist)