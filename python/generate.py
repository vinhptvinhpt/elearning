#!/usr/bin/env python
# -*- coding: utf-8 -*-
import mysql.connector
from mysql.connector import Error
import sys
import os.path
import datetime
import time
import json
import textwrap
import random
from random import randint
import os
import time

# !/usr/bin/python
sys.path.append('/venv/Lib/site-packages')
from PIL import Image, ImageDraw, ImageFont
from mysql.connector import Error

# path
path = os.getcwd()
# connect to db
# path = 'D:\\Job\\elearning-easia\\python'
# path_gen_img = 'D:\\Job\\elearning-easia\\storage\\app\\public\\upload'

path = '/usr/share/nginx/html/source/phh/elearning-easia/python'
path_gen_img = '/usr/share/nginx/html/source/phh/elearning-easia/storage/app/public/upload'
path_gen_logo = '/usr/share/nginx/html/source/phh/elearning-easia/public/logo'
path_gen_signature = '/usr/share/nginx/html/source/phh/elearning-easia/public/signature'

bg_size_width = 705
bg_size_height = 1000
logo_size_width = 100
logo_size_height = 100
bg_resize_width = 487
bg_resize_height = 457

# D:\\Job\\elearning-easia\\python\\generate.py  D:\\Job\\elearning-easia\\python\\certificate.jpg 1571 u:1 w:0 c:0
if __name__ == '__main__':
    get_user_id = int(sys.argv[1])
    get_training_id = int(sys.argv[2])

    connection = mysql.connector.connect(host='10.2.1.130',
                                         database='easiaelearning',
                                         user='easia',
                                         password='E@sia2020',
                                         buffered=True)
    try:
        if connection.is_connected():
            cursor = connection.cursor()
            #lay anh chung chi mau
            sql_select_image = "select path, position from image_certificate where is_active = 1 and type = 1"
            #sql_select_image = "select path, position from image_certificate where id = 7"
            cursor = connection.cursor()
            cursor.execute(sql_select_image)
            record_image = cursor.fetchone()
            # region get image certificate path from db
            link_image = record_image[0].replace('/storage/upload/', '')
            path_image = os.path.join(path_gen_img, link_image)

            # open image
            img = Image.open(path_image)
            img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

            # width and height of image
            image_width, image_height = img.size
            #

            # determined position of text to fill
            x_pos = (image_width) / 2
            y_pos = (image_height) / 2
            x_code = (image_width) / 2
            y_code = (image_height) / 2
            # endregion

            coordinates = json.loads(record_image[1])

            logoX = coordinates["logoX"]
            logoY = coordinates["logoY"]

            logoSig1X = coordinates["logoSig1X"]
            logoSig1Y = coordinates["logoSig1Y"]

            logoSig2X = coordinates["logoSig2X"]
            logoSig2Y = coordinates["logoSig2Y"]

            fullnameX = coordinates["fullnameX"]
            fullnameY = coordinates["fullnameY"]
            fullnameSize = int(coordinates["fullnameSize"])
            fullnameWidth = coordinates["fullnameWidth"]
            fullnameHeight = coordinates["fullnameHeight"]

            dateX = coordinates["dateX"]
            dateY = coordinates["dateY"]

            programX = coordinates["programX"]
            programY = coordinates["programY"]
            programSize = int(coordinates["programSize"])

            image_new_width = int(coordinates["image_width"])
            image_new_height = int(coordinates["image_height"])

            dateSize = int(coordinates["dateSize"])
            #signSize = int(coordinates["signSize"])

            #signX = coordinates["signX"]
            #signY = coordinates["signY"]

            #signText = coordinates["sign_text"]
            textColor = coordinates["text_color"]


            # region get new position
            positon_name_X = image_width * fullnameX / image_new_width - 20
            positon_name_Y = image_height * fullnameY / image_new_height

            positon_program_X = image_width * programX / image_new_width- 20
            positon_program_Y = image_height * programY / image_new_height

            positon_logo_X = image_width * logoX / image_new_width
            positon_logo_Y = image_height * logoY / image_new_height

            positon_logosig1_X = image_width * logoSig1X / image_new_width
            positon_logosig1_Y = image_height * logoSig1Y / image_new_height

            positon_logosig2_X = image_width * logoSig2X / image_new_width
            positon_logosig2_Y = image_height * logoSig2Y / image_new_height

            positon_date_X = image_width * dateX / image_new_width- 20
            positon_date_Y = image_height * dateY / image_new_height - 10


            font_size_name_new = image_width * fullnameSize / image_new_width
            font_size_program_new = image_width * programSize / image_new_width
            font_size_date_new = image_width * 16 / image_new_width

            # region font for text
            # set font
            font_name = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=fullnameSize, encoding="unic")
            font_training = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=programSize, encoding="unic")
            font_date = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=dateSize, encoding="unic")
            #font_sign = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=signSize, encoding="unic")

            #region lay anh badge mau
            sql_select_badge = "select path, position from image_certificate where is_active = 1 and type = 2"
            #sql_select_image = "select path, position from image_certificate where id = 7"
            cursor = connection.cursor()
            cursor.execute(sql_select_badge)
            record_badge = cursor.fetchone()

            link_badge = record_badge[0].replace('/storage/upload/', '')
            path_badge = os.path.join(path_gen_img, link_badge)


            #lay toa do text tren mau huy hieu
            coordinates_badge = json.loads(record_badge[1])

            programX_badge = coordinates_badge["programX"]
            programY_badge = coordinates_badge["programY"]
            programSize_badge = int(coordinates_badge["programSize"])

            textColor_bg = coordinates_badge["text_color"]

            font_training_badge = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=programSize_badge)

            image_new_width_bg = coordinates_badge["image_width"]
            image_new_height_bg = coordinates_badge["image_height"]

            #resize badge ve kich thuoc da luu trong db
            #img_badge = Image.open(path_badge)
            #img_badge = img_badge.resize((image_new_width_bg, image_new_height_bg), Image.ANTIALIAS)

            fullnameWidth_bg = coordinates_badge["fullnameWidth"]
            fullnameHeight_bg = coordinates_badge["fullnameHeight"]

            #endregion

            if (get_user_id == 0):
                if (get_training_id == 0):
                    #sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 "
                    sql_select_Query = "select tud.user_id, tud.fullname as fullname, ttp.name as name,ttp.logo as logo, sc.timecertificate as timecertificate, sc.code as code, sc.id as sc_id, toe.organization_id,ttp.auto_certificate as auto_certificate,ttp.auto_badge as auto_badge, sc.auto_run, ttp.deleted, ttp.id as trainning_id from student_certificate as sc join tms_user_detail as tud on sc.userid = tud.user_id join tms_organization_employee as toe on toe.user_id = tud.user_id join tms_traninning_programs as ttp on ttp.id = sc.trainning_id where sc.status = 1"
                else:
                    sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and student_certificate.trainning_id = " + str(
                        get_training_id)
                cursor.execute(sql_select_Query)
                records = cursor.fetchall()

                num = 0
                limit = 50

                for row in records:

                    # 19/3/2020 uydd
                    user_id = row[0]
                    name = row[1].encode('utf-8').strip()
                    training_name = row[2]


                    timecertificate = row[4]
                    code = row[5]
                    student_certificate_id = row[6]

                    org_id = row[7]
                    auto_cer = row[8]
                    auto_badge = row[9]
                    auto_run = row[10]

                    deleted = row[11]
                    trainning_id = row[12]



                    if deleted == 2:
                        sql_select_course = "SELECT c.fullname FROM `tms_trainning_courses` ttc join mdl_course c on c.id = ttc.course_id where trainning_id = "+str(trainning_id)
                        cursor.execute(sql_select_course)
                        course_records = cursor.fetchall()

                        training_name = course_records[0][0]


                    try:
                        if auto_cer == 1 or (auto_run == 1 and auto_cer == 0):

                            path_cer = path_gen_img + '/certificate/'+code+ '_certificate.png'

                            if os.path.exists(path_cer):
                                try:
                                    os.remove(path_cer)
                                except OSError as e:
                                    print "Failed with cer:", e.strerror # look what it says
                                    print "Error code cer:", e.code

                            #remove image certificate new
                            path_cer_new = path_gen_img + '/certificate/'+code+ '_certificate.jpeg'

                            if os.path.exists(path_cer_new):
                                try:
                                    os.remove(path_cer_new)
                                except OSError as e:
                                    print "Failed with cer:", e.strerror # look what it says
                                    print "Error code cer:", e.code

                            # open image
                            img = Image.open(path_image)
                            img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

                            # width and height of image
                            image_width, image_height = img.size

                            #region in anh chung chi
                            #lay thong tin co cau to chuc
                            sql_query_org = "SELECT f.id, f.level, f.code FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id)FROM (SELECT @id := " + str(org_id) +") tmp1 JOIN tms_organization ON @id IS NOT NULL) tmp2 JOIN tms_organization f ON tmp2._id = f.id where f.level = 2 limit 1"
                            cursor.execute(sql_query_org)
                            data_org = cursor.rowcount

                            if data_org == 1:
                                record_org = cursor.fetchone()
                                org_name_root = record_org[2]
                            else:
                                sql_query_org = "SELECT f.id, f.level, f.code FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id)FROM (SELECT @id := " + str(org_id) +") tmp1 JOIN tms_organization ON @id IS NOT NULL) tmp2 JOIN tms_organization f ON tmp2._id = f.id where f.level = 1 limit 1"
                                cursor.execute(sql_query_org)
                                record_org = cursor.fetchone()
                                org_name_root = record_org[2]

                            #logo
                            logo_name = "phh.png"
                            org_name_lower = org_name_root.lower()

                            if("ea" in org_name_lower):
                                logo_name = "easia.png"
                            elif("bg" in org_name_lower):
                                logo_name = "begodi.png"
                            elif("ev" in org_name_lower):
                                logo_name = "exotic.png"
                            elif("av" in org_name_lower):
                                logo_name = "avana.png"
                            #elif("phh" in org_name_lower):
                             #   logo_name = "phh.png"

                            path_logo = os.path.join(path_gen_logo, logo_name)
                            logo = Image.open(path_logo)
                            logo_size_width, logo_size_height = logo.size

                            logo_size_width = logo_size_width / 4
                            logo_size_height = logo_size_height / 4

                            logo = logo.resize((logo_size_width, logo_size_height), Image.ANTIALIAS)

                            #signature
                            path_logo_sig1 = os.path.join(path_gen_signature, "dean.png")

                            signature1 = Image.open(path_logo_sig1)
                            logosig1_size_width, logosig1_size_height = signature1.size

                            #logosig1_size_width = logosig1_size_width / 1.5
                            #logosig1_size_height = logosig1_size_height / 1.5
                            signature1 = signature1.resize((logosig1_size_width, logosig1_size_height), Image.ANTIALIAS)


                            path_logo_sig2 = os.path.join(path_gen_signature, "director.png")

                            signature2 = Image.open(path_logo_sig2)
                            logosig2_size_width, logosig2_size_height = signature2.size

                            logosig2_size_width = logosig2_size_width / 8
                            logosig2_size_height = logosig2_size_height / 8
                            signature2 = signature2.resize((logosig2_size_width, logosig2_size_height), Image.ANTIALIAS)

                            name_utf8 = name.decode('utf8')

                            name_utf8_training = training_name + " training"
                            #name_utf8_training = "Leading with emotional intelligence"
                            #name_utf8_training = name_utf8_training.upper()

                            length_trainning = len(name_utf8_training)

                            # open image
                            img = Image.open(path_image)
                            img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

                            # region fill text
                            # create the canvas
                            canvas = ImageDraw.Draw(img)

                            # set datetime
                            now = datetime.datetime.now()
                            strDate = str(now.day) + '/' + str(now.month) + '/' + str(now.year)
                            canvas.text((positon_date_X, positon_date_Y), strDate, font=font_date, fill=textColor)
                            # endregion

                            #region hien thi chu ky tren anh
                            #canvas.text((signX, signY+30), signText, font=font_sign, fill=textColor)
                            #endregion

                            #region xu ly hien thi ten khung nang luc tren anh

                            lines = []

                            # If the width of the text is smaller than image width
                            # we don't need to split it, just add it to the lines array
                            # and return
                            words = name_utf8_training.split(' ')

                            if (length_trainning >= 46 and programSize >= 30):
                                programSize = 24
                                font_training = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=programSize, encoding="unic")


                            if font_training.getsize(name_utf8_training)[0] <= bg_size_width:
                                lines.append(name_utf8_training)
                            else:
                                # split the line by spaces to get words
                                i = 0
                                # append every word to a line while its width is shorter than image width
                                while i < len(words):
                                    line = ''
                                    while i < len(words) and font_training.getsize(line + words[i])[0] <= bg_size_width-100:
                                        line = line + words[i] + " "
                                        i += 1
                                    if not line:
                                        line = words[i]
                                        i += 1
                                    # when the line gets longer than the max width do not append the word,
                                    # add the line to the lines array
                                    lines.append(line)

                            # create the canvas
                            line_height = font_training.getsize('hg')[1]

                            y = programY
                            for line in lines:
                                w, h = canvas.textsize(line,font_training)
                                canvas.text(((image_new_width-w+50)/2, y), line, font=font_training,fill=textColor)
                                y = y + line_height + 20
                            #endregion

                            #region xu ly hien thi ten hoc lien tren anh
                            line_names = []

                            # If the width of the text is smaller than image width
                            # we don't need to split it, just add it to the lines array
                            # and return
                            word_names = name_utf8.split(' ')

                            if font_name.getsize(name_utf8)[0] <= bg_size_width:
                                line_names.append(name_utf8)
                            else:
                                # split the line by spaces to get words
                                i = 0
                                # append every word to a line while its width is shorter than image width
                                while i < len(word_names):
                                    line = ''
                                    while i < len(word_names) and font_name.getsize(line + word_names[i])[0] <= bg_size_width-100:
                                        line = line + word_names[i] + " "
                                        i += 1
                                    if not line:
                                        line = word_names[i]
                                        i += 1
                                    # when the line gets longer than the max width do not append the word,
                                    # add the line to the lines array
                                    line_names.append(line)

                            # create the canvas
                            line_height_name = font_name.getsize('hg')[1]

                            for line in line_names:
                                w, h = canvas.textsize(line,font_name)
                                canvas.text(((image_new_width-w+50)/2, fullnameY), line, font=font_name,fill=textColor)

                            #endregion

                            positon_logo_X = int(positon_logo_X)
                            positon_logo_Y = int(positon_logo_Y)

                            img.paste(logo, (positon_logo_X, positon_logo_Y))

                            positon_logosig1_X = int(positon_logosig1_X)
                            positon_logosig1_Y = int(positon_logosig1_Y)

                            img.paste(signature1, (positon_logosig1_X, positon_logosig1_Y))


                            positon_logosig2_X = int(positon_logosig2_X)
                            positon_logosig2_Y = int(positon_logosig2_Y)

                            img.paste(signature2, (positon_logosig2_X, positon_logosig2_Y))

                            # save image certificate
                            img.save(os.path.join(path_gen_img, 'certificate', code + '_certificate.png'))
                            #replace fill transparency with white color
                            imgA = Image.open(os.path.join(path_gen_img, 'certificate/'+code + '_certificate.png'))
                            new_image = Image.new("RGBA", imgA.size, "WHITE")  # Create a white rgba background
                            new_image.paste(imgA, (0, 0), imgA)
                            new_image.convert('RGB').save(os.path.join(path_gen_img, 'certificate', code + '_certificate.jpeg'), "JPEG")
                            os.remove(os.path.join(path_gen_img, 'certificate', code + '_certificate.png'))

                            time.sleep(0.3)
                            #endregion

                        if auto_badge == 1 or (auto_run == 1 and auto_badge == 0):

                            path_bag = path_gen_img + '/certificate/'+code+ '_badge.png'

                            if os.path.exists(path_bag):
                                try:
                                    os.remove(path_bag)
                                except OSError as e:
                                    print "Failed with:", e.strerror # look what it says
                                    print "Error code:", e.code

                            #remove image badge new
                            path_bag_new = path_gen_img + '/certificate/'+code+ '_badge.jpeg'

                            if os.path.exists(path_bag_new):
                                try:
                                    os.remove(path_bag_new)
                                except OSError as e:
                                    print "Failed with cer:", e.strerror # look what it says
                                    print "Error code cer:", e.code

                            #khoi tao bien tai day de tranh hien tuong bien bi giu lai sau khi gen anh xong, tam thoi fix
                            img_badge = Image.open(path_badge)
                            img_badge = img_badge.resize((bg_resize_width, bg_resize_height), Image.ANTIALIAS)
                            #region in anh huy hieu
                            name_utf8_training = training_name

                            text = name_utf8_training

                            max_width = image_new_width_bg



                            lines = []

                            # If the width of the text is smaller than image width
                            # we don't need to split it, just add it to the lines array
                            # and return
                            words = text.split(' ')

                            length_text = len(text)

                            distance_2way_max = 320
                            distance_2way = 80
                            distance_line = 100
                            center_pos = 0
                            #programSize_badge = 36

                            # if programSize_badge >=40:
                            #     programSize_badge = 36
                            
                            if (length_text >= 62):
                                center_pos = 3
                                programSize_badge = 24
                                distance_2way_max = 360
                                distance_2way = 240
                                distance_line = 80
                            elif (length_text >= 46 and length_text < 62):
                                center_pos = 4
                                programSize_badge = 28
                                distance_2way_max = 360
                                distance_2way = 240
                                distance_line = 80
                                #print('433: '+code)
                            elif (length_text >= 35 and length_text < 46):
                                center_pos = 2
                                programSize_badge = 30
                                distance_2way_max = 350
                                distance_2way = 240
                                distance_line = 80
                                #print('448: '+code)
                            elif (length_text >= 20 and length_text < 35):
                                center_pos = 2
                                programSize_badge = 38
                                distance_2way_max = 350
                                distance_2way = 150
                            else:
                                center_pos = 1
                                programSize_badge = 36
                                distance_2way_max = 260
                                distance_2way = 60

                            font = ImageFont.truetype(os.path.join(path, 'SVN-Aleo-Regular.otf'), size=programSize_badge, encoding="unic")

                            if len(words) <= 2:
                                lines.append(text)
                                # split the line by spaces to get words
                                # for wd in words:
                                #     lines.append(wd)
                            else:
                                if font.getsize(text)[0] <= max_width - distance_2way_max:
                                    lines.append(text)
                                else:
                                    # split the line by spaces to get words
                                    i = 0
                                    # append every word to a line while its width is shorter than image width
                                    while i < len(words):
                                        line = ''
                                        while i < len(words) and font.getsize(line + words[i])[0] <= max_width-distance_2way:
                                            line = line + words[i] + " "
                                            i += 1
                                        if not line:
                                            line = words[i]
                                            i += 1
                                        # when the line gets longer than the max width do not append the word,
                                        # add the line to the lines array
                                        lines.append(line)

                            # create the canvas
                            canvas = ImageDraw.Draw(img_badge)
                            line_height = font.getsize('hg')[1]

                            # y = 0

                            y_min = (programY_badge * 140) // 100   # 150% from the top
                            y_max = (programY_badge * 175) //100   # 175% to the bottom
                            # ran_y = randint(y_min, y_max)      # Generate random point
                            # y = ran_y
                            sub_dis = 0
                            if center_pos == 2:
                                # sub_dis = -40
                                y_min = (programY_badge * 130) // 100   # 150% from the top
                            elif center_pos == 3:
                                sub_dis = 70
                            elif center_pos == 1:
                                sub_dis = 60
                            elif center_pos == 4:
                                sub_dis = 100

                            y = int(y_min)
                            # print('543'+ str(lines))
                            for line in lines:
                                w, h = canvas.textsize(line,font)
                                if center_pos == 1:
                                    canvas.text(((image_new_width_bg-w)/2, (y+distance_line-line_height-sub_dis)/2), line, font=font,fill=textColor_bg)
                                else:
                                    # print('549')
                                    canvas.text(((image_new_width_bg-w)/2, (y-h-sub_dis)/2), line, font=font,fill=textColor_bg)
                                    y = y + line_height + distance_line

                            #save image badge
                            img_badge.save(os.path.join(path_gen_img, 'certificate', code + '_badge.png'))
                            #replace fill transparency with white color
                            imgA = Image.open(os.path.join(path_gen_img, 'certificate/'+code + '_badge.png'))
                            new_image = Image.new("RGBA", imgA.size, "WHITE")  # Create a white rgba background
                            new_image.paste(imgA, (0, 0), imgA)
                            new_image.convert('RGB').save(os.path.join(path_gen_img, 'certificate', code + '_badge.jpeg'), "JPEG")
                            os.remove(os.path.join(path_gen_img, 'certificate', code + '_badge.png'))

                            time.sleep(0.3)
                            #endregion


                        sql = """UPDATE student_certificate SET status = 2 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                        sql_update_confirm = """UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = %s"""
                        cursor.execute(sql_update_confirm, (get_user_id,))
                    except Exception, e:  # xu ly chuyen trang thai cho cac ban ghi bi loi
                        print(e)
                        sql = """UPDATE student_certificate SET status = 3 WHERE id = %s"""

                        cursor.execute(sql, (student_certificate_id,))

                    #gian cach thoi gian chay script
                    time.sleep(0.3)
                num = num + 1
                if num >= limit:
                    connection.commit()
                    num = 0

                connection.commit()
                print('luu nhieu anh thanh cong')
            else:
                # 19/3/2020 uydd
                sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and student_certificate.userid = " + str(get_user_id)
                # cursor = connection.cursor()
                cursor.execute(sql_select_Query)
                rows = cursor.fetchall()
                for row in rows:
                    # 2/11/2019
                    now = datetime.datetime.now()
                    user_id = row[0]
                    name = row[1].encode('utf-8').strip()
                    training_name = row[2]
                    link_logo = row[3].replace('/storage/upload/', '')
                    timecertificate = row[4]
                    code = row[5]
                    student_certificate_id = row[6]
                    path_logo = os.path.join(path_gen_img, link_logo)

                    try:
                        # open image
                        img = Image.open(path_image)
                        img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

                        logo = Image.open(path_logo)
                        logo = logo.resize((logo_size_width, logo_size_height), Image.ANTIALIAS)

                        # name to utf8
                        name_utf8 = name.decode('utf8')
                        name_utf8_training = training_name

                        # region fill text
                        # create the canvas
                        canvas = ImageDraw.Draw(img)

                        # set datetime
                        now = datetime.datetime.now()
                        strDate = str(now.day) + '/' + str(now.month) + '/' + str(now.year)
                        canvas.text((positon_date_X, positon_date_Y), strDate, font=font_date, fill='#111111')
                        # endregion

                        # # get width and height of text
                        # name_width, name_height = canvas.textsize(name_utf8, font=font_name)
                        # training_width, training_height = canvas.textsize(name_utf8_training, font=font_training)

                        # set name program to position
                        canvas.text((positon_program_X, positon_program_Y), name_utf8_training, font=font_training, fill='#111111')
                        # set name user to postion
                        # canvas.text((positon_name_X, positon_name_Y), name_utf8, font=font_name, fill='#111111')
                        width_cut = int(fullnameWidth/fullnameSize)*2 + 2
                        if fullnameHeight > fullnameSize:
                            for line in textwrap.wrap(name_utf8.encode('utf8'), width=width_cut):
                              canvas.text((positon_name_X, positon_name_Y), line.decode('utf8'), font=font_name, fill='#111111')
                              positon_name_Y += font_name.getsize(line)[1]+15
                        else:
                            canvas.text((positon_name_X, positon_name_Y), name_utf8, font=font_name, fill='#111111')

                        img.paste(logo, (positon_logo_X, positon_logo_Y))

                        # save image
                        img.save(os.path.join(path_gen_img, 'certificate', code + '.png'))
                        #replace fill transparency with white color
                        imgA = Image.open(os.path.join(path_gen_img, 'certificate/'+code + '.png'))
                        new_image = Image.new("RGBA", imgA.size, "WHITE")  # Create a white rgba background
                        new_image.paste(imgA, (0, 0), imgA)
                        new_image.convert('RGB').save(os.path.join(path_gen_img, 'certificate', code + '.jpeg'), "JPEG")
                        os.remove(os.path.join(path_gen_img, 'certificate', code + '.png'))

                        #update db
                        sql = """UPDATE student_certificate SET status = 2 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                        sql_update_confirm = """UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = %s"""
                        cursor.execute(sql_update_confirm, (get_user_id,))
                    except Error as e:  # xu ly chuyen trang thai cho cac ban ghi bi loi
                        sql = """UPDATE student_certificate SET status = 3 WHERE id = %s"""
                        #cursor.execute(sql, (student_certificate_id,))
                connection.commit()
                print('luu anh thanh cong')
    except Error as e:
        print("Error while connecting to MySQL", e)
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("Finish!!!")
