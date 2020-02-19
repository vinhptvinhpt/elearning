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
        <button @click="screenIndex++" style="width: 200px; height: 40px; left: 220px; top: 340px; background-color: #c2c7d1; font-weight: bold;">
            <span>[9] Tự chọn</span><img src="images/keno_ico.png" width="32px">
        </button>
        <button @click="$emit('event','error')" style="width: 200px; height: 40px; left: 430px; top: 340px; background-color: #c2c7d1; font-weight: bold;">
            <span>Bulk Pick</span><img src="images/keno_ico.png" width="32px">
        </button>
        
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 3px; top: 390px; background-color: #d7d3c7; font-size: 20px; text-transform: uppercase;">Thoát
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
        <label style="text-align: center; width: 640px; left: 0; top: 0px; font-size: 22px;">KENO Tự Chọn</label>
         
        <!-- Ticket result -->
        <div id="popup_ticket" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <span class="close" @click="closepopup">&times;</span>
            <img src="./images/Ticket_game/18.png" width="400" height="400" style="margin-left: auto; margin-right: auto;">
          </div>
        </div>
        <!-- End Ticket result -->


        <div style="width: 640px; height: 350px; top: 35px; left: 0; background-color: #007ab2;"></div> 
         
        <label class="custom-control custom-checkbox" style="left: 8px; top: 48px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="1">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">1 Kỳ Quay Số</span>
        </label>  
        
        <label class="custom-control custom-checkbox" style="left: 8px; top: 93px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="2">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">2 Kỳ Quay Số</span>
        </label> 
        
        <label class="custom-control custom-checkbox" style="left: 8px; top: 133px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="3">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">3 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 8px; top: 173px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="5">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">5 Kỳ Quay Số</span>
        </label> 
        
        <label class="custom-control custom-checkbox" style="left: 8px; top: 213px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="10">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[A] 10 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 8px; top: 253px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio" value="20">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[B] 20 Kỳ Quay Số</span>
        </label>  
          
        <label class="custom-control custom-checkbox" style="left: 8px; top: 293px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio" value="30">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[C] 30 Kỳ Quay Số</span>
        </label>   
          
        <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
        <button :disabled="!validate" @click="submit" class="submit-button" style="width: 154px; height: 40px; left: 163px; top: 390px; text-transform: uppercase; font-size: 14px;">Gửi</button>
        <button @click="clear" style="left: 323px; top: 390px; width: 154px; height: 40px; background-color: #f56e43; text-transform: uppercase; font-size: 14px;">Xóa</button>
        <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
        
        <label style="top: 118px; left: 180px; font-size: 14px;">Số Bảng</label>
        <input @click="screenData[1].focusModel = 'input1'" v-model="screenData[1].input1.value" :class="{focus: screenData[1].focusModel === 'input1'}" class="keno-input" type="text" style="top: 118px; left: 270px; height: 24px; width: 60px;" readonly>
        <label style="top: 150px; left: 140px; font-size: 14px;">Bao Nhiêu Con Số</label>
        <input @click="screenData[1].focusModel = 'input2'" v-model="screenData[1].input2.value" :class="{focus: screenData[1].focusModel === 'input2'}" class="keno-input" type="text" style="top: 150px; left: 270px; height: 24px; width: 60px;" readonly>
        <label style="top: 182px; left: 135px; font-size: 14px;">Số Tiền</label>
        <input @click="screenData[1].focusModel = 'input3'" v-model="screenData[1].input3.value" :class="{focus: screenData[1].focusModel === 'input3'}" class="keno-input" type="text" style="top: 182px; left: 190px; height: 24px; width: 140px;" readonly>
        <label style="top: 230px; left: 140px; font-size: 14px;">Tổng Cộng</label>
        <input class="keno-input" type="text" style="top: 230px; left: 210px; height: 24px; width: 190px;" :value="$parent.$options.methods.formatPrice(summary)" readonly>
        
        <button @click="append('7')" style="font-size: 20px; width: 55px; height: 35px; top: 118px; left: 420px;">7</button>
        <button @click="append('8')" style="font-size: 20px; width: 55px; height: 35px; top: 118px; left: 475px;">8</button>
        <button @click="append('9')" style="font-size: 20px; width: 55px; height: 35px; top: 118px; left: 530px;">9</button>
        
        <button @click="append('4')" style="font-size: 20px; width: 55px; height: 35px; top: 153px; left: 420px;">4</button>
        <button @click="append('5')" style="font-size: 20px; width: 55px; height: 35px; top: 153px; left: 475px;">5</button>
        <button @click="append('6')" style="font-size: 20px; width: 55px; height: 35px; top: 153px; left: 530px;">6</button>
        
        <button @click="append('1')" style="font-size: 20px; width: 55px; height: 35px; top: 188px; left: 420px;">1</button>
        <button @click="append('2')" style="font-size: 20px; width: 55px; height: 35px; top: 188px; left: 475px;">2</button>
        <button @click="append('3')" style="font-size: 20px; width: 55px; height: 35px; top: 188px; left: 530px;">3</button>
        
        <button @click="backspace" style="font-size: 12px; width: 55px; height: 35px; top: 223px; left: 420px;"><--</button>
        <button @click="append('0')" style="font-size: 20px; width: 55px; height: 35px; top: 223px; left: 475px;">0</button>
        
        <button @click="set(10000)" style="left: 1px; top: 349px; width: 89px; height: 30px;">10000</button>
        <button @click="set(20000)" style="left: 92px; top: 349px; width: 89px; height: 30px;">20000</button>
        <button @click="set(50000)" style="left: 183px; top: 349px; width: 89px; height: 30px;">50000</button>
        <button @click="set(100000)" style="left: 274px; top: 349px; width: 89px; height: 30px;">100000</button>
        <button @click="set(200000)" style="left: 365px; top: 349px; width: 89px; height: 30px;">200000</button>
        <button @click="set(500000)" style="left: 456px; top: 349px; width: 89px; height: 30px;">500000</button>
        <button @click="set(1000000)" style="left: 547px; top: 349px; width: 89px; height: 30px;">1000000</button>
        
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
            screenData: [
                {},
                {
                    focusModel: "input1",
                    radio: {value: 1},
                    input3: {value: ""},
                    input1: {value: ""},
                    input2: {value: ""},
                }
            ],
            initialData: [],
            screenIndex: 0,
        }
    },
    mounted: function () {
        this.initialData = JSON.parse(JSON.stringify(this.screenData));
    },
    computed: {
        summary: function () {
            return (parseInt(this.screenData[1].radio.value) || 0) * (parseInt(this.screenData[1].input1.value) || 0) * (parseInt(this.screenData[1].input3.value) || 0);
        },
        validate: function () {
            var validated = false;
            if (this.screenData[1].input1.value.length > 0 && this.screenData[1].input2.value.length > 0 && (parseInt(this.screenData[1].input3.value) || 0) >= 10000) validated = true;
            return validated;
        }
    },
    methods: {
        set: function (value) {
            if (['input3'].includes(this.screenData[this.screenIndex].focusModel)) {
                let val = parseInt(this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value) || 0;
                this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = val + (parseInt(value) || 0);
            }
        },
        append: function (text) {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            if (model.limit !== undefined) {
                if (model.limit <= model.value.length) return;
            }
            model.value += text;
        },
        clear: function () {
            this.reset();
        },
        backspace: function () {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel],
                value = model.value.toString();
            model.value = value.substr(0, value.length - 1);
        },
        submit: function () {
            if (
                this.screenData[1].input1.value.toString() === "5"
                && this.screenData[1].input2.value.toString() === "5"
                && this.screenData[1].input3.value.toString() === "20000")
                {
                var popup = document.getElementById("popup_ticket");
                popup.style.display = "block";
                this.$emit('event', 'success');
            }else this.$emit('event', 'error');
        },
        reset: function () {
            // this.screenIndex = 0;
            this.screenData = JSON.parse(JSON.stringify(this.initialData));
        },
        closepopup: function () {
            var popup = document.getElementById("popup_ticket");
            popup.style.display = "none";
        }
    }
}