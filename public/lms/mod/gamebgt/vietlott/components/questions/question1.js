export default {
    template: `<div class="screens">
        <div class="screen" :class="{ active: screenIndex === 0 }">
        <label style="text-align: center; width: 640px; left: 0px; top: 0px; font-size: 22px;">Danh Mục Chính</label>
        <button @click="$emit('event','error')" style="width: 50px; height: 30px; left: 590px; top: 0px; text-transform: uppercase; font-size: 12px;">
                                    Thoát
                                </button>
        <button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 35px; font-size: 14px;">
                                    [1] Đăng Nhập
                                </button>
        <button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 35px; font-size: 14px;">
                                    [2] Đăng Xuất
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 113px; font-size: 14px;">
                                    [3] Chức Năng Khách Hàng
                                </button><button disabled="disabled" @click="screenIndex++" style="width: 314px; height: 72px; left: 323px; top: 113px; font-size: 14px;">
                                    [4] Báo Cáo
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 191px; font-size: 14px;">
                                    [5] Giải Đặc Biệt/Kết Quả
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 191px; font-size: 14px;">
                                    [6] Xem Lại
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 269px; font-size: 14px;">
                                    [7] Thay Đổi Mật Khẩu
                                </button><button @click="screenIndex = 6" style="width: 314px; height: 72px; left: 323px; top: 269px; font-size: 14px;">
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
            <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Kiểm Tra</label>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 35px; width: 302px; height: 72px; font-size: 14px;">
                [1] Hiển thị
            </button>
            <button @click="$emit('message',{type:'success', message:'In thử biểu tượng logo thành công.'}); screenIndex = 2;" style="position: absolute; left: 329px; top: 35px; width: 302px; height: 72px;  font-size: 14px;">
                [2] Tải biểu tượng in
            </button>
            <button @click="$emit('message',{type:'success', message:'In thử thành công.'}); screenIndex = 1;" style="position: absolute; left: 9px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [3] Kiểm tra chức năng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [4] Kiểm tra chức năng đọc
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [5] Màn hình cảm ứng
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [6] Bàn phím
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [7] Dữ liệu thống kê
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [8] Kiểm tra kết nối
            </button>
            
            <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
            <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
            
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
            <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Kiểm Tra</label>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 35px; width: 302px; height: 72px; font-size: 14px;">
                [1] Hiển thị
            </button>
            <button @click="$emit('message',{type:'success', message:'In thử biểu tượng logo thành công.'}); screenIndex = 3;" style="position: absolute; left: 329px; top: 35px; width: 302px; height: 72px;  font-size: 14px;">
                [2] Tải biểu tượng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [3] Kiểm tra chức năng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [4] Kiểm tra chức năng đọc
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [5] Màn hình cảm ứng
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [6] Bàn phím
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [7] Dữ liệu thống kê
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [8] Kiểm tra kết nối
            </button>
            
            <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
            <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
            
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
            <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Kiểm Tra</label>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 35px; width: 302px; height: 72px; font-size: 14px;">
                [1] Hiển thị
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 35px; width: 302px; height: 72px;  font-size: 14px;">
                [2] Tải biểu tượng in
            </button>
            <button @click="$emit('message',{type:'success', message:'In thử thành công.'}); screenIndex = 3;" style="position: absolute; left: 9px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [3] Kiểm tra chức năng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [4] Kiểm tra chức năng đọc
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [5] Màn hình cảm ứng
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [6] Bàn phím
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [7] Dữ liệu thống kê
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [8] Kiểm tra kết nối
            </button>
            
            <button @click="$emit('event','error')"  style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
            <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
            
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
            <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Kiểm Tra</label>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 35px; width: 302px; height: 72px; font-size: 14px;">
                [1] Hiển thị
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 35px; width: 302px; height: 72px;  font-size: 14px;">
                [2] Tải biểu tượng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [3] Kiểm tra chức năng in
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 125px; width: 302px; height: 72px;  font-size: 14px;">
                [4] Kiểm tra chức năng đọc
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [5] Màn hình cảm ứng
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 215px; width: 302px; height: 72px;  font-size: 14px;">
                [6] Bàn phím
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 9px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [7] Dữ liệu thống kê
            </button>
            <button @click="$emit('event','error')" style="position: absolute; left: 329px; top: 305px; width: 302px; height: 72px;  font-size: 14px;">
                [8] Kiểm tra kết nối
            </button>
            
            <button @click="screenIndex++"  style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
            <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
            
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
             <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Danh Mục Chính</label>
        <button @click="$emit('event','error')" style="width: 50px; height: 30px; left: 590px; top: 0px; text-transform: uppercase; font-size: 12px;">
                                    Thoát
                                </button>
        <button @click="screenIndex++" style="width: 314px; height: 72px; left: 3px; top: 35px; font-size: 14px;">
                                    [1] Đăng Nhập
                                </button>
        <button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 35px; font-size: 14px;">
                                    [2] Đăng Xuất
                                </button><button disabled="disabled" @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 113px; font-size: 14px;">
                                    [3] Chức Năng Khách Hàng
                                </button><button disabled="disabled" @click="screenIndex++" style="width: 314px; height: 72px; left: 323px; top: 113px; font-size: 14px;">
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
        <div class="screen" :class="{ active: screenIndex === 5 }">
        <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Đăng Nhập</label>
        
        <div style="width: 640px; height: 350px; top: 35px; left: 0; background-color: #8f939a;"></div> 
        
        <label style="position: absolute; width: 200px; text-align: center; top: 35px; left: 0;">Chọn Người Dùng</label>
        
        <label class="custom-control custom-checkbox" style="left: 3px; top: 58px;">
           <input class="custom-control-input" type="radio" v-model="screenData[5].radio.value" value="1">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description" style="font-size: 14px;">[A] N/V Bán hàng</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 3px; top: 81px;">
           <input class="custom-control-input" type="radio" v-model="screenData[5].radio.value" value="2">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description" style="font-size: 14px;">[B] Q/L Điểm bán hàng</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 3px; top: 104px;">
           <input class="custom-control-input" type="radio" v-model="screenData[5].radio.value" value="3">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description" style="font-size: 14px;">[C] Đại Lý</span>
        </label>

        <label style="position: absolute; top: 60px; font-size: 11px; left: 270px;">Nhập ID của người dùng gồm 6 chữ
            số</label>

        <label style="font-size: 20px; position: absolute; left: 270px; top: 80px;">ID Người Dùng</label>
        <input @click="screenData[5].focusModel = 'input1'" v-model="screenData[5].input1.value" :class="{focus: screenData[5].focusModel === 'input1'}" type="text" class="input" style="height: 25px; width: 120px; left: 425px; top: 82px;" readonly>
        <label style="font-size: 20px; position: absolute; left: 300px; top: 118px;">Mật Khẩu</label>
        <input @click="screenData[5].focusModel = 'input2'" v-model="screenData[5].input2.value" :class="{focus: screenData[5].focusModel === 'input2'}" type="password" class="input" style="height: 25px; width: 110px; left: 435px; top: 121px;" readonly>
        
        <button @click="append('9')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 165px; left: 460px;">9
        </button>
        <button @click="append('8')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 165px; left: 365px;">8
        </button>
        <button @click="append('7')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 165px; left: 270px;">7
        </button>

        <button @click="append('6')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 200px; left: 460px;">6
        </button>
        <button @click="append('5')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 200px; left: 365px;">5
        </button>
        <button @click="append('4')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 200px; left: 270px;">4
        </button>

        <button @click="append('3')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 235px; left: 460px;">3
        </button>
        <button @click="append('2')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 235px; left: 365px;">2
        </button>
        <button @click="append('1')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 235px; left: 270px;">1
        </button>

        <button @click="clear" style="width: 95px; height: 35px; font-size: 12px; position: absolute; top: 270px; left: 460px; background-color: #f55f3b;">XOÁ
        </button>
        <button @click="append('0')" style="width: 95px; height: 35px; font-size: 20px; position: absolute; top: 270px; left: 365px;">0
        </button>
        <button @click="backspace" style="width: 95px; height: 35px; font-size: 12px; position: absolute; top: 270px; left: 270px;"><--
        </button>
        
         <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
         <button :disabled="screenData[5].radio.value.length < 1 || screenData[5].input1.value.length < 1 || screenData[5].input2.value.length < 1 " @click="submit" class="submit-button" style="width: 154px; height: 40px; left: 163px; top: 390px; text-transform: uppercase; font-size: 14px;">Gửi</button>
         <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
           
        <button @click="$emit('event','error')" style="left: 69px; top: 439px; width: 60px; height: 35px;"><img src="images/hotspot_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 132px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_up_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 195px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 258px; top: 439px; width: 60px; height: 35px;"><img src="images/printer_2_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 321px; top: 439px; width: 60px; height: 35px;"><img src="images/monitor_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 384px; top: 439px; width: 60px; height: 35px;"><img src="images/mail_folder_ico.png" width="32px"><span style="font-size: 4px;">20</span></button>
        <button @click="$emit('event','error')" style="left: 447px; top: 439px; width: 60px; height: 35px;"><img src="images/lock_ico.png" width="32px"></button>
        <button @click="$emit('event','error')" style="left: 510px; top: 439px; width: 60px; height: 35px;"><img src="images/arrow_folder_ico.png" width="32px"><span style="font-size: 4px;">320</span></button>
        
        </div>
    </div>`,
    data() {
        return {
            screenData: [{}, {}, {}, {}, {}, {
                focusModel: "input1",
                radio: {value: 1},
                input1: {value: "", limit: 6},
                input2: {value: "", limit: 6},
            },{}],
            initialData: [],
            screenIndex: 0,
        }
    },
    mounted: function () {
        this.initialData = JSON.parse(JSON.stringify(this.screenData));
    },
    methods: {
        set: function (value) {
            this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = value;
        },
        append: function (text) {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            if (model.limit !== undefined) {
                if (model.limit <= model.value.length) return;
            }
            model.value += text;
        },
        clear: function () {
            this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = "";
        },
        backspace: function () {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel],
                value = model.value;
            model.value = value.substr(0, value.length - 1);
        },
        submit: function () {
            console.log(this.screenData[5]);
            if (this.screenData[5].radio.value.toString() === "1"
                && this.screenData[5].input1.value.toString() === "111111"
                && this.screenData[5].input2.value.toString() === "111111"
            )
                this.$emit('event', 'success');
            else this.$emit('event', 'error');
        },
        reset: function () {
            // this.screenIndex = 0;
            this.screenData = JSON.parse(JSON.stringify(this.initialData));
        }
    }
}