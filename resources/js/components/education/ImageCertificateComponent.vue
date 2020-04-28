<template>
  <div class="row">
    <div class="cl-box-img">
      <div class="cl-image">
        <img :src="img_certificate_path" alt="" class="logo_img">
      </div>
      <div class="cl-download">
        <a :download="this.code+'_certificate.png'" :href="this.img_certificate_path" class="btn"><i class="fa fa-download"></i> Download</a>
      </div>
    </div>

<!--    badge-->
    <div class="cl-box-img">
      <div class="cl-image">
        <img :src="img_badge_path" alt="" class="logo_img">
      </div>
      <div class="cl-download">
        <a :download="this.code+'_badge.png'" :href="this.img_badge_path" class="btn"><i class="fa fa-download"></i> Download</a>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        props: ['code'],
        data() {
            return {
                img_certificate_path : '/storage/upload/certificate/'+this.code+'_certificate.png',
                img_badge_path: '/storage/upload/certificate/'+this.code+'_badge.png',
            }
        },
        methods: {
        },
        mounted() {
        }
    }
</script>

<style scoped>
 .cl-box-img{
   width: 44%;
   background-color: white;
   -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
   box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
   margin: 1% 0 1% 4%;
 }
  .cl-image{
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

  .cl-download{
    text-align: center;
    padding: 2%;
    border-top: 1px solid #acb0b1;
  }
</style>
