import sys
import re
import json

f = open(sys.argv[1], "r")
argv = json.loads(f.read())
raw = argv['0']

remove = raw.replace("\"", "\"")
remove = remove.replace('\\\\', '\\')

jsonData = json.loads(remove)
solution = jsonData["0"]

output = "var json='{\"attrs\":{\"width\":800,\"height\":300},\"className\":\"Stage\",\"children\":[{\"attrs\":{},\"className\":\"Layer\",\"children\":[{\"attrs\":{\"width\":\"auto\",\"height\":\"auto\",\"text\":\" เฉลย: " + \
    solution + \
    "\",\"fontFamily\":\"THSarabunNew\",\"fontSize\":18,\"x\":0,\"y\":20,\"wrap\":\"word\"},\"className\":\"Text\"}]}]}';\nvar stage=Konva.Node.create(json,'solution-container');"
print(output)
