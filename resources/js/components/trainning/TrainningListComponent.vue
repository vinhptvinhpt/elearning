<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.quan_tri_khung_nang_luc') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_khung_nang_luc')}}</h5>
          <div class="row mb-4">
            <div class="col-sm">
              <div class="accordion" id="accordion_1">
                <div class="card" style="border-bottom:1px solid rgba(0,0,0,.125) !important;">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                       aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_knl')}}</a>
                  </div>
                  <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                    <div class="card-body">
                      <trainning-create></trainning-create>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">
                <div class="row">
                  <div class="col-sm-6 col-md-8">
                    <div class="dataTables_length">
                      <span class="d-inline-block">{{trans.get('keys.hien_thi')}}</span>
                      <select v-model="row"
                              class="custom-select custom-select-sm form-control form-control-sm d-inline-block"
                              @change="getTrainnings(1)">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-4">
                    <form v-on:submit.prevent="getTrainnings(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control search_text"
                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_knl')+' ...'"/>
                        <button type="button" id="btnFilter"
                                class="btn btn-primary btn-sm"
                                @click="getTrainnings(1)">
                          {{trans.get('keys.tim')}}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>

                <br/>
                <table id="datable_1" class="table_res">
                  <thead>
                  <tr>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma_knl')}}</th>
                    <th>{{trans.get('keys.ten_knl')}}</th>
                    <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(sur,index) in trainnings">
                    <td>{{ (current-1)*row+(index+1) }}</td>
                    <td>
                      {{ sur.code }}
                    </td>
                    <td>{{ sur.name }}</td>

                    <td class="text-center">

                      <router-link
                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                        :title="trans.get('keys.sua_thong_tin_knl')"
                        :to="{
                         name: 'TrainningEdit',
                         params: { id: sur.id }
                          }">
                        <span class="btn-icon-wrap"><i
                          class="fal fa-pencil"></i></span>
                      </router-link>

                      <button v-if="sur.id!=1" :title="trans.get('keys.xoa')" data-toggle="modal"
                              data-target="#delete-ph-modal"
                              @click="deletePost(sur.id)"
                              class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
                    </td>
                  </tr>
                  </tbody>
                  <tfoot>

                  </tfoot>
                </table>

                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                              :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>

</template>

<script>
  //import vPagination from 'vue-plain-pagination'
  import TrainningCreate from './TrainningCreateComponent'

  export default {
    //components: {vPagination},
    components: {TrainningCreate},
    data() {
      return {
        trainnings: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        row: 5
      }
    },
    methods: {
      getTrainnings(paged) {
        axios.post('/api/trainning/list', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row
        })
          .then(response => {
            this.trainnings = response.data.data.data;
            this.current = response.data.pagination.current_page;
            this.totalPages = response.data.pagination.total;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      onPageChange() {
        this.getTrainnings();
      },
      deletePost(id) {
        swal({
          title: "Khung năng lực đang có học viên theo học. Bạn có chắc chắn muốn xóa khung năng lực đã chọn",
          text: "Chọn 'ok' để thực hiện thao tác.",
          type: "success",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        }, function () {
          axios.post('/api/trainning/delete', {id: id})
            .then(response => {
              if (response.data.status) {
                swal({
                  title: response.data.message,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                }, function () {
                  location.reload();
                });
              } else {
                swal({
                  title: response.data.message,
                  type: "error",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                });
              }

            })
            .catch(error => {
              swal("Thông báo!", "Lỗi hệ thống. Thao tác thất bại!", "error")
              console.log(error);
            });
        });

        return false;
      }
    },
    mounted() {
      this.getTrainnings();
    }
  }
</script>

<style scoped>

</style>
