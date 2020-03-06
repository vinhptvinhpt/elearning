<template>
    <div>
        <div class="overwrap_search_box"></div>
        <div class="hk-wrapper hk-vertical-nav">
            <top-bar></top-bar>
            <side-bar></side-bar>
            <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
            <div class="hk-pg-wrapper" id="app">
                <router-view/>
            </div>
        </div>
    </div>
</template>

<script>
    import TopBar from './partials/LayoutTopBar.vue'
    import SideBar from './partials/LayoutSideBar.vue'


    export default {
        components: {TopBar, SideBar},
      methods:{
          openMenu(){
            $('#navbar_toggle_btn').click(function(){
              if($('.hk-wrapper').hasClass('hk-nav-toggle')){
                $('.hk-wrapper').removeClass('hk-nav-toggle');
              }else{
                $('.hk-wrapper').addClass('hk-nav-toggle');
              }
            });
          }
      },
        mounted() {
          this.openMenu();
            // this.$utils.setLayout('default')
        }
    }
</script>

<style scoped>

</style>
