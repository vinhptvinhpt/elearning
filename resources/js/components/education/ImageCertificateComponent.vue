<template>
    <div class="row">
        <div v-if="exist_cer === 1" class="cl-box-img box-certificate">
            <div class="cl-image">
                <img :src="img_certificate_path" alt="" class="logo_img">
            </div>
            <div class="cl-download">
                <a :download="this.code+'_certificate.jpeg'" :href="this.img_certificate_path" class="btn"><i
                        class="fa fa-download"></i> Download</a>
            </div>
        </div>

        <!--    badge-->
        <div v-if="exist_badge === 1" class="cl-box-img">
            <div class="cl-image">
                <img :src="img_badge_path" alt="" class="logo_img">
            </div>
            <div class="cl-download">
                <a :download="this.code+'_badge.jpeg'" :href="this.img_badge_path" class="btn"><i
                        class="fa fa-download"></i> Download</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['code', 'badge'],
        data() {
            return {
                img_certificate_path: '/storage/upload/certificate/' + this.code + '_certificate.jpeg',
                img_badge_path: '/storage/upload/certificate/' + this.code + '_badge.jpeg',
                exist_cer: 0,
                exist_badge: 0,
            }
        },
        methods: {
            checkExistImage() {
                axios.post('/api/certificate/checkexist', {
                    code: this.code
                })
                    .then(response => {
                        if (response.data.status) {
                            this.exist_cer = response.data.existCertificate;
                            this.exist_badge = response.data.existBadge;
                        }
                    })
                    .catch(error => {

                    });
            }
        },
        mounted() {
            if (this.badge != 1) {
                $(".box-certificate").css("margin", "1% 25%");
            }
            this.checkExistImage();
        }
    }
</script>

<style scoped>
    .cl-box-img {
        width: 44%;
        background-color: white;
        -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        margin: 1% 0 1% 4%;
    }

    .cl-image {
        width: 80%;
        margin: 0 auto;
        min-height: 570px;
    }

    .btn {
        background-color: DodgerBlue;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 20px;
    }

    .cl-download {
        text-align: center;
        padding: 2%;
        border-top: 1px solid #acb0b1;
    }
</style>
