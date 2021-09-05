# -*- coding: utf-8 -*-

import numpy as np
import random
import json
import sys

namelist = ['กานพลู', 'ก้ามปู', 'กาละแม', 'กอหญ้า', 'กองทัพ', 'กุ้งนาง', 'กะทิ', 'กำไล', 'ขวัญข้าว', 'ข้าวหอม', 'ข้าวปั้น', 'ขนมผิง', 'ขุนพล', 'ขลุ่ยผิว', 'คิมหันต์', 'จอมขวัญ', 'จำปูน', 'จันทร์เจ้า', 'จันทร์กะพ้อ', 'ใจดี', 'จริงใจ', 'เจ้าขุน', 'เจ้านาย', 'ช้องนาง', 'ช่อม่วง', 'ชะเอม', 'ชบาไพร', 'ซองพลู', 'ดั่งใจ', 'ดีใจ', 'ต้นน้ำ', 'ต้นกล้า', 'ต้นหลิว', 'ต้นแต้ว', 'แตงไทย', 'เตยหอม', 'ตะวัน', 'ทอฝัน', 'ทองปราย', 'ทองภู', 'ทานธรรม', 'แทนไท', 'เทียนหอม', 'ทะนาน', 'ธรรมะ', 'นะดี', 'นะโม', 'น้ำปิง', 'น้ำอบ',
            'น้ำเหนือ', 'นิ้ม', 'แน่งน้อย', 'ใบข้าว', 'ใบตอง', 'ใบเตย', 'ใบบัว', 'ใบบุญ', 'บัวบูชา', 'ปราง', 'ปรายฟ้า', 'ปั้นหยา', 'ปั้นจั่น', 'เป็นแดน', 'แป้งร่ำ', 'แป้งหมี่', 'ปิ่นมุก', 'ปอแก้ว', 'เพียงออ', 'พุทโธ', 'พะแพง', 'แพรวา', 'พอใจ', 'พวงชมพู', 'ฟ้าใส', 'ภูผา', 'มะลิ', 'มันตา', 'ไม้ม้วน', 'เรไร', 'ลลิต', 'ลังลอง', 'ลัญจ์', 'ส้มจี๊ด', 'ส้มฉุน', 'ส้มซ่า', 'ส้มป่อย', 'ส้มลิ้ม', 'สิตางศุ์', 'สุขใจ', 'โสน', 'ไหมฝัน', 'สไบงาม', 'เอื้องเหนือ', 'ไออุ่น', 'อุ่นใจ', 'อุ่นเรือน', 'อันนา', 'อบเชย', 'อุ้มบุญ', 'เอื้อนเอ่ย']
fruitlist = [
    ['กระจับ', 'กระท้อน',  'กระท้อนขันทอง',  'กระท้อนทับทิม',  'กระท้อนปุยฝ้าย',  'กระท้อนอิล่า',  'กล้วย',  'กล้วยไข่',  'กล้วยนางยา',  'กล้วยน้ำว้า',  'กล้วยน้ำว้านวล',  'กล้วยเล็บมือนาง',  'กล้วยหอม',  'กล้วยหอมทอง',  'กล้วยหักมุก',  'กีวี',  'เก๋ากี้',  'แก้วมังกร',  'ขนุน',  'ขนุนจำปากรอบ',  'ขนุนทองประเสริฐ',  'ขนุนทองสุดใจ',  'ขนุนเหรียญบาท',  'แคนตาลูป',  'แครนเบอร์รี่',  'เงาะ',  'เงาะสีชมพู',  'ชมพู่',  'ชมพู่ทับทิมจันทร์',  'ชมพู่ทูลเกล้า',  'ชมพู่มะเหมี่ยว',  'เชอร์รี่',  'ตะขบ',  'ตาล',  'แตงไทย',  'แตงโม',  'แตงโมกินรี',  'แตงโมตอปิโด',  'ท้อ',  'ทุเรียน',  'น้อยหน่า',  'น้อยหน่าเนื้อ',  'น้อยหน่าหนัง',  'น้อยหน่าโหน่ง',  'บลูเบอร์รี่',  'บ๊วย',  'เบอรี่',  'แบล็กเคอร์เรนท์',  'แบล็กเบอร์รี่',  'ฝรั่ง',  'ฝรั่งกลมสาลี่',  'ฝรั่งกิมจู',  'ฝรั่งแป้งสีทอง',  'พลับ',  'พลัม',  'พุทรา',  'พุทรานม',  'พุทราสงวนทอง',  'พุทราสามรส',  'พุทราสาลี่',  'พุทราสีทอง',  'แพร',  'มะกอก',  'มะกอกดำ',  'มะกอกป่า',  'มะกอกฝรั่ง',  'มะขวิด',  'มะขาม',  'มะขามเทศ',  'มะขามหวานสีชมพู',  'มะขามหวานสีทอง',  'มะเขือเทศ',  'มะเดื่อ',  'มะตูม',  'มะปราง',  'มะพร้าว',  'มะพร้าวอ่อนน้ำหอม',  'มะเฟือง',  'มะไฟ',  'มะไฟไข่เต่า',  'มะไฟเหรียญทอง',  'มะม่วง',  'มะม่วงแก้ว',  'มะม่วงแก้วลืมรัง',
        'มะม่วงเขียวมรกต',  'มะม่วงเขียวเสวย',  'มะม่วงเขียวใหญ่',  'มะม่วงโชคอนันต์แก่',  'มะม่วงโชคอนันต์สุก',  'มะม่วงเบา',  'มะม่วงฟ้าลั่น',  'มะม่วงฟ้าลั่นสามพราน',  'มะม่วงมันเดือนเก้า',  'มะม่วงแรด',  'มะม่วงหนังกลางวัน',  'มะม่วงหิมพานต์',  'มะม่วงอกร่องดิบ',  'มะม่วงอกร่องทอง',  'มะม่วงอกร่องสุก',  'มะยงชิด',  'มะยม',  'มะละกอ',  'มะละกอแขกดำ',  'มะละกอฮอลแลนด์',  'มะละกอฮาวาย',  'มังคุด',  'มังคุดผิวคละ',  'มังคุดผิวมัน',  'ระกำ',  'ระกำหวาน',  'แรสเบอร์รี่',  'ลองกอง',  'ละมุด',  'ละมุดหวานกรอบ',  'ละมุดหวานสุก',  'ลางสาด',  'ลำไย',  'ลำไยกะโหลก',  'ลำไยสีชมพู',  'ลำไยแห้ว',  'ลำไยอีดอ',  'ลิ้นจี่',  'ลิ้นจี่ค่อม',  'ลิ้นจี่จักรพรรดิ์',  'ลิ้นจี่ฮงฮวย',  'สตรอเบอร์รี่',  'ส้ม',  'ส้มกัมควอท',  'ส้มเขียวหวาน',  'ส้มเขียวหวานสีน้ำตาล',  'ส้มเช้ง',  'ส้มโชกุล',  'ส้มฟรีมอค์',  'ส้มสายน้ำผึ้ง',  'ส้มสีทอง',  'ส้มเหนือ',  'ส้มโอ',  'ส้มโอขาวแตงกวา',  'ส้มโอขาวน้ำผึ้ง',  'ส้มโอขาวใหญ่แม่กลอง',  'สละ',  'สับปะรด',  'สับปะรดตราดสีทอง',  'สาลี่',  'สาลี่ก้านยาว',  'สาลี่น้ำผึ้ง',  'สาลี่หอม',  'สาลี่หิมะ',  'เสาวรส',  'หม่อน',  'หลุมพี',  'หว้า',  'องุ่น',  'องุ่นเขียว',  'องุ่นดำ',  'องุ่นพันธุ์แดง',  'อาโวคาโด',  'อินทผลัม',  'แอปเปิล',  'แอปเปิลเขียว',  'แอปเปิลแดง',  'แอปเปิลฟูจิ'],
    ['ผล', 'กิโล', 'ลูก']
]

# nounlist จะเป็น [list,list,list] แต่ตอนนี้มีแค่ผลไม้
nounList = [fruitlist]

grade = [1, 2]
op = ['plu','min','div','mul']

# แต่ละระดับชั้นจะได้โจทย์ยากไม่เท่ากัน เช่นป.1 มีแค่บวกลบเลขไม่เกิน 100
def generate_expression(grade,targetLevel=1):
    expression = []
    a = 0
    b = 0
    if(grade == 1):
        operation = random.choice(op[0:2])
        if (operation == 'min'):
            a = np.random.randint(0, 100 + 1)
            b = np.random.randint(0, 100 + 1)
            if (a > b):
                expression.append(a)
                expression.append(operation)
                expression.append(b)
            else:
                expression.append(b)
                expression.append(operation)
                expression.append(a)
        else:
            if(targetLevel == 1):
                a = np.random.randint(0, 9 + 1)
                b = np.random.randint(0, 9 + 1)
            elif(targetLevel == 2):
                a = np.random.randint(0, 9 + 1)
                b = np.random.randint(9, 100 + 1)
            elif(targetLevel == 3):
                a = np.random.randint(9, 100 + 1)
                b = np.random.randint(9, 100 + 1)
            expression.append(a)
            expression.append(operation)
            expression.append(b)
    elif(grade == 2):
        operation = random.choice(op)
        if (operation == 'min'):
            a = np.random.randint(0, 1000 + 1)
            b = np.random.randint(0, 1000 + 1)
            if (a > b):
                expression.append(a)
                expression.append(operation)
                expression.append(b)
            else:
                expression.append(b)
                expression.append(operation)
                expression.append(a)
        elif (operation == 'div'):
            a = np.random.randint(0, 99 + 1)
            b = np.random.randint(1, 9 + 1)
            while(a/b > 9):
                a = np.random.randint(0, 99 + 1)
                b = np.random.randint(1, 9 + 1)
            expression.append(a)
            expression.append(operation)
            expression.append(b)
        elif (operation == 'mul'):
            a = np.random.randint(0, 99 + 1)
            b = np.random.randint(0, 9 + 1)
            expression.append(a)
            expression.append(operation)
            expression.append(b)
        else:
            a = np.random.randint(0, 1000 + 1)
            b = np.random.randint(0, 1000 + 1)
            expression.append(a)
            expression.append(operation)
            expression.append(b)
    return expression

def get_digit(number, n):
    return number // 10**n % 10

def getLevel(expression,indicator):
    if(expression[1] == 'plu'):
        count = 0
        for i in range(3):
            a = get_digit(expression[0],i)
            b = get_digit(expression[2],i)
            if(a+b >= 10):
                count +=1
        if(int(indicator) == 1):
            if(count == 0 and a<10 and b<10):
                return 1
            elif(count == 0 and (a<10 or b<10)):
                return 2
            elif(count == 0 and a>=10 and b>=10):
                return 3
            elif(count == 1 and a<10 and b<10):
                return 4
            else:
                return 5
        else:
            if(count == 0):
                return 1
            elif(count == 1):
                return 2
            elif(count == 2):
                return 3
            else:
                return 4
    elif(expression[1] == 'min'):
        count = 0
        for i in range(3):
            a = get_digit(expression[0],i)
            b = get_digit(expression[2],i)
            if(a<b):
                count += 1
        if(int(indicator) == 1):
            if(count == 0 and a<10 and b<10):
                return 2
            elif(count == 0 and (a<10 or b<10)):
                return 3
            elif(count == 0 and a>=10 and b>=10):
                return 3
            elif(count == 1 and b<10):
                return 4
            else:
                return 5
        else:
            if(count == 0):
                return 1
            elif(count == 1):
                return 2
            else:
                return 3
    elif(expression[1] == 'mul'):
        count = 0
        for i in range(3):
            a = get_digit(expression[0],i)
            b = expression[2]
            if(a*b >= 10):
                count += 1
        if(count == 0):
            return 2
        elif(count == 1):
            return 3
        else:
            return 4
    else:
        #div
        if(expression[0]%expression[2] == 0 and expression[0]<10):
            return 3
        elif(expression[0]%expression[2] != 0 and expression[0]<10):
            return 4
        elif(expression[0]%expression[2] == 0 and expression[0]>=10):
            return 4
        else:
            return 5

#GET ARGUMENTS
f = open(sys.argv[1], "r")
argv = json.loads(f.read())
indicator = argv['0']

try:
    targetLevel = argv['1']
except IndexError:
    targetLevel = 0

ex = generate_expression(int(indicator))

# START GENERATING
initial = [('ซื้อ', 'มา'), ('มี', 'อยู่')]
# verb same = คำกิริยาของในปย.ที่มีตัวละครเดียว verb diff = ในปย.มีหลายตัวละคร
verb1same = []
verb1diff = []
# verb2 = คำขยาย เช่นให้ อีก เพิ่ม
verb2 = []
# conclude = คำสรุป เช่น รวม เหลือ
conclude = []
if (ex[1] == 'plu'):
    verb1same = ['ซื้อ']
    verb1diff = ['ให้']
    verb2 = ['เพิ่ม', 'อีก', 'มาอีก', 'เพิ่มมา']
    conclude = ['รวม', 'ทั้งหมด', 'รวมทั้งหมด']
elif (ex[1] == 'min'):
    verb1same = ['ขาย', 'ทำหาย']
    verb1diff = ['แบ่ง', 'แย่ง', 'เอาออก']
    verb2 = ['ไป']
    conclude = ['เหลือ']
elif (ex[1] == 'div'):
    verb1same = verb1diff = ['แจกจ่าย']
    verb2 = ['ไปให้', 'ให้']
    conclude = ['ได้']

name1 = random.choice(namelist)
name2 = random.choice(namelist)
if(name1 == name2):
    verb1 = verb1same
else:
    verb1 = verb1diff

init = random.choice(initial)

nounPack = random.choice(nounList)
# noun = สุ่มคำนาม mes = สุ่มลักษณะนาม
noun = random.choice(nounPack[0])
mes = random.choice(nounPack[1])
prob = ''
ans = ''
if(ex[1] == 'plu'):
    plusProb = [
        [name1, init[0], noun, init[1], ' ', str(ex[0]), ' ', mes, ' ', 'ต่อมา', name2, random.choice(verb1), random.choice(
            verb2), ' ', str(ex[2]), ' ', mes, ' สุดท้าย', name1, 'มี', noun, random.choice(conclude), 'กี่', mes]
    ]
if(ex[1] == 'min'):
    minProb = [[name1, init[0], noun, init[1], ' ', str(ex[0]), ' ', mes, ' ', 'ต่อมา', name2, random.choice(verb1), random.choice(verb2), ' ', str(ex[2]), ' ', mes, ' สุดท้าย', name1, 'มี', noun, random.choice(conclude), 'กี่', mes],
               [name1, init[0], noun, init[1], ' ', str(ex[0]), ' ', mes, ' ', name2, 'ต้อง', random.choice(verb1), random.choice(verb2), 'กี่', mes, ' ', name1, 'จึงจะมี', noun, random.choice(conclude), ' ', str(ex[2]), ' ', mes]]
if(ex[1] == 'div'):
    divProb = [[name1, init[0], noun, init[1], ' ', str(ex[0]), ' ', mes, ' ต่อมา', random.choice(verb1), random.choice(verb2), "เพื่อน", ' ', str(ex[2]), ' คน', ' เพื่อน', 'จะได้', noun, 'คน', 'ละกี่', mes, 'และ', name1, 'จะเหลือ', noun, 'กี่', mes],
               ]
if(ex[1] == 'mul'):
    mulProb = [['เพื่อน ', str(ex[0]), ' คน', ' ให้', noun, 'แก่', name1,
               'คนละ ', str(ex[2]), ' ', mes, ' ', name1, 'จะมี', noun, 'กี่', mes]]

show_ex = ''

if(ex[1] == 'plu'):
    show_ex = '+'
    prob = random.choice(plusProb)
    ans = str(ex[0]+ex[2])+' '+mes
    minus_choice = ex[0]-ex[2] if ex[0] > ex[2] else ex[2]-ex[0]
    fake_choices = [str(ex[0]+ex[2]-10)+' '+mes, str(ex[0]+ex[2]+10)+' '+mes,
                    str(minus_choice)+' '+mes, str(ex[0]+ex[2]-5)+' '+mes, str(ex[0]+ex[2]+5)+' '+mes]
elif(ex[1] == 'min'):
    show_ex = '-'
    prob = random.choice(minProb)
    ans = str(ex[0]-ex[2])+' '+mes
    fake_choices = [str(ex[0]-ex[2]-10)+' '+mes, str(ex[0]-ex[2]+10) +
                    ' '+mes, str(ex[0]-ex[2]-5)+' '+mes, str(ex[0]-ex[2]+5)+' '+mes]
elif(ex[1] == 'div'):
    show_ex = '÷'
    prob = random.choice(divProb)
    ans = str(ex[0]//ex[2])+' '+mes+' เหลือ ' + \
        str(ex[0]-(ex[2]*(ex[0]//ex[2])))+' '+mes
    fake_choices = [str((ex[0]//ex[2])-1)+' '+mes+' เหลือ ' +
                    str(ex[0]-(ex[2]*(ex[0]//ex[2])))+' '+mes, str((ex[0]//ex[2])-1)+' '+mes+' เหลือ ' +
                    str(ex[0]-(ex[2]*((ex[0]//ex[2])-1)))+' '+mes, str((ex[0]//ex[2])+1)+' '+mes+' เหลือ ' +
                    str(ex[0]-(ex[2]*(ex[0]//ex[2])))+' '+mes, str((ex[0]//ex[2])+1)+' '+mes+' เหลือ ' +
                    str(ex[0]-(ex[2]*((ex[0]//ex[2])-1)))+' '+mes, ]
else:
    show_ex = '×'
    prob = random.choice(mulProb)
    ans = str(ex[0]*ex[2])+' '+mes
    fake_choices = [str(ex[0]*(ex[2]+1))+' '+mes, str(ex[0]*(ex[2]-1)) +
                    ' '+mes, str((ex[0]+1)*ex[2])+' '+mes, str((ex[0]-1)*ex[2])+' '+mes]

# problem to building block
blocks = []
# add choices
choices_data = random.sample(fake_choices, 3)
ans_pos = np.random.randint(0, 4)
choices_data.insert(ans_pos, ans)
choices = {'idx': 0, 'content': choices_data, 'type': 'choice'}

blocks.append(choices)

for idx, word in enumerate(prob):
    if word in ['รวม','ทั้งหมด','รวมทั้งหมด']:
        blockType = 'plus_key'
    elif word in ['เหลือ']:
        blockType = 'min_key'
    elif word in ['คนละ']:
        blockType = 'mul_key'
    elif word in ['แจกจ่าย']:
        blockType = 'div_key'
    elif word in namelist:
        blockType = 'name'
    elif word in [item for sublist in nounList for item in sublist[0]]:
        blockType = 'object'
    elif word in [item for sublist in nounList for item in sublist[1]]:
        blockType = 'measurement'
    elif word.isdigit():
        blockType = 'number'
    else:
        blockType = 'other'
    block = {'idx': idx+1, 'content': word, 'type': blockType}
    blocks.append(block)

data = {}
data['question'] = json.dumps(blocks)
data['answer'] = ans
data['solution'] = json.dumps({"0":str(ex[0])+str(show_ex)+str(ex[2])+'='+str(ans)})
data['level'] = getLevel(ex,indicator)

print(json.dumps(data))
