export default {
    template: `<div class="screens">
        <div class="screen" :class="{ active: screenIndex === 0 }">
        <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">Chức Năng Khách Hàng</label>
        
        <div style="width: 640px; height: 87px; top: 35px; background-color: #fff; z-index: 1"></div>
        
        <label style="top: 38px; left: 3px; font-size: 12px;">Bán hàng</label>
        <input type="text" style="top: 37px; left: 65px; border: 1px solid #c6c5b5; border-radius: 1px; height: 20px; width: 64px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        <input type="text" style="top: 37px; left: 130px; border: 1px solid #c6c5b5; border-radius: 1px; height: 20px; width: 175px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        
        <label style="top: 38px; left: 320px; font-size: 12px;">Hủy bỏ</label>
        <input type="text" style="top: 37px; left: 368px; border: 1px solid #c6c5b5; border-radius: 1px; height: 20px; width: 64px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        <input type="text" style="top: 37px; left: 433px; border: 1px solid #c6c5b5; border-radius: 1px; height: 20px; width: 175px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        
        <label style="top: 58px; left: 25px; font-size: 12px;">Trả</label>
        <input type="text" style="top: 58px; left: 48px; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 64px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        <input type="text" style="top: 58px; left: 113px; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 175px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        
        <label style="top: 80px; left: 80px; font-size: 12px;">Tổng cộng</label>
        <input type="text" style="top: 79px; left: 149px; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 64px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        <input type="text" style="top: 79px; left: 214px; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 175px; font-size: 16px; padding: 0 5px;" value="0" readonly>
        
        <input type="text" style="top: 100px; left: 0; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 147px; font-size: 16px; padding: 0 5px;" value="" readonly>
        <input type="text" style="top: 100px; left: 148px; border: 1px solid #c6c5b5; border-radius: 2px; height: 20px; width: 330px; font-size: 16px; padding: 0 5px;" value="" readonly>
        <button disabled @click="$emit('event','error')" style="width: 150px; height: 42px; right: 0; top: 78px; background-color: #606168; font-size: 18px;">TỔNG CỘNG</button>
        
        <label style="left: 0; top: 136px; padding: 3px 10px; font-size: 12px; background-color: #abb7af; line-height: 14px;">[A] Trò Chơi</label>
        <div style="width: 640px; height: 229px; top: 156px; background-color: #84858e; z-index: 1"></div>
        
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 10px; top: 160px; background-color: #fc4d56; font-weight: bold;">
            <span>MEGA 6/45</span><img src="images/mega_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 220px; top: 160px; background-color: #c2c7d1; font-weight: bold;">
            <span>[1] Tự chọn</span><img src="images/mega_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 160px; background-color: #c2c7d1; font-weight: bold;">
            <span>[2] Bulk Pick</span><img src="images/mega_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 10px; top: 205px; background-color: #fd624e; font-weight: bold;">
            <span>POWER 6/55</span><img src="images/power_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 220px; top: 205px; background-color: #c2c7d1; font-weight: bold;">
            <span>[3] Tự chọn</span><img src="images/power_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 205px; background-color: #c2c7d1; font-weight: bold;">
            <span>[4] Bulk Pick</span><img src="images/power_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 10px; top: 250px; background-color: #c95bcc; font-weight: bold;">
            <span>MAX 4D</span><img src="images/4d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 220px; top: 250px; background-color: #c2c7d1; font-weight: bold;">
            <span>[5] Tự chọn</span><img src="images/4d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 250px; background-color: #c2c7d1; font-weight: bold;">
            <span>[6] Bulk Pick</span><img src="images/4d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 10px; top: 295px; background-color: #fb7ae0; font-weight: bold;">
            <span>MAX 3D</span><img src="images/3d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 220px; top: 295px; background-color: #c2c7d1; font-weight: bold;">
            <span>[7] Tự chọn</span><img src="images/3d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 295px; background-color: #c2c7d1; font-weight: bold;">
            <span>[8] Bulk Pick</span><img src="images/3d_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" class="keno-button" style="width: 200px; height: 40px; left: 10px; top: 340px; background-color: #00a6e3; font-weight: bold;">
            <span>KENO</span><img src="images/keno_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 220px; top: 340px; background-color: #c2c7d1; font-weight: bold;">
            <span>[9] Tự chọn</span><img src="images/keno_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 340px; background-color: #c2c7d1; font-weight: bold;">
            <span>Bulk Pick</span><img src="images/keno_ico.png" width="32px">
        </button>
        
        <button @click="screenIndex++" style="width: 122px; height: 40px; left: 3px; top: 390px; background-color: #d7d3c7; font-size: 20px; text-transform: uppercase;">Thoát
        </button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 131px; top: 390px; background-color: #0092ce; text-transform: uppercase; font-size: 14px;">Màn hình B/H
        </button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 259px; top: 390px; background-color: #f3eb91; text-transform: uppercase; font-size: 14px;">Thanh toán
        </button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 387px; top: 390px; background-color: #f46750; text-transform: uppercase; font-size: 14px;">Hủy bỏ
        </button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 515px; top: 390px; background-color: #ed425f; text-transform: uppercase; font-size: 14px;">Trợ giúp
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
        <label style="text-align: center; width: 640px; left: 0px; top: 0px; font-size: 22px;">Danh Mục Chính</label>
        <button @click="$emit('event','error')" style="width: 50px; height: 30px; left: 590px; top: 0px; text-transform: uppercase; font-size: 12px;">
                                    Thoát
                                </button>
        <button disabled="disabled" style="width: 314px; height: 72px; left: 3px; top: 35px; font-size: 14px;">
                                    [1] Đăng Nhập
                                </button>
        <button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 35px; font-size: 14px;">
                                    [2] Đăng Xuất
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 113px; font-size: 14px;">
                                    [3] Chức Năng Khách Hàng
                                </button><button @click="screenIndex++" style="width: 314px; height: 72px; left: 323px; top: 113px; font-size: 14px;">
                                    [4] Báo Cáo
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 191px; font-size: 14px;">
                                    [5] Giải Đặc Biệt/Kết Quả
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 323px; top: 191px; font-size: 14px;">
                                    [6] Xem Lại
                                </button><button @click="$emit('event','error')" style="width: 314px; height: 72px; left: 3px; top: 269px; font-size: 14px;">
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
        <div class="screen" :class="{ active: screenIndex === 2 }">
            <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 0px; font-size: 22px;">Báo Cáo</label> <label style="text-align: center; width: 640px; position: absolute; left: 0px; top: 35px;">Chọn báo cáo</label>
            
            <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
            <label class="custom-control custom-checkbox" style="position: absolute; left: 30px; top: 200px;">
            <input type="checkbox" class="custom-control-input" v-model="screenData[2].checkbox"> 
            <span class="custom-control-indicator"></span> 
            <span class="custom-control-description">[1] Báo Cáo Tổng Kết N/V Bán Hàng</span>
            </label>
                                <button @click="$emit('event','error')" style="position: absolute; width: 154px; height: 40px; left: 3px; top: 390px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button>
                                <button :disabled="!screenData[2].checkbox" @click="screenIndex++" class="submit-button" style="position: absolute; width: 154px; height: 40px; left: 163px; top: 390px; font-size: 14px; text-transform: uppercase;">
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
        <div class="screen" :class="{ active: screenIndex === 3 }">
       <label style="text-align: center; width: 640px; position: absolute; left: 0; top: 0; font-size: 22px;">Báo Cáo</label>
        <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
       <button @click="submit()" style="position: absolute; width: 60px; height: 35px; left: 450px; top: 45px; font-size: 16px; background-color: rgb(0, 25, 207);">In</button>
                                <label style="position: absolute; left: 130px; font-size: 16px; top:50px;">Báo Cáo</label>
                                <textarea style="position:absolute; left: 130px; top: 80px; text-transform: uppercase; width: 380px; height: 280px; font-family: 'Courier New'; font-size:12px; resize: none; overflow-y: scroll;" readonly="">Báo cáo tổng số của n/v
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
</textarea><button @click="$emit('event','error')" style="position: absolute; width: 154px; height: 40px; left: 3px; top: 390px; font-size: 20px; background-color: rgb(191, 191, 191); text-transform: uppercase;">
                                    Thoát
                                </button><button @click="$emit('event','error')" style="position: absolute; left: 483px; top: 390px; width: 154px; height: 40px; text-transform: uppercase; font-size: 14px; background-color: rgb(213, 49, 91);">
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
    </div>`,
    data() {
        return {
            screenData: [{},{},{checkbox:true},{}],
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