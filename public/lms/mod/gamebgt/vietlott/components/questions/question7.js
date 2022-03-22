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
        <button @click="screenIndex++" style="width: 200px; height: 40px; left: 10px; top: 250px; background-color: #c95bcc; font-weight: bold;">
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
        <label style="text-align: center; width: 640px; left: 0; top: 0px; font-size: 22px;">MAX 4D</label>
         
        <!-- Ticket result -->
        <div id="popup_ticket" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <span class="close" @click="closepopup">&times;</span>
            <img src="./images/Ticket_game/06.png" width="400" height="400" style="margin-left: auto; margin-right: auto;">
          </div>
        </div>
        <!-- End Ticket result -->

        <div style="width: 640px; height: 350px; top: 35px; left: 0; background-color: #800b96;"></div> 
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 35px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="1">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">1 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 60px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="2">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">2 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 85px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="3">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">3 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 110px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="4">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">4 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 135px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="5">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">5 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 5px; top: 160px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="6">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">6 Kỳ Quay Số</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 35px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="A">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[A] 08/09/19</span>
        </label>   
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 60px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="B">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[B] 10/09/19</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 85px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="C">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[C] 13/09/19</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 110px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="D">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[D] 16/09/19</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 135px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="E">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[E] 20/09/19</span>
        </label>
        
        <label class="custom-control custom-checkbox" style="left: 105px; top: 160px;">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="F">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[F] 26/09/19</span>
        </label>            
         
        <label style="top: 185px; left: 3px; font-size: 14px;">Chơi</label>
        <input v-model="screenData[1].input3.value" class="four-d-input" type="text" style="top: 185px; left: 45px; height: 20px; width: 140px; background: none; border: 1px solid #000;" readonly>
        <button class="mode-btn" :class="{active:screenData[1].input3.value === '4D'}" :disabled="!modeChangeable" @click="screenData[1].input3.value = '4D'" style="width: 92px; height: 34px; top: 210px; left: 1px;">4D</button>
        <button class="mode-btn" :class="{active:screenData[1].input3.value === 'TỔ HỢP'}" :disabled="!modeChangeable" @click="screenData[1].input3.value = 'TỔ HỢP'" style="width: 92px; height: 34px; top: 210px; left: 95px; text-transform: uppercase">TỔ HỢP</button>
        <button class="mode-btn" :class="{active:screenData[1].input3.value === 'BAO'}" :disabled="!modeChangeable" @click="screenData[1].input3.value = 'BAO'" style="width: 92px; height: 34px; top: 245px; left: 1px; text-transform: uppercase">BAO</button>
        <button class="mode-btn" :class="{active:screenData[1].input3.value === 'CUỘN 1'}" :disabled="!modeChangeable" @click="screenData[1].input3.value = 'CUỘN 1'" style="width: 92px; height: 34px; top: 245px; left: 95px; text-transform: uppercase">CUỘN 1</button>
        <button class="mode-btn" :class="{active:screenData[1].input3.value === 'CUỘN 4'}" :disabled="!modeChangeable" @click="screenData[1].input3.value = 'CUỘN 4'" style="width: 92px; height: 34px; top: 280px; left: 1px;">CUỘN 4</button>
       
        <label style="top: 40px; left: 215px; font-size: 14px;">MAX 4D</label>
        <label style="top: 40px; left: 310px; font-size: 14px;">Số Tiền</label>
        <label style="top: 76px; left: 200px; font-size: 14px;">A</label>
        <input @click="screenData[1].focusModel = 'input1'" :value="((screenData[1].input1.value !== 'TC' && screenData[1].input1.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input1.value" :class="{focus: screenData[1].focusModel === 'input1'}" class="four-d-input" type="text" style="top: 65px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input2'" :value="screenData[1].input2.value" :class="{focus: screenData[1].focusModel === 'input2'}" class="four-d-input" type="text" style="top: 65px; left: 300px; height: 42px; width: 82px;" readonly> 
         
        <label style="top: 118px; left: 200px; font-size: 14px;">B</label>
        <input @click="screenData[1].focusModel = 'input4'" :value="((screenData[1].input4.value !== 'TC' && screenData[1].input4.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input4.value" :class="{focus: screenData[1].focusModel === 'input4'}" class="four-d-input" type="text" style="top: 107px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input5'" :value="screenData[1].input5.value" :class="{focus: screenData[1].focusModel === 'input5'}" class="four-d-input" type="text" style="top: 107px; left: 300px; height: 42px; width: 82px;" readonly>  
         
        <label style="top: 160px; left: 200px; font-size: 14px;">C</label>
        <input @click="screenData[1].focusModel = 'input6'" :value="((screenData[1].input6.value !== 'TC' && screenData[1].input6.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input6.value" :class="{focus: screenData[1].focusModel === 'input6'}" class="four-d-input" type="text" style="top: 149px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input7'" :value="screenData[1].input7.value" :class="{focus: screenData[1].focusModel === 'input7'}" class="four-d-input" type="text" style="top: 149px; left: 300px; height: 42px; width: 82px;" readonly>  
         
        <label style="top: 202px; left: 200px; font-size: 14px;">D</label>
        <input @click="screenData[1].focusModel = 'input8'" :value="((screenData[1].input8.value !== 'TC' && screenData[1].input8.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input8.value" :class="{focus: screenData[1].focusModel === 'input8'}" class="four-d-input" type="text" style="top: 191px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input9'" :value="screenData[1].input9.value" :class="{focus: screenData[1].focusModel === 'input9'}" class="four-d-input" type="text" style="top: 191px; left: 300px; height: 42px; width: 82px;" readonly>  
          
        <label style="top: 244px; left: 200px; font-size: 14px;">E</label>
        <input @click="screenData[1].focusModel = 'input10'" :value="((screenData[1].input10.value !== 'TC' && screenData[1].input10.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input10.value" :class="{focus: screenData[1].focusModel === 'input10'}" class="four-d-input" type="text" style="top: 233px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input11'" :value="screenData[1].input11.value" :class="{focus: screenData[1].focusModel === 'input11'}" class="four-d-input" type="text" style="top: 233px; left: 300px; height: 42px; width: 82px;" readonly>  
          
        <label style="top: 286px; left: 200px; font-size: 14px;">F</label>
        <input @click="screenData[1].focusModel = 'input12'" :value="((screenData[1].input12.value !== 'TC' && screenData[1].input12.value.length > 0 && screenData[1].input3.value == 'CUỘN 1')?'*':'') + screenData[1].input12.value" :class="{focus: screenData[1].focusModel === 'input12'}" class="four-d-input" type="text" style="top: 275px; left: 215px; height: 42px; width: 82px;" readonly>
        <input @click="screenData[1].focusModel = 'input13'" :value="screenData[1].input13.value" :class="{focus: screenData[1].focusModel === 'input13'}" class="four-d-input" type="text" style="top: 275px; left: 300px; height: 42px; width: 82px;" readonly>    
          
        <label style="top: 38px; left: 390px; font-size: 12px;">Tổng Cộng</label>
        <input class="four-d-input" type="text" style="top: 35px; left: 460px; height: 24px; width: 175px;" :value="$parent.$options.methods.formatPrice(summary)" readonly>
        
        <button @click="append('7')" style="font-size: 20px; width: 85px; height: 64px; top: 60px; left: 385px;">7</button>
        <button @click="append('8')" style="font-size: 20px; width: 85px; height: 64px; top: 60px; left: 470px;">8</button>
        <button @click="append('9')" style="font-size: 20px; width: 85px; height: 64px; top: 60px; left: 555px;">9</button>
        
        <button @click="append('4')" style="font-size: 20px; width: 85px; height: 64px; top: 123px; left: 385px;">4</button>
        <button @click="append('5')" style="font-size: 20px; width: 85px; height: 64px; top: 123px; left: 470px;">5</button>
        <button @click="append('6')" style="font-size: 20px; width: 85px; height: 64px; top: 123px; left: 555px;">6</button>
        
        <button @click="append('1')" style="font-size: 20px; width: 85px; height: 64px; top: 187px; left: 385px;">1</button>
        <button @click="append('2')" style="font-size: 20px; width: 85px; height: 64px; top: 187px; left: 470px;">2</button>
        <button @click="append('3')" style="font-size: 20px; width: 85px; height: 64px; top: 187px; left: 555px;">3</button>
        
        <button @click="backspace" style="font-size: 12px; width: 85px; height: 64px; top: 251px; left: 385px;"><--</button>
        <button @click="append('0')" style="font-size: 20px; width: 85px; height: 64px; top: 251px; left: 470px;">0</button>
        
        <button @click="set(10000)" style="left: 1px; top: 315px; width: 158px; height: 34px;">10000</button>
        <button @click="set(20000)" style="left: 161px; top: 315px; width: 158px; height: 34px;">20000</button>
        <button @click="set(50000)" style="left: 321px; top: 315px; width: 158px; height: 34px;">50000</button>
        <button @click="set(100000)" style="left: 481px; top: 315px; width: 158px; height: 34px;">100000</button>
        
        <button @click="set(200000)" style="left: 1px; top: 349px; width: 158px; height: 34px;">200000</button>
        <button @click="set(500000)" style="left: 161px; top: 349px; width: 158px; height: 34px;">500000</button>
        <button @click="set(1000000)" style="left: 321px; top: 349px; width: 158px; height: 34px;">1000000</button>
        <button @click="tc" style="left: 481px; top: 349px; width: 158px; height: 34px; background-color: #f5d65f; text-transform: uppercase;">Tự chọn</button>
         
        <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
        <button :disabled="!validate" @click="submit" class="submit-button" style="width: 154px; height: 40px; left: 163px; top: 390px; text-transform: uppercase; font-size: 14px;">Gửi</button>
        <button @click="clear" style="left: 323px; top: 390px; width: 154px; height: 40px; background-color: #f56e43; text-transform: uppercase; font-size: 14px;">Xóa</button>
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
            screenData: [
                {},
                {
                    focusModel: "input1",
                    radio: {value: 1},
                    input3: {value: "4D"},
                    input1: {value: "", limit: 4, link: "input2"},
                    input2: {value: ""},
                    input4: {value: "", limit: 4, link: "input5"},
                    input5: {value: ""},
                    input6: {value: "", limit: 4, link: "input7"},
                    input7: {value: ""},
                    input8: {value: "", limit: 4, link: "input9"},
                    input9: {value: ""},
                    input10: {value: "", limit: 4, link: "input11"},
                    input11: {value: ""},
                    input12: {value: "", limit: 4, link: "input13"},
                    input13: {value: ""},
                }
            ],
            initialData: [],
            screenIndex: 0,
        }
    },
    mounted: function () {
        this.initialData = JSON.parse(JSON.stringify(this.screenData));
    },
    watch: {
        mode: function (val) {
            var b = ['input1', 'input4', 'input6', 'input8', 'input10', 'input12'];
            for (var i = 0; i < b.length; i++) {
                if (val === 'CUỘN 1') this.screenData[1][b[i]].limit = 3;
                else this.screenData[1][b[i]].limit = 4;
            }
        }
    },
    computed: {
        mode: function () {
            return this.screenData[1].input3.value;
        },
        summary: function () {
            var a = 0, b = ['input2', 'input5', 'input7', 'input9', 'input11', 'input13'];
            for (var i = 0; i < b.length; i++) {
                a += (parseInt(this.screenData[1][b[i]].value) || 0);
            }
            return a * (parseInt(this.screenData[1].radio.value) || 0);
        },
        modeChangeable: function () {
            var a = true, b = ['input1', 'input4', 'input6', 'input8', 'input10', 'input12'];
            for (var i = 0; i < b.length; i++) {
                var model = this.screenData[1][b[i]];
                if (model.value.length > 0) {
                    a = false;
                    break;
                }
            }
            return a;
        },
        validate: function () {
            var validated = false;
            var b = ['input1', 'input4', 'input6', 'input8', 'input10', 'input12'];
            var c = 4;
            if (this.mode === 'CUỘN 1') c = 3;
            for (var i = 0; i < b.length; i++) {
                var model = this.screenData[1][b[i]];
                if (model.value === 'TC' || model.value.length === c) {
                    if ((parseInt(this.screenData[1][model.link].value) || 0) >= 10000) validated = true;
                    else validated = false;
                } else {
                    if (this.screenData[1][model.link].value.length !== 0) validated = false;
                    if (model.value.length !== 0) validated = false;
                    break;
                }
            }
            return validated;
        }
    },
    methods: {
        set: function (value) {
            if (['input2', 'input5', 'input7', 'input9', 'input11', 'input13'].includes(this.screenData[this.screenIndex].focusModel)) {
                let val = parseInt(this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value) || 0;
                this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = val + (parseInt(value) || 0);
            }
        },
        tc: function () {
            if (['input1', 'input4', 'input6', 'input8', 'input10', 'input12'].includes(this.screenData[this.screenIndex].focusModel)) {
                var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
                model.value = 'TC';
                if (model.next !== undefined) this.screenData[this.screenIndex].focusModel = model.next;
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
                this.screenData[1].input1.value.toString() === "7979"
                && this.screenData[1].input2.value.toString() === "500000"
                && this.screenData[1].input3.value.toString() === "TỔ HỢP"
                && this.screenData[1].input4.value.toString() === ""
                && this.screenData[1].input5.value.toString() === ""
                && this.screenData[1].input6.value.toString() === ""
                && this.screenData[1].input7.value.toString() === ""
                && this.screenData[1].input8.value.toString() === ""
                && this.screenData[1].input9.value.toString() === ""
                && this.screenData[1].input10.value.toString() === ""
                && this.screenData[1].input11.value.toString() === ""
                && this.screenData[1].input12.value.toString() === ""
                && this.screenData[1].input13.value.toString() === ""
            ){
                var popup = document.getElementById("popup_ticket");
                popup.style.display = "block";
                this.$emit('event', 'success');
            }
            else this.$emit('event', 'error');
        },
        reset: function () {
            this.screenData = JSON.parse(JSON.stringify(this.initialData));
        },
        closepopup: function () {
            var popup = document.getElementById("popup_ticket");
            popup.style.display = "none";
        }
    }
}