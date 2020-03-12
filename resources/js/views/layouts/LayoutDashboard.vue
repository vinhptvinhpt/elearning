<template>
    <div>
        <div class="overwrap_search_box"></div>
        <div class="hk-wrapper hk-vertical-nav">
            <top-bar :key="topBarKey"></top-bar>
            <side-bar :current_roles="current_roles" v-if="current_roles !== null"></side-bar>
            <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
            <div class="hk-pg-wrapper" id="app">
              <router-view :key="$route.fullPath"></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import TopBar from './partials/LayoutTopBar.vue'
    import SideBar from './partials/LayoutSideBar.vue'

    export default {
      components: {TopBar, SideBar},
      data() {
        return {
          topBarKey: 0,
          current_roles: null,
          selected_role: 'userxxx'
        }
      },
      methods:{
          openMenu(){
            $('#navbar_toggle_btn').click(function(){
              if($('.hk-wrapper').hasClass('hk-nav-toggle')){
                $('.hk-wrapper').removeClass('hk-nav-toggle');
              }else{
                $('.hk-wrapper').addClass('hk-nav-toggle');
              }
            });
          },
          checkRoleSidebar() {
            axios.get('/api/checkrolesidebar')
              .then(response => {
                this.current_roles = response.data;
                // $checkRole->has_user_market = false;
                // $checkRole->has_master_agency = false;
                // $checkRole->has_role_agency = false;
                // $checkRole->has_role_pos = false;
                // $checkRole->root_user = false;
                // $checkRole->has_role_manager = false;
                // $checkRole->has_role_leader = false;
                if (response.data.root_user === true) {
                  this.selected_role = 'root';
                } else if (response.data.has_role_manager === true) {
                  this.selected_role = 'manager';
                } else if (response.data.has_role_leader === true) {
                  this.selected_role = 'leader';
                } else if (response.data.has_user_market === true) {
                  this.selected_role = 'user_market';
                } else {
                  this.selected_role = 'user';
                }
              })
              .catch(error => {
                console.log(error.response.data);
              });
          },
          renderTopBarAgain() { //Force render top bar
            this.topBarKey += 1;
          }
      },
      mounted() {
        this.openMenu();
          // this.$utils.setLayout('default')
        this.checkRoleSidebar();
      }
    }
</script>

<style scoped>

</style>
