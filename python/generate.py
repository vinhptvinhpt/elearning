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
#path = 'D:\\Job\\elearning-easia\\python'
#path_gen_img = 'D:\\Job\\elearning-easia\\storage\\app\\public\\upload'
path = '/home/easia/elearning-easia/python'
path_gen_img = '/home/easia/elearning-easia/storage/app/public/upload'
bg_size_width = 705
bg_size_height = 1000
logo_size_width = 100
logo_size_height = 100

#D:\\Job\\elearning-easia\\python\\generate.py  D:\\Job\\elearning-easia\\python\\certificate.jpg 1571 u:1 w:0 c:0
if __name__ == '__main__':
    get_user_id = int(sys.argv[1])
    get_training_id = int(sys.argv[2])
    connection = mysql.connector.connect(host='localhost',
                                                 database='easia',
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

            #region get image certificate path from db
            link_image = record_image[0].replace('/storage/upload/', '')
            path_image = os.path.join(path_gen_img, link_image)

            #open image
            img = Image.open(path_image)
            img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

            # width and height of image
            image_width, image_height = img.size

            # determined position of text to fill
            x_pos = (image_width) / 2
            y_pos = (image_height) / 2
            x_code = (image_width) / 2
            y_code = (image_height) / 2
            #endregion

            #region font for text
            # set font
            font_name = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=40)
            font_training = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=35)
            font_date = ImageFont.truetype(os.path.join(path, 'Lato-Bold.ttf'), size=17)
            #endregion

            if(get_user_id == 0):
                if(get_training_id == 0):
                    sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 "
                else:
                    sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and student_certificate.trainning_id = " +str(get_training_id)
                cursor.execute(sql_select_Query)
                records = cursor.fetchall()
                for row in records:
                    # 19/3/2020 uydd
                    user_id = row[0]
                    name = row[1].encode('utf-8').strip()
                    training_name = row[2]
                    link_logo = row[3].replace('/storage/upload/', '')
                    timecertificate = row[4]
                    code = row[5]
                    student_certificate_id = row[6]
                    path_logo = os.path.join(path_gen_img, link_logo)
                    try:
                        logo = Image.open(path_logo)
                        logo = logo.resize((logo_size_width, logo_size_height), Image.ANTIALIAS)
                        # name to utf8
                        name_utf8 = name.decode('utf8')
                        name_utf8_training = training_name

                        # open image
                        img = Image.open(path_image)
                        img = img.resize((bg_size_width, bg_size_height), Image.ANTIALIAS)

                        # region fill text
                        # create the canvas
                        canvas = ImageDraw.Draw(img)

                        # set datetime
                        now = datetime.datetime.now()
                        canvas.text((390, 730), str(now.day) + '/', font=font_date, fill='#111111')
                        canvas.text((419, 730), str(now.month) + '/', font=font_date, fill='#111111')
                        x_year = 440 if len(str(now.month)) == 1 else 448
                        canvas.text((x_year, 730), str(now.year), font=font_date, fill='#111111')
                        # endregion

                        # get width and height of text
                        name_width, name_height = canvas.textsize(name_utf8, font=font_name)
                        training_width, training_height = canvas.textsize(name_utf8_training, font=font_training)

                        # set name to position
                        canvas.text(((image_width - training_width) / 2, 560), name_utf8_training,
                                    font=font_training, fill='#111111')
                        # canvas.text((2105, 1585), address_utf8_branch, font=font_name, fill='#060836')
                        canvas.text(((image_width - name_width - 20) / 2, 440),
                                    name_utf8.upper(), font=font_name, fill='#111111')
                        img.paste(logo, (90, 70))
                        # save image
                        img.save(os.path.join(path_gen_img, 'certificate', code + '.png'))
                        sql = """UPDATE student_certificate SET status = 2 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                        sql_update_confirm = """UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = %s"""
                        cursor.execute(sql_update_confirm, (get_user_id,))
                    except Exception, e:  # xu ly chuyen trang thai cho cac ban ghi bi loi
                        sql = """UPDATE student_certificate SET status = 3 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                connection.commit()
                print('luu nhieu anh thanh cong')
            else:
                # 19/3/2020 uydd
                sql_select_Query = "select tms_user_detail.user_id, tms_user_detail.fullname as fullname, tms_traninning_programs.name as name, tms_traninning_programs.logo as logo, student_certificate.timecertificate as timecertificate, student_certificate.code as code, student_certificate.id as student_certificate_id from student_certificate join tms_user_detail on student_certificate.userid = tms_user_detail.user_id join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and student_certificate.userid = " + str(get_user_id)
                # cursor = connection.cursor()
                cursor.execute(sql_select_Query)
                row = cursor.fetchone()
                if cursor.rowcount == 1:
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
                        canvas.text((390, 730), str(now.day) + '/', font=font_date, fill='#111111')
                        canvas.text((419, 730), str(now.month) + '/', font=font_date, fill='#111111')
                        x_year = 440 if len(str(now.month)) == 1 else 448
                        canvas.text((x_year, 730), str(now.year), font=font_date, fill='#111111')
                        # endregion

                        # get width and height of text
                        name_width, name_height = canvas.textsize(name_utf8, font=font_name)
                        training_width, training_height = canvas.textsize(name_utf8_training, font=font_training)

                        # set name to position
                        canvas.text(((image_width - training_width) / 2, 560), name_utf8_training,
                                    font=font_training, fill='#111111')
                        # canvas.text((2105, 1585), address_utf8_branch, font=font_name, fill='#060836')
                        canvas.text(((image_width - name_width - 20) / 2, 440),
                                    name_utf8, font=font_name, fill='#111111')

                        img.paste(logo, (90, 70))

                        # save image
                        img.save(os.path.join(path_gen_img, 'certificate', code + '.png'))
                        sql = """UPDATE student_certificate SET status = 2 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                        sql_update_confirm = """UPDATE tms_user_detail SET confirm = 1 WHERE tms_user_detail.id = %s"""
                        cursor.execute(sql_update_confirm, (get_user_id,))
                    except:  # xu ly chuyen trang thai cho cac ban ghi bi loi
                        sql = """UPDATE student_certificate SET status = 3 WHERE id = %s"""
                        cursor.execute(sql, (student_certificate_id,))
                        print('error')
                connection.commit()
                print('luu anh thanh cong')
    except Error as e:
        print("Error while connecting to MySQL", e)
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection is closed")
