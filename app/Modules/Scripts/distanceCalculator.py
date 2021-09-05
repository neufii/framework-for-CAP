# -*- coding: utf-8 -*-

import numpy as np
import json
import sys

f = open(sys.argv[1], "r")
argv = json.loads(f.read())['0']
raw = []
for i in argv:
    raw.append(argv[i])

def processJSON(jsonData):
    arr = []
    for item in jsonData[1:]:
        if(item['type'] != 'other'):
            arr.append(item['content'])
        elif(item['type'] == 'other'):
            continue
    return arr

def jaccardDistance(list1, list2):
    intersection = len(list(set(list1).intersection(list2)))
    union = (len(set(list1)) + len(set(list2))) - intersection
    return 1-(float(intersection) / union)

def getDistanceMatrix(data):
    def jaccardDistance(list1, list2):
        intersection = len(list(set(list1).intersection(list2)))
        union = (len(set(list1)) + len(set(list2))) - intersection
        return 1-(float(intersection) / union)

    out = np.zeros(shape=(len(data),len(data)))
    for idxI,i in enumerate(data):
        for idxJ,j in enumerate(data):
            out[idxI,idxJ] = jaccardDistance(i,j)
    return out

processedData = list(map(lambda x: processJSON(json.loads(x)), raw))
result = getDistanceMatrix(processedData)

for dis in result:
     print(','.join(dis.astype(str)))

