export default {
    template: `<div class="screens">
        <div class="screen" :class="{ active: screenIndex === 0 }">
        <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 0px; font-size: 22px;">Báo Cáo</label>
         <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
        <button @click="$emit('event','error')" style="position: absolute; width: 60px; height: 35px; top: 45px; font-size: 16px; background-color: rgb(0, 25, 207); left: 450px;">
                                    In
                                </button>
                                <label style="position: absolute; left: 130px; font-size: 16px; top:50px;">Báo Cáo</label><textarea style="position:absolute; left: 130px; top: 80px; text-transform: uppercase; width: 380px; height: 280px; font-family: 'Courier New'; font-size:12px; resize: none; overflow-y: scroll;" readonly="">Báo cáo tổng số của n/v
BGT Vietlot
ID N/V bán hàng                       999999
04/09/17                               14:19
ID Điểm bán hàng                  9999999999

Bán hàng 4D          9999     99.999.999.999
Bán hàng 6/45        9999     99.999.999.999
Bán hàng 6/55        9999     99.999.999.999
Bán hàng keno        9999     99.999.999.999
Bán hàng 3d          9999     99.999.999.999
Tiền thưởng          9999     99.999.999.999
Y/c trả thưởng       9999     99.999.999.999
Vé 4D đã hủy         9999     99.999.999.999
Vé 6/45 đã hủy       9999     99.999.999.999
Vé 6/55 đã hủy       9999     99.999.999.999
Vé keno đã hủy       9999     99.999.999.999
Vé 3D đã hủy         9999     99.999.999.999
Tổng cộng                     99.999.999.999
</textarea><button @click="screenIndex++"  style="position: absolute; width: 154px; height: 40px; left: 3px; top: 390px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button><button @click="$emit('event','error')" style="position: absolute; left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">
                                    Trợ giúp
                                </button>
                                <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
        
        </div>
        <div class="screen" :class="{ active: screenIndex === 1 }">
            <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 0px; font-size: 22px;">Báo Cáo</label> 
             <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
            <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 35px;">Chọn báo cáo</label>
            <label class="custom-control custom-checkbox" style="position: absolute; left: 30px; top: 200px;">
            <input type="checkbox" class="custom-control-input" v-model="screenData[2].checkbox"> 
            <span class="custom-control-indicator"></span> 
            <span class="custom-control-description">[1] Báo Cáo Tổng Kết N/V Bán Hàng</span>
            </label>
                                <button @click="screenIndex++" style="position: absolute; width: 154px; height: 40px; left: 3px; top: 390px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button>
                                <button :disabled="!screenData[2].checkbox" @click="$emit('event','error')" class="submit-button" style="position: absolute; width: 154px; height: 40px; left: 163px; top: 390px; font-size: 14px; text-transform: uppercase;">
                                    Gửi
                                </button>
                                <button @click="$emit('event','error')" style="position: absolute; left: 483px; top: 390px; width: 154px; height: 40px; text-transform: uppercase; font-size: 14px; background-color: rgb(213, 49, 91);">
                                    Trợ giúp
                                </button> 
                <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
       
        </div>
        <div class="screen" :class="{ active: screenIndex === 2 }">
          <label style="text-align: center; width: 640px; left: 0px; top: 0px; font-size: 22px;">Danh Mục Chính</label>
        <button @click="$emit('event','error')" style="width: 50px; height: 30px; left: 590px; top: 0px; text-transform: uppercase; font-size: 12px;">
                                    Thoát
                                </button>
        <button disabled="disabled" style="width: 314px; height: 72px; left: 3px; top: 35px; font-size: 14px;">
                                    [1] Đăng Nhập
                                </button>
        <button @click="screenIndex++" style="width: 314px; height: 72px; left: 323px; top: 35px; font-size: 14px;">
                                    [2] Đăng Xuất
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 113px; font-size: 14px;">
                                    [3] Chức Năng Khách Hàng
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 113px; font-size: 14px;">
                                    [4] Báo Cáo
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 191px; font-size: 14px;">
                                    [5] Giải Đặc Biệt/Kết Quả
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 191px; font-size: 14px;">
                                    [6] Xem Lại
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 269px; font-size: 14px;">
                                    [7] Thay Đổi Mật Khẩu
                                </button><button style="width: 314px; height: 72px; left: 323px; top: 269px; font-size: 14px;">
                                    [8] Kiểm Tra
                                </button><button @click="$emit('event','error')" style="left: 323px; top: 347px; width: 314px; height: 72px; text-transform: uppercase; font-size: 16px; background-color: rgb(235, 62, 92);">
                                    Trợ Giúp
                                </button> 
                 <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
       
        </div>
        <div class="screen" :class="{ active: screenIndex === 3 }">
        <label style="text-align: center; width: 640px; position: absolute; left: 0; top: 0; font-size: 22px;">Đăng Xuất</label>
        <div style="background-color: #fff; font-size: 32px; width: 640px; position: absolute; top: 35px; left: 0; text-align: center; height: 350px; line-height: 350px;">Bấm GỬI để đăng xuất</div>
        <button @click="$emit('event','error')" style="position: absolute; left: 3px; top: 390px; width: 154px; height: 40px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button><button @click="screenIndex++" style="position: absolute; width: 154px; height: 40px; left: 163px; top: 390px; font-size: 14px; background-color: rgb(0, 255, 43); text-transform: uppercase;">
                                    Gửi
                                </button><button @click="$emit('event','error')" style="position: absolute; width: 154px; height: 40px; left: 483px; top: 390px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">
                                    Trợ giúp
                                </button>  
                 <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
       
        
        </div>
        <div class="screen" :class="{ active: screenIndex === 4 }">
        <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 0px; font-size: 22px;">Đăng Xuất</label>
         <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
        <button @click="$emit('message',{type:'success', message:'In báo cáo thành công.'});" style="position: absolute; left: 450px; top: 45px; width: 60px; height: 35px; font-size: 16px; background-color: rgb(0, 25, 207);">
                                    In
                                </button>
                                <label style="position: absolute; left: 130px; font-size: 16px; top:50px;">Đăng Xuất</label>
                                <textarea style="position:absolute; left: 130px; top: 80px; text-transform: uppercase; width: 380px; height: 280px; font-family: 'Courier New'; font-size:12px; resize: none; overflow-y: scroll;" readonly="">                                 (Đăng Xuất)
BGT Vietlot
ID N/V bán hàng                       999999
04/09/17                               14:19
ID Điểm bán hàng                  9999999999

Bán hàng 4D          9999     99.999.999.999
Bán hàng 6/45        9999     99.999.999.999
Bán hàng 6/55        9999     99.999.999.999
Bán hàng keno        9999     99.999.999.999
Bán hàng 3d          9999     99.999.999.999
Tiền thưởng          9999     99.999.999.999
Y/c trả thưởng       9999     99.999.999.999
Vé 4D đã hủy         9999     99.999.999.999
Vé 6/45 đã hủy       9999     99.999.999.999
Vé 6/55 đã hủy       9999     99.999.999.999
Vé keno đã hủy       9999     99.999.999.999
Vé 3D đã hủy         9999     99.999.999.999
Tổng cộng                     99.999.999.999
</textarea><button @click="screenIndex++" style="position: absolute; width: 154px; height: 40px; left: 3px; top: 390px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button><button @click="$emit('event','error')" style="position: absolute; width: 154px; height: 40px; left: 483px; top: 390px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">
                                    Trợ giúp
                                </button>           
         <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
        </div>
        <div class="screen" :class="{ active: screenIndex === 5 }">
        <label style="text-align: center; width: 640px; left: 0px; top: 0px; font-size: 22px;">Danh Mục Chính</label>
        <button @click="screenIndex++" style="width: 50px; height: 30px; left: 590px; top: 0px; text-transform: uppercase; font-size: 12px;">
                                    Thoát
                                </button>
        <button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 35px; font-size: 14px;">
                                    [1] Đăng Nhập
                                </button>
        <button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 35px; font-size: 14px;">
                                    [2] Đăng Xuất
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 113px; font-size: 14px;">
                                    [3] Chức Năng Khách Hàng
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 113px; font-size: 14px;">
                                    [4] Báo Cáo
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 191px; font-size: 14px;">
                                    [5] Giải Đặc Biệt/Kết Quả
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 191px; font-size: 14px;">
                                    [6] Xem Lại
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 269px; font-size: 14px;">
                                    [7] Thay Đổi Mật Khẩu
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 269px; font-size: 14px;">
                                    [8] Kiểm Tra
                                </button><button @click="$emit('event','error')" style="left: 323px; top: 347px; width: 314px; height: 72px; text-transform: uppercase; font-size: 16px; background-color: rgb(235, 62, 92);">
                                    Trợ Giúp
                                </button> 
        <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
        
        </div>
        <div class="screen" :class="{ active: screenIndex === 6 }">
            <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
            <label style="text-align: center; position: absolute; left: 5px; top: 35px;">Thoát khỏi ứng dụng?</label>
            <button @click="screenIndex++" style="position: absolute; width: 314px; height: 40px; left: 3px; top: 390px; font-size: 14px; background-color: rgb(191, 191, 191);">[1] Có</button>
            <button @click="$emit('event','error')" style="position: absolute; width: 314px; height: 40px; left: 323px; top: 390px; font-size: 14px; background-color: rgb(191, 191, 191);">[2] Không</button>
        <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
        </div>
        <div class="screen" :class="{ active: screenIndex === 7 }">
            <label style="text-align: center; width: 640px; position: absolute; left: 0; top: 0; font-size: 22px;">Bộ Nạp</label> 
            <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
            <label style="text-align: center; position: absolute; top: 35px; width: 640px;">Đang đọc tập tin 119</label>
            <textarea style="position: absolute; left: 10px; width: 620px; height: 300px; top: 70px;" readonly></textarea>
            <button @click="$emit('event','error')" style="position: absolute; width: 314px; height: 40px; left: 3px; top: 390px; font-size: 14px; background-color: rgb(191, 191, 191);">[1] Ứng dụng</button>
            <button @click="$emit('event','error')" style="position: absolute; width: 314px; height: 40px; left: 323px; top: 390px; font-size: 14px; background-color: rgb(191, 191, 191);">[2] Kiểm Tra Hệ Thống</button>
        
            <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 434px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
            <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 163px; top: 434px; background-color: #000; color:#fff; font-size: 14px;">[A] Tải Đĩa</button>
            <button @click="$emit('event','error')" style="left: 323px; top: 434px; width: 154px; height: 40px; background-color: #000; color:#fff; font-size: 14px;">[B] Tải Về</button>
            <button @click="$emit('event','success')" style="left: 483px; top: 434px; width: 154px; height: 40px; background-color: #000; color:#fff; font-size: 14px;">[C] Tắt Máy</button>
        </div>
    </div>`,
    data() {
        return {
            screenData: [{},{},{checkbox:true},{},{},{},{},{}],
            initialData: [],
            screenIndex: 0,
        }
    },
    mounted: function () {
        this.initialData = JSON.parse(JSON.stringify(this.screenData));
    },
    methods: {
        submit: function () {
            this.$emit('event', 'success');
        },
        reset: function () {
            // this.screenIndex = 0;
            this.screenData = JSON.parse(JSON.stringify(this.initialData));
        }
    }
}