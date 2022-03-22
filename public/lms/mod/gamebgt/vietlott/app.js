$(document).ready(function () {
    var elWidth = $('#main').outerWidth(),
        scale = elWidth / 640,
        xOffset = elWidth - 640,
        yOffset = 480 * scale - 480;
    $('#main .wrap').css({
        transform: "scale(" + scale + ")"
    });
});

var vm = new Vue({
    el: '#app',
    data: {
        navActive: false,
        alert: {show: false, type: '', message: ''},
        score: 0,
        state: 0,
        questionIndex: 0,
        questions: [
            {
                "name": "Câu 1",
                "description": "Tải biểu tượng in (logo), Kiểm tra chức năng in, Đăng nhập với ID và mật khẩu 111111.",
                "file": "question1.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 2",
                "description": "(Mega 6/45) Bán 1 vé kiểu tiêu chuẩn gồm 3 bảng TC cho 2 kỳ quay số liên tiếp.",
                "file": "question2.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 3",
                "description": "(Mega 6/45) Bán 1 vé kiểu bao 5 (15, 26, 30, 33, 45).",
                "file": "question3.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 4",
                "description": "(Power 6/55) Bán 1 vé kiểu bao 18 (2, 9, 11, 14, 17, 19, 22, 25, 29, 31, 35, 40, 43, 48, 49, 50, 52, 55).",
                "file": "question4.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 5",
                "description": "(Power 6/55) Bán 1 vé gồm 2 bộ số: 1 bộ TC và 1 bộ gồm các số: 05 17 35 42 50 55.",
                "file": "question5.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 6",
                "description": "(Max 4D) Bán 1 vé gồm 3 bộ số: 3579 cược 30.000 VND; 2222 cược 100.000 VND, 8888 cược 200.000VND.",
                "file": "question6.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 7",
                "description": "(Max 4D Tổ hợp) Bán một vé Max 4D Tổ hợp 7979 cược 500.000VND.",
                "file": "question7.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 8",
                "description": "(Max 4D Tổ hợp) Bán 5 vé Max 4D Tổ hợp TC cược 10.000VND/vé.",
                "file": "question8.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 9",
                "description": "(Max 4D Bao) Bán một vé Max 4D Bao số 5569 cược 20.000 VND/bộ số.",
                "file": "question9.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 10",
                "description": "(Max 4D Cuộn 1) Bán một vé Max 4D Cuộn 1 bộ số 289 cược 10.000VND.",
                "file": "question10.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 11",
                "description": "(Max 3D) Bán 1 vé Max 3D cơ bản với bộ số 299 cược 30.000VND.",
                "file": "question11.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 12",
                "description": "(Max 3D) Bán 1 vé Max 3D+ cơ bản TC cược 50.000VND.",
                "file": "question12.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 13",
                "description": "(Max 3D) Bán 1 vé Max 3D cơ bản tự chọn gồm 4 bảng số, cược 10.000VND/bảng.",
                "file": "question13.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 14",
                "description": "(Keno) Bán 1 vé Keno bậc 2 gồm 2 số - 18, 79, cược 10.000 VND, 2 kỳ.",
                "file": "question14.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 15",
                "description": "(Keno) Bán 1 vé Keno TC 5 bộ bậc 5 - cược 20.000 VND/bộ, 1 kỳ.",
                "file": "question15.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 16",
                "description": "Trả thưởng vé Power 6/55 bằng cách nhập TSN (1234 1234 8AC2 8AC2).",
                "file": "question16.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 17",
                "description": "In báo cáo nhân viên.",
                "file": "question17.js",
                "retry": 1,
                "state": 0,
            },
            {
                "name": "Câu 18",
                "description": "Đăng xuất, tắt thiết bị đầu cuối.",
                "file": "question18.js",
                "retry": 1,
                "state": 0,
            }
        ],
        screenData: {
            question: 0,
            index: 0,
            data: [],
        },
    },
    watch: {
        $data: {
            handler: function (val, oldVal) {
                if (USER_ID !== undefined && ITEM_ID !== undefined) {
                    const parsed = JSON.stringify(val);
                    localStorage.setItem('data_' + USER_ID + '_' + ITEM_ID, parsed);
                }
            },
            deep: true
        }
    },
    mounted() {
        if (USER_ID !== undefined && ITEM_ID !== undefined) {
            if (localStorage.getItem('data_' + USER_ID + '_' + ITEM_ID)) {
                const data = JSON.parse(localStorage.getItem('data_' + USER_ID + '_' + ITEM_ID));
                this.score = data.score;
                this.state = data.state;
                this.questionIndex = data.questionIndex;
                this.questions = data.questions;
            }
        }
    },
    computed: {
        componentFile() {
            var index = this.questionIndex;
            return () => import(`./components/questions/${this.questions[index].file}`);
        },
    },
    methods: {
        saveScreen: function () {
            this.screenData.question = this.questionIndex;
            this.screenData.data = this.$refs.component.screenData;
            this.screenData.index = this.$refs.component.screenIndex;
        },
        formatPrice(value) {
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        alertShow: function (params) {
            var self = this;
            self.alert.show = true;
            self.alert.type = params.type;
            self.alert.message = params.message;
            setTimeout(function () {
                self.alert.show = false;
            }, 2000);
        },
        loadQuestion: function (index) {
            if (this.questions[index].state === 0) {
                this.state = 0;
                this.questionIndex = index;
            }
        },
        handler(params) {
            if (params === 'error') {
                if (this.questions[this.questionIndex].retry > 0) {
                    this.$refs.component.reset();
                    this.questions[this.questionIndex].retry--;
                    this.alertShow({type: 'danger', message: 'Câu trả lời chưa chính xác. Vui lòng thử lại.'});
                } else
                    this.state = 1;
            } else if (params === 'success') {
                this.score++;
                this.questions[this.questionIndex].state = 2;
                this.state = 2;
            }
        },
        next() {
            this.questions[this.questionIndex].state = this.state;
            let index = this.questionIndex;
            while (index < this.questions.length - 1) {
                index++;
                if (this.questions[index].state === 0) {
                    return this.loadQuestion(index);
                }
            }
            axios.post('submit.php', {userid: USER_ID, itemid: ITEM_ID, finalgrade: this.score});
            this.state = 3;
        },
        reset() {
            this.state = 0;
            this.$refs.component.reset();
        }
    }
});