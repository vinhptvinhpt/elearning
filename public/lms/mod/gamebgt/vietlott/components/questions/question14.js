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
        <button @click="screenIndex++" class="keno-button" style="width: 200px; height: 40px; left: 10px; top: 340px; background-color: #00a6e3; font-weight: bold;">
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
        <label style="text-align: center; width: 640px; left: 0; top: 0; font-size: 22px;">KENO</label>
         
        <!-- Ticket result -->
        <div id="popup_ticket" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <span class="close" @click="closepopup">&times;</span>
            <img src="./images/Ticket_game/17.png" width="400" height="400" style="margin-left: auto; margin-right: auto;">
          </div>
        </div>
        <!-- End Ticket result -->


        <div style="width: 640px; height: 350px; top: 35px; left: 0; background-color: #007ab2;"></div> 
        
        <div style="background-color: #fff; width: 190px; height: 230px; top: 46px; left: 32px; border: 2px solid #782c37;">
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(1) }" @click="add(1)">1</a></div>
                <div class="col"><a :class="{ selected: isExists(2) }" @click="add(2)">2</a></div>
                <div class="col"><a :class="{ selected: isExists(3) }" @click="add(3)">3</a></div>
                <div class="col"><a :class="{ selected: isExists(4) }" @click="add(4)">4</a></div>
                <div class="col"><a :class="{ selected: isExists(5) }" @click="add(5)">5</a></div>
                <div class="col"><a :class="{ selected: isExists(6) }" @click="add(6)">6</a></div>
                <div class="col"><a :class="{ selected: isExists(7) }" @click="add(7)">7</a></div>
                <div class="col"><a :class="{ selected: isExists(8) }" @click="add(8)">8</a></div>
                <div class="col"><a :class="{ selected: isExists(9) }" @click="add(9)">9</a></div>
                <div class="col"><a :class="{ selected: isExists(10) }" @click="add(10)">10</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(11) }" @click="add(11)">11</a></div>
                <div class="col"><a :class="{ selected: isExists(12) }" @click="add(12)">12</a></div>
                <div class="col"><a :class="{ selected: isExists(13) }" @click="add(13)">13</a></div>
                <div class="col"><a :class="{ selected: isExists(14) }" @click="add(14)">14</a></div>
                <div class="col"><a :class="{ selected: isExists(15) }" @click="add(15)">15</a></div>
                <div class="col"><a :class="{ selected: isExists(16) }" @click="add(16)">16</a></div>
                <div class="col"><a :class="{ selected: isExists(17) }" @click="add(17)">17</a></div>
                <div class="col"><a :class="{ selected: isExists(18) }" @click="add(18)">18</a></div>
                <div class="col"><a :class="{ selected: isExists(19) }" @click="add(19)">19</a></div>
                <div class="col"><a :class="{ selected: isExists(20) }" @click="add(20)">20</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(21) }" @click="add(21)">21</a></div>
                <div class="col"><a :class="{ selected: isExists(22) }" @click="add(22)">22</a></div>
                <div class="col"><a :class="{ selected: isExists(23) }" @click="add(23)">23</a></div>
                <div class="col"><a :class="{ selected: isExists(24) }" @click="add(24)">24</a></div>
                <div class="col"><a :class="{ selected: isExists(25) }" @click="add(25)">25</a></div>
                <div class="col"><a :class="{ selected: isExists(26) }" @click="add(26)">26</a></div>
                <div class="col"><a :class="{ selected: isExists(27) }" @click="add(27)">27</a></div>
                <div class="col"><a :class="{ selected: isExists(28) }" @click="add(28)">28</a></div>
                <div class="col"><a :class="{ selected: isExists(29) }" @click="add(29)">29</a></div>
                <div class="col"><a :class="{ selected: isExists(30) }" @click="add(30)">30</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(31) }" @click="add(31)">31</a></div>
                <div class="col"><a :class="{ selected: isExists(32) }" @click="add(32)">32</a></div>
                <div class="col"><a :class="{ selected: isExists(33) }" @click="add(33)">33</a></div>
                <div class="col"><a :class="{ selected: isExists(34) }" @click="add(34)">34</a></div>
                <div class="col"><a :class="{ selected: isExists(35) }" @click="add(35)">35</a></div>
                <div class="col"><a :class="{ selected: isExists(36) }" @click="add(36)">36</a></div>
                <div class="col"><a :class="{ selected: isExists(37) }" @click="add(37)">37</a></div>
                <div class="col"><a :class="{ selected: isExists(38) }" @click="add(38)">38</a></div>
                <div class="col"><a :class="{ selected: isExists(39) }" @click="add(39)">39</a></div>
                <div class="col"><a :class="{ selected: isExists(40) }" @click="add(40)">40</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(41) }" @click="add(41)">41</a></div>
                <div class="col"><a :class="{ selected: isExists(42) }" @click="add(42)">42</a></div>
                <div class="col"><a :class="{ selected: isExists(43) }" @click="add(43)">43</a></div>
                <div class="col"><a :class="{ selected: isExists(44) }" @click="add(44)">44</a></div>
                <div class="col"><a :class="{ selected: isExists(45) }" @click="add(45)">45</a></div>
                <div class="col"><a :class="{ selected: isExists(46) }" @click="add(46)">46</a></div>
                <div class="col"><a :class="{ selected: isExists(47) }" @click="add(47)">47</a></div>
                <div class="col"><a :class="{ selected: isExists(48) }" @click="add(48)">48</a></div>
                <div class="col"><a :class="{ selected: isExists(49) }" @click="add(49)">49</a></div>
                <div class="col"><a :class="{ selected: isExists(50) }" @click="add(50)">50</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(51) }" @click="add(51)">51</a></div>
                <div class="col"><a :class="{ selected: isExists(52) }" @click="add(52)">52</a></div>
                <div class="col"><a :class="{ selected: isExists(53) }" @click="add(53)">53</a></div>
                <div class="col"><a :class="{ selected: isExists(54) }" @click="add(54)">54</a></div>
                <div class="col"><a :class="{ selected: isExists(55) }" @click="add(55)">55</a></div>
                <div class="col"><a :class="{ selected: isExists(56) }" @click="add(56)">56</a></div>
                <div class="col"><a :class="{ selected: isExists(57) }" @click="add(57)">57</a></div>
                <div class="col"><a :class="{ selected: isExists(58) }" @click="add(58)">58</a></div>
                <div class="col"><a :class="{ selected: isExists(59) }" @click="add(59)">59</a></div>
                <div class="col"><a :class="{ selected: isExists(60) }" @click="add(60)">60</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(61) }" @click="add(61)">61</a></div>
                <div class="col"><a :class="{ selected: isExists(62) }" @click="add(62)">62</a></div>
                <div class="col"><a :class="{ selected: isExists(63) }" @click="add(63)">63</a></div>
                <div class="col"><a :class="{ selected: isExists(64) }" @click="add(64)">64</a></div>
                <div class="col"><a :class="{ selected: isExists(65) }" @click="add(65)">65</a></div>
                <div class="col"><a :class="{ selected: isExists(66) }" @click="add(66)">66</a></div>
                <div class="col"><a :class="{ selected: isExists(67) }" @click="add(67)">67</a></div>
                <div class="col"><a :class="{ selected: isExists(68) }" @click="add(68)">68</a></div>
                <div class="col"><a :class="{ selected: isExists(69) }" @click="add(69)">69</a></div>
                <div class="col"><a :class="{ selected: isExists(70) }" @click="add(70)">70</a></div>
            </div>
            <div class="row no-gutters numbers keno-numbers">
                <div class="col"><a :class="{ selected: isExists(71) }" @click="add(71)">71</a></div>
                <div class="col"><a :class="{ selected: isExists(72) }" @click="add(72)">72</a></div>
                <div class="col"><a :class="{ selected: isExists(73) }" @click="add(73)">73</a></div>
                <div class="col"><a :class="{ selected: isExists(74) }" @click="add(74)">74</a></div>
                <div class="col"><a :class="{ selected: isExists(75) }" @click="add(75)">75</a></div>
                <div class="col"><a :class="{ selected: isExists(76) }" @click="add(76)">76</a></div>
                <div class="col"><a :class="{ selected: isExists(77) }" @click="add(77)">77</a></div>
                <div class="col"><a :class="{ selected: isExists(78) }" @click="add(78)">78</a></div>
                <div class="col"><a :class="{ selected: isExists(79) }" @click="add(79)">79</a></div>
                <div class="col"><a :class="{ selected: isExists(80) }" @click="add(80)">80</a></div>
            </div>
        </div>
        
        <label style="top: 82px; left: 330px;">KENO</label>
        <label style="top: 82px; left: 435px;">Số</label>
        <label style="top: 82px; left: 520px;">Số Tiền</label>
        
        <label style="top: 115px; left: 280px; font-size: 12px;">A</label>
        <textarea @click="screenData[1].focusModel = 'input1'" :value="value('input1')" :class="{focus: screenData[1].focusModel === 'input1'}" class="keno-input" style="top: 108px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input1.value)? screenData[1].input1.value.length: 1)" type="text" class="keno-input" style="top: 108px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input8'" v-model="screenData[1].input8.value" :class="{focus: screenData[1].focusModel === 'input8'}" type="text" class="keno-input" style="top: 108px; left: 478px; height: 32px; width: 150px;" readonly>
        
        <label style="top: 149px; left: 280px; font-size: 12px;">B</label>
        <textarea @click="screenData[1].focusModel = 'input2'" :value="value('input2')" :class="{focus: screenData[1].focusModel === 'input2'}" class="keno-input" style="top: 142px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input2.value)? screenData[1].input2.value.length: 1)" type="text" class="keno-input" style="top: 142px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input10'" v-model="screenData[1].input10.value" :class="{focus: screenData[1].focusModel === 'input10'}" type="text" class="keno-input" style="top: 142px; left: 478px; height: 32px; width: 150px;" readonly>
        
        <label style="top: 183px; left: 280px; font-size: 12px;">C</label>
        <textarea @click="screenData[1].focusModel = 'input3'" :value="value('input3')" :class="{focus: screenData[1].focusModel === 'input3'}" class="keno-input" style="top: 176px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input3.value)? screenData[1].input3.value.length: 1)" type="text" class="keno-input" style="top: 176px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input12'" v-model="screenData[1].input12.value" :class="{focus: screenData[1].focusModel === 'input12'}" type="text" class="keno-input" style="top: 176px; left: 478px; height: 32px; width: 150px;" readonly>
       
        <label style="top: 217px; left: 280px; font-size: 12px;">D</label>
        <textarea @click="screenData[1].focusModel = 'input4'" :value="value('input4')" :class="{focus: screenData[1].focusModel === 'input4'}" class="keno-input" style="top: 210px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input4.value)? screenData[1].input4.value.length: 1)" type="text" class="keno-input" style="top: 210px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input14'" v-model="screenData[1].input14.value" :class="{focus: screenData[1].focusModel === 'input14'}" type="text" class="keno-input" style="top: 210px; left: 478px; height: 32px; width: 150px;" readonly>
       
        <label style="top: 251px; left: 280px; font-size: 12px;">E</label>
        <textarea @click="screenData[1].focusModel = 'input5'" :value="value('input5')" :class="{focus: screenData[1].focusModel === 'input5'}" class="keno-input" style="top: 244px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input5.value)? screenData[1].input5.value.length: 1)" type="text" class="keno-input" style="top: 244px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input16'" v-model="screenData[1].input16.value" :class="{focus: screenData[1].focusModel === 'input16'}" type="text" class="keno-input" style="top: 244px; left: 478px; height: 32px; width: 150px;" readonly>
        
        <label style="top: 285px; left: 280px; font-size: 12px;">F</label>
        <textarea @click="screenData[1].focusModel = 'input6'" :value="value('input6')" :class="{focus: screenData[1].focusModel === 'input6'}" class="keno-input" style="top: 278px; left: 304px; height: 32px; width: 110px; padding-right: 30px;" readonly></textarea>
        <input :value="(Array.isArray(screenData[1].input6.value)? screenData[1].input6.value.length: 1)" type="text" class="keno-input" style="top: 278px; left: 416px; height: 32px; width: 60px;" readonly>
        <input @click="screenData[1].focusModel = 'input18'" v-model="screenData[1].input18.value" :class="{focus: screenData[1].focusModel === 'input18'}"  type="text" class="keno-input" style="top: 278px; left: 478px; height: 32px; width: 150px;" readonly>
         
        <div class="row no-gutter" style="top: 325px; width: 640px; margin: 0;">
            <div class="col">
                    <label class="custom-control custom-checkbox">
                   <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="1">
                   <span class="custom-control-indicator"></span>
                   <span class="custom-control-description">1 Kỳ</span>
                    </label>
            </div>
            <div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="2">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">2 Kỳ</span>
        </label>
</div>
<div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="3">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">3 Kỳ</span>
        </label>
</div>
<div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="5">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">5 Kỳ</span>
        </label>
</div>
<div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="10">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[A] 10 Kỳ</span>
        </label>
</div>
<div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="20">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[B] 20 Kỳ</span>
        </label>
</div>
<div class="col">
            <label class="custom-control custom-checkbox">
           <input class="custom-control-input" type="radio" v-model="screenData[1].radio.value" value="30">
           <span class="custom-control-indicator"></span>
           <span class="custom-control-description">[C] 30 Kỳ</span>
        </label>
</div>
        </div>
        
        <button @click="set(10000)" style="left: 1px; top: 354px; width: 75px; height: 30px;">10000</button>
        <button @click="set(20000)" style="left: 77px; top: 354px; width: 75px; height: 30px;">20000</button>
        <button @click="set(50000)" style="left: 153px; top: 354px; width: 75px; height: 30px;">50000</button> 
        <button @click="set(100000)" style="left: 229px; top: 354px; width: 75px; height: 30px;">100000</button>
        <button @click="set(200000)" style="left: 305px; top: 354px; width: 75px; height: 30px;">200000</button> 
        <button @click="set(500000)" style="left: 381px; top: 354px; width: 75px; height: 30px;">500000</button> 
        <button @click="set(1000000)" style="left: 457px; top: 354px; width: 75px; height: 30px;">1000000</button>
        <button @click="backspace()" style="left: 533px; top: 354px; width: 45px; height: 30px; font-size: 12px;"><--</button>
        <button @click="tc" style="left: 579px; top: 354px; width: 60px; height: 30px; font-size: 12px; background-color: rgb(232, 223, 72);">TC</button>  
          
        <button @click="$emit('event','error')" style="width: 154px; height: 40px; left: 3px; top: 390px; background-color: rgb(191, 191, 191); text-transform: uppercase; font-size: 20px;">Thoát</button>
        <button :disabled="!validate"  @click="submit" class="submit-button" style="width: 154px; height: 40px; left: 163px; top: 390px; text-transform: uppercase; font-size: 14px;">Gửi</button>
        <button @click="clear" style="left: 323px; top: 390px; width: 154px; height: 40px; background-color: #f56e43; text-transform: uppercase; font-size: 14px;">Xóa</button>
        <button @click="$emit('event','error')" style="left: 483px; top: 390px; width: 154px; height: 40px; background-color: rgb(213, 49, 91); text-transform: uppercase; font-size: 14px;">Trợ giúp</button>
        
        <label style="top: 40px; left: 360px; font-size: 14px;">Tổng Cộng</label>
        <input class="keno-input" type="text" style="top: 40px; left: 430px; height: 20px; width: 130px;" :value="$parent.$options.methods.formatPrice(summary)" readonly>
        
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
                    input1: {value: [], link: 'input8'},
                    input2: {value: [], link: 'input10'},
                    input3: {value: [], link: 'input12'},
                    input4: {value: [], link: 'input14'},
                    input5: {value: [], link: 'input16'},
                    input6: {value: [], link: 'input18'},
                    input7: {value: ""},
                    input8: {value: ""},
                    input9: {value: ""},
                    input10: {value: ""},
                    input11: {value: ""},
                    input12: {value: ""},
                    input13: {value: ""},
                    input14: {value: ""},
                    input15: {value: ""},
                    input16: {value: ""},
                    input17: {value: ""},
                    input18: {value: ""},
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
            if (!this.validate) return 0;
            var a = 0, b = ['input8', 'input10', 'input12', 'input14', 'input16', 'input18'];
            for (var i = 0; i < b.length; i++) {
                a += (parseInt(this.screenData[1][b[i]].value) || 0);
            }
            return a * (parseInt(this.screenData[1].radio.value) || 0);
        },
        validate: function () {
            var validated = false;
            var b = ['input1', 'input2', 'input3', 'input4', 'input5', 'input6'];
            for (var i = 0; i < b.length; i++) {
                var model = this.screenData[1][b[i]];
                if (model.value === 'TC' || model.value.length > 0) {
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
        isExists: function (value) {
            var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
            if (model !== undefined && Array.isArray(model.value)) {
                return model.value.includes(value);
            }
            return false;
        },
        tc: function () {
            if (['input1', 'input2', 'input3', 'input4', 'input5', 'input6'].includes(this.screenData[this.screenIndex].focusModel)) {
                var model = this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel];
                model.value = 'TC';
                if (model.next !== undefined) this.screenData[this.screenIndex].focusModel = model.next;
            }
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
                } else if (!a && model.value.length < 10) {
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
                for (let i = 0; i < 10; i++) {
                    if (this.screenData[this.screenIndex][model].value[i] !== undefined) {
                        a.push(('0' + this.screenData[this.screenIndex][model].value[i]).slice(-2))
                    } else a.push('__');
                }
            }
            return a.join(' ');
        },
        set: function (value) {
            if (['input8', 'input10', 'input12', 'input14', 'input16', 'input18'].includes(this.screenData[this.screenIndex].focusModel)) {
                let val = parseInt(this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value) || 0;
                this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value = val + (parseInt(value) || 0);
            }
        },
        append: function (text) {
            this.screenData[this.screenIndex][this.screenData[this.screenIndex].focusModel].value += text;
        },
        clear: function () {
            this.reset();
        },
        backspace: function () {

        },
        submit: function () {
            if (
                this.screenData[1].radio.value.toString() === "2"
                && this.screenData[1].input1.value.toString() === "18,79"
                && this.screenData[1].input2.value.toString() === ""
                && this.screenData[1].input3.value.toString() === ""
                && this.screenData[1].input4.value.toString() === ""
                && this.screenData[1].input5.value.toString() === ""
                && this.screenData[1].input6.value.toString() === ""
                && this.screenData[1].input7.value.toString() === ""
                && this.screenData[1].input8.value.toString() === "10000"
                && this.screenData[1].input9.value.toString() === ""
                && this.screenData[1].input10.value.toString() === ""
                && this.screenData[1].input11.value.toString() === ""
                && this.screenData[1].input12.value.toString() === ""
                && this.screenData[1].input13.value.toString() === ""
                && this.screenData[1].input14.value.toString() === ""
                && this.screenData[1].input15.value.toString() === ""
                && this.screenData[1].input16.value.toString() === ""
                && this.screenData[1].input17.value.toString() === ""
                && this.screenData[1].input18.value.toString() === ""
            ) {
                var popup = document.getElementById("popup_ticket");
                popup.style.display = "block";
                this.$emit('event', 'success');
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