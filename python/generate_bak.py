#!/usr/bin/env python
# -*- coding: utf-8 -*-
import mysql.connector
from mysql.connector import Error
import sys
import os.path
import datetime
import time

# !/usr/bin/python
sys.path.append('/venv/Lib/site-packages')
from PIL import Image, ImageDraw, ImageFont
from mysql.connector import Error

# path
path = os.getcwd()
# connect to db
path = 'D:\\Job\\elearning-easia\\python'
path_gen_img = 'D:\\Job\\elearning-easia\\storage\\app\\public\\upload'
#D:\\Job\\elearning-easia\\python\\generate.py  D:\\Job\\elearning-easia\\python\\certificate.jpg 1571 u:1 w:0 c:0
if __name__ == '__main__':
    # get_user_id = int(sys.argv[2])
    get_user_id = 1571
    connection = mysql.connector.connect(host='localhost',
                                                 database='elearning-easi',
                                                 user='root',
                                                 password='',
                                                 buffered=True)
    try:


        if connection.is_connected():
            cursor = connection.cursor()
            sql_select_image = "select path from image_certificate where is_active = 1"
            cursor = connection.cursor()
            cursor.execute(sql_select_image)
            record_image = cursor.fetchone()
            link_image = record_image[0].replace('\storage\upload\\', '')
            path_image = os.path.join(path_gen_img, link_image)

            if (get_user_id == 0):
                sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, student_certificate.code as code from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join mdl_user on mdl_user.id = tms_user_detail.user_id where student_certificate.status = 1 "
                cursor.execute(sql_select_Query)
                records = cursor.fetchall()
                for row in records:
                    # 2/11/2019
                    #
                    user_id = row[0]
                    name = row[1]
                    code = row[2]
                    try:

                        # name = row[1].encode('utf-8').strip()
                        now = datetime.datetime.now()
                        sql_get_branch = "select b.name, b.address from tms_branch as b  JOIN tms_branch_sale_room as bsr on b.id = bsr.branch_id JOIN tms_sale_rooms as sr on sr.id = bsr.sale_room_id JOIN tms_sale_room_user as sru on sru.sale_room_id = sr.id WHERE sru.user_id = " + str(
                            user_id)
                        cursor.execute(sql_get_branch)
                        branch = cursor.fetchone()
                        if branch == None:
                            branch_name = "Hà Nội"
                            branch_address = "Hà Nội"
                        else:
                            branch_name = branch[0]
                            branch_address = branch[1].encode('utf-8').strip()

                        if not branch_name:
                            branch_name = "Hà Nội"

                        if not branch_address:
                            branch_address = "Hà Nội"

                        # print record
                        # if os.path.exists(os.path.join(path, 'certificate', code+'.png')):
                        # if os.path.exists('E:\\Python\\certificate\\' + code + '.png'):
                        # print('Da ton tai')
                        # else:
                        # img = Image.open("E:\\Python\\certificate.png")
                        img = Image.open(path_image)
                        # width and height of image
                        image_width, image_height = img.size
                        # name to utf8
                        name_utf8 = name
                        name_utf8_branch = branch_name
                        # name_utf8_branch = branch_name.decode('utf8')
                        address_utf8_branch = branch_address.decode('utf8')
                        # create the canvas
                        canvas = ImageDraw.Draw(img)
                        # set font
                        font_name = ImageFont.truetype(os.path.join(path, 'MyriadPro-Semibold.otf'), size=150)
                        font_branch = ImageFont.truetype(os.path.join(path, 'MyriadPro-Semibold.otf'), size=115)
                        font_code = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=90)
                        font_date = ImageFont.truetype(os.path.join(path, 'MyriadPro-SemiboldIt.otf'), size=100)
                        # get width and height of text
                        text_width, text_height = canvas.textsize(name_utf8, font=font_name)
                        code_width, code_height = canvas.textsize(code, font=font_name)

                        # set code to position
                        # canvas.text((x_code, y_code), code, font=font_code, fill='BLACK')
                        canvas.text((2105, 1595), "QC " + code, font=font_code, fill='#060836')

                        # set datetime
                        canvas.text((3150, 2282), str(now.day), font=font_date, fill='#060836')
                        canvas.text((3525, 2282), str(now.month), font=font_date, fill='#060836')
                        canvas.text((3975, 2282), str(now.year)[-2:], font=font_date, fill='#060836')
                        # set name to position
                        canvas.text((3450, 700), name_utf8_branch.upper(), font=font_branch, fill='#060836')
                        # canvas.text((2105, 1585), address_utf8_branch, font=font_name, fill='#060836')
                        canvas.text(((image_width - text_width) / 2, (image_height - text_height) / 2 - 450),
                                    name_utf8.upper(), font=font_name, fill='#060836')
                        # save image
                        img.save(os.path.join(path_gen_img, 'certificate', code + '.png'))
                        sql_update_confirm = "UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = " + str(
                            user_id)
                        cursor.execute(sql_update_confirm)
                        sql = "UPDATE student_certificate SET status = 2 WHERE status = 1 and userid = " + str(user_id)
                        cursor.execute(sql)
                        time.sleep(0.3)
                    except:  # xu ly chuyen trang thai cho cac ban ghi bi loi
                        sql = "UPDATE student_certificate SET status = 3 WHERE status = 1 and userid = " + str(user_id)
                        cursor.execute(sql)
                        print('error')
                connection.commit()
                print('luu nhieu anh thanh cong')
            else:
                sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, student_certificate.code as code from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join mdl_user on mdl_user.id = tms_user_detail.user_id where student_certificate.userid = " + str(
                    get_user_id)
                # cursor = connection.cursor()
                cursor.execute(sql_select_Query)
                records = cursor.fetchall()
                for row in records:
                    # 2/11/2019
                    now = datetime.datetime.now()
                    sql_get_branch = """select b.name, b.address from tms_branch as b  JOIN tms_branch_sale_room as bsr on b.id = bsr.branch_id JOIN tms_sale_rooms as sr on sr.id = bsr.sale_room_id JOIN tms_sale_room_user as sru on sru.sale_room_id = sr.id WHERE sru.user_id = %s"""
                    cursor.execute(sql_get_branch, (get_user_id,))
                    branch = cursor.fetchone()
                    if branch == None:
                        branch_name = "Hà Nội"
                    else:
                        branch_name = branch[0]
                        branch_address = branch[1].encode('utf-8').strip()
                    #
                    user_id = row[0]
                    # name = row[1]
                    code = row[2]
                    name = row[1].encode('utf-8').strip()
                    # print record
                    # if os.path.exists(os.path.join(path, 'certificate', code+'.png')):
                    # if os.path.exists('E:\\Python\\certificate\\' + code + '.png'):
                    # print('Da ton tai')
                    # else:
                    # img = Image.open("E:\\Python\\certificate.png")
                    img = Image.open(path_image)
                    # width and height of image
                    image_width, image_height = img.size
                    # name to utf8
                    name_utf8 = name.decode('utf8')
                    name_utf8_branch = branch_name
                    # name_utf8_branch = branch_name.decode('utf8')
                    address_utf8_branch = branch_address.decode('utf8')
                    # create the canvas
                    canvas = ImageDraw.Draw(img)
                    # set font
                    font_name = ImageFont.truetype(os.path.join(path, 'TSDBOR+MyriadPro-Bold.ttf'), size=150)
                    font_branch = ImageFont.truetype(os.path.join(path, 'MyriadPro-Semibold.otf'), size=115)
                    font_code = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=90)
                    font_date = ImageFont.truetype(os.path.join(path, 'MyriadPro-SemiboldIt.otf'), size=100)
                    # get width and height of text
                    text_width, text_height = canvas.textsize(name_utf8, font=font_name)
                    code_width, code_height = canvas.textsize(code, font=font_name)
                    # determined position of text to fill
                    x_pos = (image_width) / 2
                    y_pos = (image_height) / 2
                    x_code = (image_width) / 2
                    y_code = (image_height) / 2
                    # set code to position
                    # canvas.text((x_code, y_code), code, font=font_code, fill='BLACK')
                    canvas.text((2105, 1595), "QC " + code, font=font_code, fill='#060836')

                    # set datetime
                    canvas.text((3150, 2282), str(now.day), font=font_date, fill='#060836')
                    canvas.text((3525, 2282), str(now.month), font=font_date, fill='#060836')
                    canvas.text((3975, 2282), str(now.year)[-2:], font=font_date, fill='#060836')
                    # set name to position
                    canvas.text((3450, 700), name_utf8_branch.upper(), font=font_branch, fill='#060836')
                    # canvas.text((2105, 1585), address_utf8_branch, font=font_name, fill='#060836')
                    canvas.text(((image_width - text_width) / 2, (image_height - text_height) / 2 - 450),
                                name_utf8.upper(), font=font_name, fill='#060836')
                    # save image
                    img.save(os.path.join(path_gen_img, 'certificate', code + '.png'))
                sql = """UPDATE student_certificate SET status = 2 WHERE userid = %s"""
                cursor.execute(sql, (get_user_id,))
                sql_update_confirm = """UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = %s"""
                cursor.execute(sql_update_confirm, (get_user_id,))
                connection.commit()
                print('luu anh thanh cong')

    except Error as e:
        print("Error while connecting to MySQL", e)
    finally:
        if (connection.is_connected()):
            cursor.close()
            connection.close()
            print("MySQL connection is closed")
