<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="selected_role === 'root' || selected_role === 'admin'" class="breadcrumb-item">
              <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}" >
                {{ trans.get('keys.to_chuc') }}
              </router-link>
            </li>
            <li v-if="selected_role === 'root'|| selected_role === 'admin'" class="breadcrumb-item"><router-link :to="{ name: 'EditOrganization', params: {id: team.organization_id}}">{{ team.organization ? team.organization.name : '' }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{name: 'IndexTeam', params: {page: source_page}, query: {organization_id: team.organization_id}}" >
                {{ trans.get('keys.danh_sach_team') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chi_tiet') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="card">
            <div class="card-body">
              <div class="edit_city_form form-material">
                <h5 class="mb-20">{{trans.get('keys.sua_team')}}: {{team.name}}</h5>

                <div class="row">


                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="team_organization_id"><strong>{{trans.get('keys.to_chuc')}}</strong></label>
                      <treeselect :disabled="true" v-model="team.organization_id" :multiple="false" :options="options" id="team_organization_id"/>
                      <label v-if="!team.organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>

                    </div>
                  </div>


                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="team_name"><strong>{{trans.get('keys.ten_team')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="team_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_team')+' *'" required v-model="team.name">
                      </div>
                      <label v-if="!team.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="team_code"><strong>{{trans.get('keys.ma_team')}} *</strong></label>
                      <div class="input-group">
                        <input type="text" id="team_code" class="form-control form-control-line" :placeholder="trans.get('keys.ma_team')+' *'" v-model="team.code">
                      </div>
                      <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                      <label v-if="!team.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="team_description" ><strong>{{trans.get('keys.mo_ta')}}</strong></label>
                      <textarea id="team_description" class="form-control form-control-line" :placeholder="trans.get('keys.mo_ta')" required v-model="team.description"></textarea>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group text-right">
                      <router-link :to="backButton()" class="btn btn-secondary btn-sm" style="color: rgb(255, 255, 255);">{{trans.get('keys.quay_lai')}}</router-link>
                      <button type="button" class="btn btn-primary btn-sm" @click="update()">{{trans.get('keys.cap_nhat')}}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  //import vPagination from 'vue-plain-pagination'
  export default {
    props: [
      'id',
      'source_page',
      'roles_ready',
      'selected_role',
    ],
    components: {
      //vPagination
    },
    data() {
      return {
        team: {
          name: '',
          code: '',
          organization_id: '',
          description: '',
        },
        options: []
      }
    },
    methods: {
      backButton() {
        return {
          name: 'IndexTeam',
          params: {page: this.source_page, back_page:'1'},
          query: {organization_id: this.team.organization_id}
        };
      },
      selectOrganization(current_id) {
        $('.content_search_box').addClass('loadding');
        axios.post('/organization/list',{
          keyword: this.organization_keyword,
          level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
          paginated: 0 //không phân trang
        })
          .then(response => {
            this.organization_list = response.data;
            //Set options recursive
            this.options = this.setOptions(response.data, current_id);
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
          })
      },
      setOptions(list, current_id) {
        let outPut = [];
        for (const [key, item] of Object.entries(list)) {
          let newOption = {
            id: item.id,
            label: item.code,
          };
          if (item.children.length > 0) {
            for (const [key, child] of Object.entries(item.children)) {
              if (child.id === current_id) {
                newOption.isDefaultExpanded = true;
                break;
              }
            }
            newOption.children = this.setOptions(item.children, current_id);
          }
          outPut.push(newOption);
        }
        return outPut;
      },
      getData() {
        axios.post('/organization-team/detail/'+this.id)
          .then(response => {
            this.team = response.data;
            this.selectOrganization(this.team.organization_id);
          })
          .catch(error => {
            console.log(error.response.data);
          })
      },
      update() {
        let current_pos = this;
        if(!this.team.name){
          $('.organization_required').show();
          return;
        }
        if(!this.team.organization_id){
          $('.organization_required').show();
          return;
        }
        if(!this.team.code){
          $('.position_required').show();
          return;
        }
        axios.post('/organization-team/update',{
          id: this.id,
          organization_id: this.team.organization_id,
          name: this.team.name,
          code: this.team.code,
          description: this.team.description
        })
          .then(response => {
            if(response.data.key) {
              toastr['error'](response.data.message, this.trans.get('keys.loi'));
              $('.form-control').removeClass('error');
              $('#team'+response.data.key).addClass('error');
            }
            else{
              if (response.data.status === 'success') {
                toastr[response.data.status](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                $('.form-control').removeClass('error');
                let query = { organization_id: current_pos.team.organization_id };
                this.$router.push({ name: 'IndexTeam', query: query});
              }
              else {
                toastr[response.data.status](response.data.message, current_pos.trans.get('keys.that_bai'));
              }
            }
          })
          .catch(error => {
            //console.log(error);
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          })
      },
    },
    computed: { //Phải gọi trên html nó mới trigger computed value
      filterPosition: function() {
        let default_response = [
          {
            key: 'manager',
            value: this.trans.get('keys.manager')
          },
          {
            key: 'leader',
            value: this.trans.get('keys.leader')
          },
          {
            key: 'employee',
            value: this.trans.get('keys.employee')
          }
        ];
        if (this.roles_ready) {
          //overwrite filter for manager / leader
          if (this.selected_role === 'root' || this.selected_role === 'admin') {
            return default_response;
          }
          let response = [];
          if (this.selected_role === 'manager') {
            response.push({
              key: 'leader',
              value: this.trans.get('keys.leader')
            });
          }
          if (this.selected_role === 'manager' || this.selected_role === 'leader') {
            response.push({
              key: 'employee',
              value: this.trans.get('keys.employee')
            });
          }
          return response;
        } else {
          return default_response;
        }
      },
      isOrgUpper: function() {
        if (this.roles_ready) {
          //overwrite filter for manager / leader
          return this.selected_role === 'manager' || this.selected_role === 'leader';
        } else {
          return false;
        }
      }
    },
    mounted() {
      this.getData();
    }
  }
</script>

<style scoped>

</style>
