
<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.bao_cao') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <!--Filter block-->
      <div class="card">
        <div class="card-body">


          <!--New filter-->
          <div class="row">
            <div class="col-6">
              <treeselect v-model="organization_id" :multiple="false" :options="organization_options" id="organization_id"/>
            </div>
            <div class="col-6">
              <select id="training_select" v-model="training_id" class="custom-select form-control">
                <option v-for="training_option in training_options" :value="training_option.id">
                  {{training_option.name}}
                </option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <select v-model="mode_select" class="custom-select form-control">
                <option value="completed">
                  {{ trans.get('keys.hoan_thanh_dao_tao')}}
                </option>
                <option value="certificated">
                  {{ trans.get('keys.da_cap_chung_chi')}}
                </option>
              </select>
            </div>
            <div class="col-6">
              <button id="buttonReport" class="btn btn-primary btn-sm hasLoading" @click="listData()">{{trans.get('keys.xem')}}<i class="fa fa-spinner"></i></button>
            </div>
          </div>

        </div>
      </div>
      <!--End Filter block-->

      <!--Content Report-->
      <div class="card" id="requestReport">
        <div class="card-body">
          <h5 class="text-center mb-20">{{trans.get('keys.thong_ke_nhan_vien')}}</h5>
          <div class="text-right mb-20">
            <a style="color: #fff" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" v-on:click="expandAll()" :title="trans.get('keys.xem_chi_tiet')" v-if="report_data.selected_level !== 'city'">
              <span class="btn-icon-wrap"><i class="fal fa-eye"></i></span>
            </a>
            <a style="color: #fff" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" v-on:click="exportExcel(report_data)" :title="trans.get('keys.xuat_excel')">
              <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i></span>
            </a>
          </div>

          <div class="table-responsive">
            <table>
              <thead>
              <tr>
                <th>{{trans.get('keys.ten')}}</th>
                <th v-if="mode_select === 'completed'">{{trans.get('keys.da_hoan_thanh_dao_tao')}}</th>
                <th v-else>{{trans.get('keys.da_co_giay_chung_nhan')}}</th>

                <th v-if="mode_select === 'completed'">{{trans.get('keys.chua_hoan_thanh_dao_tao')}}</th>
                <th v-else>{{trans.get('keys.chua_co_giay_chung_nhan')}}</th>

                <th>{{trans.get('keys.tong_so')}}</th>
              </tr>
              </thead>
              <tbody>

                  <tr :style="'background:'+ item.color" v-for="(item, index) in report_data">
                    <td v-if="item.type === 'organization' || item.type === 'training'"><strong>{{item.column1}}</strong></td>
                    <td v-else>{{item.column1}}</td>
                    <td v-html="item.column2" style="vertical-align: top;"></td>
                    <td v-html="item.column3" style="vertical-align: top;"></td>
                    <td v-html="item.column4" style="vertical-align: top;"></td>
                  </tr>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--End Content Report-->
    </div>
  </div>

</template>

<script>
    export default {
    	data() {
            return {
                report_data: [],
                organization_id: 0,
                organization_options: [
                  {
                    id: 0,
                    label: this.trans.get('keys.chon_to_chuc')
                  }
                ],
                training_id: 0,
                training_options: [
                  {
                    id: 0,
                    label: this.trans.get('keys.khung_nang_luc')
                  }
                ],
                mode_select: 'completed'
            }
        },
        methods: {
            preloadData() {
                this.fetchOrganization();
                this.fetchTraining();
                this.listData();
            },
            listData() {
              axios.post('/report/list_detail', {
                organization_id: this.organization_id,
                training_id: this.training_id,
                mode_select: this.mode_select
              })
                .then(response => {
                  let list = response.data;
                  this.report_data = [];
                  this.setData(list, 'organization');
                  //Reset report_data array
                })
                .catch(error => {
                  console.log(error);
                });
            },
            toggleRow(id, row_count) {
                if (row_count > 0) {
                    $( '#' + id ).toggle( "slow", function() {
                        // Animation complete.
                    });
                }
            },
            expandAll() {
                $(document).ready(function(){
                    $("[has-content=true]").toggle( "slow", function() {
                        // Animation complete.
                    });
                });
            },
            exportExcel(data) {
                axios.post('/exportReport', {
                    data: data,
                })
                    .then(response => {
                        var a = $("<a>")
                            .prop("href", "/downloadExportReport")
                            //.prop("download", "newfile.txt")
                            .appendTo("body");
                        a[0].click();
                        a.remove();
                        //roam_message('success','Lỗi hệ thống. Thao tác thất bại');
                    })
                    .catch(error => {
                        console.log(error);
                        roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            myFilterBy: (option, label, search) => {
                if (!label) {
                    label = '';
                }
                let new_search = convertUtf8(search);
                let new_label = convertUtf8(label);
                //return this.filterBy(option, new_label, new_search); //can not call components function here
                return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
            },
            fetchOrganization() {
              $('.content_search_box').addClass('loadding');
              axios.post('/organization/list', {
                keyword: this.parent_keyword,
                level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                paginated: 0 //không phân trang,
              })
                .then(response => {
                  //Set options recursive
                  this.organization_options = this.setOptions(response.data);
                  $('.content_search_box').removeClass('loadding');
                })
                .catch(error => {
                  $('.content_search_box').removeClass('loadding');
                })
            },
            fetchTraining() {
              axios.post('/api/trainning/list', {
                paginated: 0
              })
                .then(response => {
                  this.training_options = response.data;
                })
                .catch(error => {
                  console.log(error.response.data);
                });
            },
            setOptions(list) {
              let outPut = [];
              for (const [key, item] of Object.entries(list)) {
                let newOption = {
                  id: item.id,
                  label: item.name
                };
                if (item.children.length > 0) {
                  newOption.children = this.setOptions(item.children);
                }
                outPut.push(newOption);
              }
              return outPut;
            },
            setData(list, type) {

              for (const [key, item] of Object.entries(list)) {
                let pushObject = {
                  type: type,
                  column1: '',
                  column2: [],
                  column3: [],
                  column4: [],
                  color: "#fff"
                };

                pushObject.column1 = item.name;


                console.log(item);


                if (type === 'organization' || type === 'training' || type === 'courses') {
                  if (this.mode_select === 'completed') {
                    pushObject.column2 = Object.keys(item.completed).length;
                    pushObject.column3 = Object.keys(item.incomplete).length;
                  } else {
                    pushObject.column2 = Object.keys(item.certificated).length;
                    pushObject.column3 = Object.keys(item.certificated_missing).length;
                  }
                  pushObject.column4 = Object.keys(item.users).length;
                }

                if (type === 'organization') {
                  pushObject.color = '#e0e3e4';
                } else if (type === 'training') {
                  pushObject.color = '#5BBFDE';
                }

                this.report_data.push(pushObject);

                this.setUserListObject(item);

                if (typeof item.training != 'undefined' && item.training) {
                  this.setData(item.training, 'training');
                }

                if (typeof item.courses != 'undefined' && item.courses) {
                  this.setData(item.courses, 'courses');
                }

              }
            },
            setUserListObject(item) {
              if (typeof item.users !== 'undefined') {
                let pushObject = {
                  type: 'users',
                  column1: '',
                  column2: '',
                  column3: '',
                  column4: '',
                  color: "#fff"
                };
                if (this.mode_select === 'completed') {
                  if (typeof item.completed !== 'undefined') {
                    pushObject.column2 = this.setUserList(item.completed);
                  }
                  if (typeof item.incomplete !== 'undefined') {
                    pushObject.column3 = this.setUserList(item.incomplete);
                  }
                } else {
                  if (typeof item.certificated !== 'undefined') {
                    pushObject.column2 = this.setUserList(item.certificated);
                  }
                  if (typeof item.certificated_missing !== 'undefined') {
                    pushObject.column3 = this.setUserList(item.certificated_missing);
                  }
                }
                pushObject.column4 = this.setUserList(item.users);
                this.report_data.push(pushObject);
              }
            },
            setUserList(users) {
              let display_text = '';
              let display_array = [];
              for (const [key, item] of Object.entries(users)) {
                  display_array.push(item.fullname);
              }
              if (display_array.length !== 0) {
                display_text = display_array.join('<br/>');
              }
              return display_text;
            }
        },
        mounted() {
          this.preloadData();
        },
    }

    function convertUtf8(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
        str = str.replace(/đ/g,"d");
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
        str = str.replace(/ + /g," ");
        str = str.trim();
        return str;
    }
</script>

<style scoped>
    .hidden {
        display:none;
    }
</style>
