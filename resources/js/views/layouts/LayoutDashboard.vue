<template>
  <div>
    <div class="overwrap_search_box"></div>
    <div class="hk-wrapper hk-vertical-nav">
      <top-bar v-if="roles_ready" :key="topBarKey" :current_roles="current_roles" :slugs="slugs"></top-bar>
      <side-bar v-if="roles_ready" :current_roles="current_roles" :slugs="slugs"></side-bar>
      <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
      <div class="hk-pg-wrapper" id="app">
        <router-view
          :key="$route.fullPath"
          :current_roles="current_roles"
          :roles_ready="roles_ready"
          :slugs="slugs"
          :selected_role="selected_role"></router-view>
      </div>
    </div>
  </div>
</template>

<script>
  import TopBar from './partials/LayoutTopBar.vue'
  import SideBar from './partials/LayoutSideBar.vue'
  import Ls from "../../services/ls";

  export default {
    components: {TopBar, SideBar},
    data() {
      return {
        topBarKey: 0,
        slugs: [],
        current_roles: {
          has_user_market: false,
          has_master_agency: false,
          has_role_agency: false,
          has_role_pos: false,
          has_role_manager: false,
          has_role_leader: false,
          root_user: false,
          has_role_admin: false,
        },
        roles_ready: false,
        selected_role: 'user'
      }
    },
    methods: {
      openMenu() {
        $('#navbar_toggle_btn').click(function () {
          if ($('.hk-wrapper').hasClass('hk-nav-toggle')) {
            $('.hk-wrapper').removeClass('hk-nav-toggle');
          } else {
            $('.hk-wrapper').addClass('hk-nav-toggle');
          }
        });
      },
      checkRole() {
        axios.get('/api/check_role')
          .then(response => {
            this.current_roles = response.data.roles;
            this.slugs = response.data.slugs;
            //Pass to sidebar

            if (response.data.roles.root_user === true) {
              this.selected_role = 'root';
            } else if (response.data.roles.has_role_admin === true) {
              this.selected_role = 'admin';
            } else if (response.data.roles.has_role_manager === true) {
              this.selected_role = 'manager';
            } else if (response.data.roles.has_role_leader === true) {
              this.selected_role = 'leader';
            } else if (response.data.roles.has_user_market === true) {
              this.selected_role = 'user_market';
            } else {
              this.selected_role = 'user';
            }
            this.roles_ready = true;
          })
          .catch(error => {
            //console.log(error);
          });
      },
      renderTopBarAgain() { //Force render top bar
        this.topBarKey += 1;
      },
      logOut() {
        // Auth.logout().then(() => {
        //     // sslssso.logout();
        //     // window.location.href = '/';
        // });

        let token = Ls.get('auth.token');

        axios.post('/bgtlogout', {token: token})
          .then(response => {
            Ls.remove('auth.token');
            Ls.remove('auth.user');
            Ls.remove('auth.lang');
            Ls.remove('__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye');
            //sslssso.logout();
            window.location.href = '/';
          })
          .catch(error => {
            console.log(error.response.data);
          });
      }
    },
    mounted() {
      this.openMenu();
      // this.$utils.setLayout('default')
      this.checkRole();
    },
    updated() {
      var getAuthUser = localStorage.getItem('auth.user');
      var getAuthToken = localStorage.getItem('auth.token');
      //if null -> required login
      if (!getAuthUser || !getAuthToken) {
        this.logOut();
      }
    }
  }
</script>

<style scoped>

</style>
