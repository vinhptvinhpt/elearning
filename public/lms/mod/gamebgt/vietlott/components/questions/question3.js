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
        
        <button @click="screenIndex++" style="width: 200px; height: 40px; left: 10px; top: 160px; background-color: #fc4d56; font-weight: bold;">
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
        
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 3px; top: 390px; background-color: #d7d3c7; font-size: 20px; text-transform: uppercase;">Thoát</button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 131px; top: 390px; background-color: #0092ce; text-transform: uppercase; font-size: 14px;">Màn hình B/H</button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 259px; top: 390px; background-color: #f3eb91; text-transform: uppercase; font-size: 14px;">Thanh toán</button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 387px; top: 390px; background-color: #f46750; text-transform: uppercase; font-size: 14px;">Hủy bỏ</button>
        <button @click="$emit('event','error')" style="width: 122px; height: 40px; left: 515px; top: 390px; background-color: #ed425f; text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
        
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
        <label style="text-align: center; width: 640px; left: 0; top: 0px; font-size: 22px;">MEGA 6/45</label>
         
        <!-- Ticket result -->
        <div id="popup_ticket" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <span class="close" @click="closepopup">&times;</span>
            <img src="./images/Ticket_game/02.png" width="400" height="400" style="margin-left: auto; margin-right: auto;">
          </div>
        </div>
        <!-- End Ticket result -->


        <div style="width: 640px; height: 350px; top: 35px; left: 0; background-color: #c3333c;"></div>
        
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
         
        <label style="top: 185px; left: 3px; font-size: 14px;">Bao</label>
        <input v-model="screenData[1].input3.value" class="mega-input" type="text" style="top: 185px; left: 45px; height: 21px; width: 140px; background: none; border: 1px solid #000;" readonly>
        <button :disabled="screenData[1].input3.value === 'BAO 5R'" @click="screenData[1].input3.value = 'BAO 5R'" style="width: 92px; height: 56px; top: 210px; left: 1px;">BAO 5R</button>
        <button :disabled="screenData[1].input3.value === 'BAO 7'" @click="screenData[1].input3.value = 'BAO 7'" style="width: 92px; height: 56px; top: 210px; left: 95px; text-transform: uppercase">BAO 7</button>
        <button :disabled="screenData[1].input3.value === 'BAO 8'" @click="screenData[1].input3.value = 'BAO 8'" style="width: 92px; height: 56px; top: 269px; left: 1px; text-transform: uppercase">BAO 8</button>
        <button :disabled="screenData[1].input3.value === 'BAO 9'" @click="screenData[1].input3.value = 'BAO 9'" style="width: 92px; height: 56px; top: 269px; left: 95px; text-transform: uppercase">BAO 9</button>
        <button @click="$emit('event','error')" style="width: 186px; height: 56px; top: 328px; left: 1px;">BAO KHÁC</button>

         <div style="background-color: #fff; width: 170px; height: 264px; top: 38px; left: 200px; border: 2px solid #782c37;">
            <div class="row no-gutters numbers" style="font-size: 12px;">
                <div class="col text-center"><a :class="{ selected: isExists(1) }" @click="add(1)">1</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(11) }" @click="add(11)">11</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(21) }" @click="add(21)">21</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(31) }" @click="add(31)">31</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(41) }" @click="add(41)">41</a></div>
            </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">   
                <div class="col text-center"><a :class="{ selected: isExists(2) }" @click="add(2)">2</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(12) }" @click="add(12)">12</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(22) }" @click="add(22)">22</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(32) }" @click="add(32)">32</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(42) }" @click="add(42)">42</a></div>
             </div>
            <div class="row no-gutters numbers" style="font-size: 12px;"> 
                <div class="col text-center"><a :class="{ selected: isExists(3) }" @click="add(3)">3</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(13) }" @click="add(13)">13</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(23) }" @click="add(23)">23</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(33) }" @click="add(33)">33</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(43) }" @click="add(43)">43</a></div>
             </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">    
                <div class="col text-center"><a :class="{ selected: isExists(4) }" @click="add(4)">4</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(14) }" @click="add(14)">14</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(24) }" @click="add(24)">24</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(34) }" @click="add(34)">34</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(44) }" @click="add(44)">44</a></div>
               </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">        
                <div class="col text-center"><a :class="{ selected: isExists(5) }" @click="add(5)">5</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(15) }" @click="add(15)">15</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(25) }" @click="add(25)">25</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(35) }" @click="add(35)">35</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(45) }" @click="add(45)">45</a></div>
                 </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">   
                <div class="col text-center"><a :class="{ selected: isExists(6) }" @click="add(6)">6</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(16) }" @click="add(16)">16</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(26) }" @click="add(26)">26</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(36) }" @click="add(36)">36</a></div>
                <div class="col text-center empty"></div>
                </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">    
                <div class="col text-center"><a :class="{ selected: isExists(7) }" @click="add(7)">7</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(17) }" @click="add(17)">17</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(27) }" @click="add(27)">27</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(37) }" @click="add(37)">37</a></div>
                <div class="col text-center empty"></div>
            </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">     
                <div class="col text-center"><a :class="{ selected: isExists(8) }" @click="add(8)">8</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(18) }" @click="add(18)">18</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(28) }" @click="add(28)">28</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(38) }" @click="add(38)">38</a></div>
                <div class="col text-center empty"></div>
            </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">    
                <div class="col text-center"><a :class="{ selected: isExists(9) }" @click="add(9)">9</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(19) }" @click="add(19)">19</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(29) }" @click="add(29)">29</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(39) }" @click="add(39)">39</a></div>
                <div class="col text-center empty"></div>
            </div>
            <div class="row no-gutters numbers" style="font-size: 12px;">    
                <div class="col text-center"><a :class="{ selected: isExists(10) }" @click="add(10)">10</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(20) }" @click="add(20)">20</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(30) }" @click="add(30)">30</a></div>
                <div class="col text-center"><a :class="{ selected: isExists(40) }" @click="add(40)">40</a></div>
                <div class="col text-center empty"></div>
            </div>
        </div>

        <label style="top: 90px; left: 450px; font-size: 14px;">A</label>
        <textarea @click="screenData[1].focusModel = 'input1'" :value="value('input1')" :class="{focus: screenData[1].focusModel === 'input1'}" class="mega-input" type="text" style="top: 76px; left: 474px; height: 48px; width: 164px; padding-right: 70px;"readonly></textarea>
        
        <label style="top: 141px; left: 450px; font-size: 14px;">B</label>
        <textarea @click="screenData[1].focusModel = 'input2'" :value="value('input2')" :class="{focus: screenData[1].focusModel === 'input2'}" class="mega-input" type="text" style="top: 127px; left: 474px; height: 48px; width: 164px; padding-right: 70px;" readonly></textarea>
         
        <label style="top: 192px; left: 450px; font-size: 14px;">C</label>
        <textarea @click="screenData[1].focusModel = 'input4'" :value="value('input4')" :class="{focus: screenData[1].focusModel === 'input4'}" class="mega-input" type="text" style="top: 178px; left: 474px; height: 48px; width: 164px; padding-right: 70px;" readonly></textarea>
         
        <label style="top: 243px; left: 450px; font-size: 14px;">D</label>
        <textarea @click="screenData[1].focusModel = 'input5'" :value="value('input5')" :class="{focus: screenData[1].focusModel === 'input5'}" class="mega-input" type="text" style="top: 229px; left: 474px; height: 48px; width: 164px; padding-right: 70px;" readonly></textarea>
         
        <label style="top: 294px; left: 450px; font-size: 14px;">E</label>
        <textarea @click="screenData[1].focusModel = 'input6'" :value="value('input6')" :class="{focus: screenData[1].focusModel === 'input6'}" class="mega-input" type="text" style="top: 280px; left: 474px; height: 48px; width: 164px; padding-right: 70px;" readonly></textarea>
         
        <label style="top: 345px; left: 450px; font-size: 14px;">F</label>
        <textarea @click="screenData[1].focusModel = 'input7'" :value="value('input7')" :class="{focus: screenData[1].focusModel === 'input7'}" class="mega-input" type="text" style="top: 331px; left: 474px; height: 48px; width: 164px; padding-right: 70px;" readonly></textarea>
          
        <label style="top: 48px; left: 430px; font-size: 12px;">Tổng Cộng</label>
        <input class="mega-input" type="text" style="top: 35px; left: 500px; height: 40px; width: 130px;" :value="$parent.$options.methods.formatPrice(summary)" readonly>
        
        <button @click="tc()" style="background-color: #e8df48; height: 34px; width: 116px; left: 240px; top: 345px; text-transform: uppercase">Tự chọn</button>
        
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
        <div class="screen" :class="{ active: screenIndex === 2 }">
          <label style="text-align: center; width: 640px; position: absolute; left: 0; top: 0; font-size: 22px;">MEGA 6/45 Bao</label> 
            <div style="width: 640px; height: 350px;  top: 35px; background-color: #84858e;"></div>
            <label style="text-align: center; position: absolute; left: 100px; top: 35px;">Xác nhận Bao dự thưởng?</label>
            <label style="text-align: center;position: absolute; left: 150px; top: 70px;">{{screenData[1].input3.value}}</label>
            <button @click="$emit('event','success')" style="position: absolute; width: 314px; height: 40px; left: 3px; top: 390px; font-size: 14px; background-color: rgb(191, 191, 191);">[1] Có</button>
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
    </div>`,
    data() {
        return {
            screenData: [
                {},
                {
                    focusModel: "input1",
                    radio: {value: 1},
                    input3: {value: ""},
                    input1: {value: [], next: 'input2'},
                    input2: {value: [], next: 'input4'},
                    input4: {value: [], next: 'input5'},
                    input5: {value: [], next: 'input6'},
                    input6: {value: [], next: 'input7'},
                    input7: {value: []}
                },
                {}
            ],
            initialData: [],
            screenIndex: 0,
        }
    },
    watch: {
        mode: function (val) {
            var b = ['input1', 'input2', 'input4', 'input5', 'input6', 'input7'];
            for (var i = 0; i < b.length; i++) {
                if (Array.isArray(this.screenData[1][b[i]].value) && this.screenData[1][b[i]].value.length > this.limit) {
                    var c = this.screenData[1][b[i]].value.length - this.limit;
                    this.screenData[1][b[i]].value.splice(this.limit, c);
                }
            }
        }
    },
    computed: {
        mode: function () {
            return this.screenData[1].input3.value;
        },
        summary: function () {
            var a = 10000;
            switch (this.screenData[1].input3.value) {
                case 'BAO 5R':
                    a = 400000;
                    break;
                case 'BAO 7':
                    a = 70000;
                    break;
                case 'BAO 8':
                    a = 280000;
                    break;
                case 'BAO 9':
                    a = 840000;
                    break;
                case 'BAO 18':
                    a = 185640000;
                    break;
            }
            var b = ['input1', 'input2', 'input4', 'input5', 'input6', 'input7'], c = 0;
            for (var i = 0; i < b.length; i++) {
                if (this.screenData[1][b[i]].value === 'TC' || this.screenData[1][b[i]].value.length === this.limit) c++;
            }
            return a * (parseInt(this.screenData[1].radio.value) || 0) * c;
        },
        limit: function () {
            var result = 6;
            switch (this.screenData[1].input3.value) {
                case 'BAO 5R':
                    result = 5;
                    break;
                case 'BAO 7':
                    result = 7;
                    break;
                case 'BAO 8':
                    result = 8;
                    break;
                case 'BAO 9':
                    result = 9;
                    break;
                case 'BAO 18':
                    result = 18;
                    break;
            }
            return result;
        },
        validate: function () {
            var validated = false;
            var b = ['input1', 'input2', 'input4', 'input5', 'input6', 'input7'];
            for (var i = 0; i < b.length; i++) {
                if (this.screenData[1][b[i]].value === 'TC' || this.screenData[1][b[i]].value.length === this.limit) {
                    validated = true;
                } else {
                    if (this.screenData[1][b[i]].value.length !== 0) validated = false;
                    break;
                }
            }
            return validated;
        }
    },
    mounted: function () {
        this.initialData = JSON.parse(JSON.stringify(this.screenData));
    },
    methods: {
        isExists: function (value) {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            if (model !== undefined && Array.isArray(model.value)) {
                return model.value.includes(value);
            }
            return false;
        },
        tc: function () {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            model.value = 'TC';
            if (model.next !== undefined) this.screenData[this.screenIndex].focusModel = model.next;
        },
        add: function (value) {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            if (Array.isArray(model.value)) {
                var a = model.value.includes(value);
                if (a) {
                    var index = model.value.indexOf(value);
                    if (index > -1) {
                        model.value.splice(index, 1);
                    }
                } else if (!a && model.value.length < this.limit) {
                    model.value.push(value);
                    model.value.sort((a, b) => a - b);
                    if (model.value.length === this.limit && model.next !== undefined) this.screenData[this.screenIndex].focusModel = model.next;
                }
            }
        },
        value: function (model) {
            let a = [];
            if (this.screenData[this.screenIndex][model] !== undefined) {
                if (!Array.isArray(this.screenData[this.screenIndex][model].value)) return this.screenData[this.screenIndex][model].value;
                for (let i = 0; i < this.limit; i++) {
                    if (this.screenData[this.screenIndex][model].value[i] !== undefined) {
                        a.push(('0' + this.screenData[this.screenIndex][model].value[i]).slice(-2))
                    } else a.push('__');
                }
            }
            return a.join(' ');
        },
        set: function (value) {
            this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = value;
        },
        append: function (text) {
            this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value += text;
        },
        clear: function () {
            this.reset();
        },
        backspace: function () {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel],
                value = model.value;
            model.value = value.substr(0, value.length - 1);
        },
        submit: function () {
            if (this.screenData[1].radio.value.toString() === "1"
                && this.screenData[1].input1.value.toString() === "15,26,30,33,45"
                && this.screenData[1].input2.value.toString() === ""
                && this.screenData[1].input3.value.toString() === "BAO 5R"
                && this.screenData[1].input4.value.toString() === ""
                && this.screenData[1].input5.value.toString() === ""
                && this.screenData[1].input6.value.toString() === ""
                && this.screenData[1].input7.value.toString() === ""
            ) {
                var popup = document.getElementById("popup_ticket");
                popup.style.display = "block";
                this.screenIndex++;
                // this.$emit('event', 'success');
            } else this.$emit('event', 'error');
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