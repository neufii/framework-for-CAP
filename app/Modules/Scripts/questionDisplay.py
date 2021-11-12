import sys
import re
import json

raw = sys.argv[1]

processed = json.loads(raw)

connected_data = ''.join(e['content'] for e in processed[1:])

choiceA = processed[0]['content'][0]
choiceB = processed[0]['content'][1]
choiceC = processed[0]['content'][2]
choiceD = processed[0]['content'][3]

output = "var json='{\"attrs\":{\"width\":800,\"height\":300},\"className\":\"Stage\",\"children\":[{\"attrs\":{},\"className\":\"Layer\",\"children\":[{\"attrs\":{\"width\":\"auto\",\"height\":\"auto\",\"text\":\""+connected_data+"\",\"fontFamily\":\"THSarabunNew\",\"fontSize\":18,\"x\":0,\"y\":20,\"wrap\":\"word\"},\"className\":\"Text\"},{\"attrs\":{\"x\":30,\"y\":100,\"id\":\"choiceA\"},\"children\":[{\"attrs\":{\"fill\":\"#E3D7FF\",\"opacity\":0},\"className\":\"Tag\"},{\"attrs\":{\"text\":\""+choiceA+"\",\"lineHeight\":1.9,\"fontSize\":18,\"fontFamily\":\"THSarabunNew\",\"padding\":5},\"className\":\"Text\"}],\"className\":\"Label\"},{\"attrs\":{\"y\":100,\"x\":400,\"id\":\"choiceB\"},\"children\":[{\"attrs\":{\"fill\":\"#E3D7FF\",\"opacity\":0},\"className\":\"Tag\"},{\"attrs\":{\"text\":\""+choiceB+"\",\"lineHeight\":1.9,\"fontSize\":18,\"fontFamily\":\"THSarabunNew\",\"padding\":5},\"className\":\"Text\"}],\"className\":\"Label\"},{\"attrs\":{\"x\":30,\"y\":200,\"id\":\"choiceC\"},\"children\":[{\"attrs\":{\"fill\":\"#E3D7FF\",\"opacity\":0},\"className\":\"Tag\"},{\"attrs\":{\"text\":\""+choiceC+"\",\"lineHeight\":1.9,\"fontSize\":18,\"fontFamily\":\"THSarabunNew\",\"padding\":5},\"className\":\"Text\"}],\"className\":\"Label\"},{\"attrs\":{\"y\":200,\"x\":400,\"id\":\"choiceD\"},\"children\":[{\"attrs\":{\"fill\":\"#E3D7FF\",\"opacity\":0},\"className\":\"Tag\"},{\"attrs\":{\"text\":\""+choiceD + \
    "\",\"lineHeight\":1.9,\"fontSize\":18,\"fontFamily\":\"THSarabunNew\",\"padding\":5},\"className\":\"Text\"}],\"className\":\"Label\"}]}]}';\nvar stage=Konva.Node.create(json,'question-container');\n/**setfunctions*\n/*onclick*/\nstage.findOne('#choiceA').on('click',function(){this.children[0].opacity(1);stage.findOne('#choiceB').children[0].opacity(0);stage.findOne('#choiceC').children[0].opacity(0);stage.findOne('#choiceD').children[0].opacity(0);window.learnerAnswer=\""+choiceA+"\";});\nstage.findOne('#choiceB').on('click',function(){this.children[0].opacity(1);stage.findOne('#choiceA').children[0].opacity(0);stage.findOne('#choiceC').children[0].opacity(0);stage.findOne('#choiceD').children[0].opacity(0);window.learnerAnswer=\""+choiceB+"\";});\nstage.findOne('#choiceC').on('click',function(){this.children[0].opacity(1);stage.findOne('#choiceA').children[0].opacity(0);stage.findOne('#choiceB').children[0].opacity(0);stage.findOne('#choiceD').children[0].opacity(0);window.learnerAnswer=\"" + \
    choiceC+"\";});\nstage.findOne('#choiceD').on('click',function(){this.children[0].opacity(1);stage.findOne('#choiceA').children[0].opacity(0);stage.findOne('#choiceB').children[0].opacity(0);stage.findOne('#choiceC').children[0].opacity(0);window.learnerAnswer=\""+choiceD + \
    "\";});\n/*onhover*/\nstage.findOne('#choiceA').on('mouseover mouseout',function(){var opacity=this.children[0].opacity();this.children[0].opacity(opacity===0?0.5:(opacity===1?1:0));});\nstage.findOne('#choiceB').on('mouseover mouseout',function(){var opacity=this.children[0].opacity();this.children[0].opacity(opacity===0?0.5:(opacity===1?1:0));});\nstage.findOne('#choiceC').on('mouseover mouseout',function(){var opacity=this.children[0].opacity();this.children[0].opacity(opacity===0?0.5:(opacity===1?1:0));});\nstage.findOne('#choiceD').on('mouseover mouseout',function(){var opacity=this.children[0].opacity();this.children[0].opacity(opacity===0?0.5:(opacity===1?1:0));});"
print(output)
