<template>
    <nav class="navbar navbar-expand-xl navbar-light fixed-top hk-navbar">
        <a id="navbar_toggle_btn" class="navbar-toggle-btn nav-link-hover" href="javascript:void(0);"><i
                class="fa fa-bars"
                aria-hidden="true"></i></a>
        <a class="navbar-brand d-none d-sm-flex" href="javascript:void(0)" @click="gotoDashboard()">
            <img id="vl_logo" class="brand-img d-inline-block" style="height: 34px;"
                 src="/assets/dist/img/logo-black.png" alt="Easia"/>
        </a>
        <ul class="navbar-nav hk-navbar-content">
            <li class="nav-item dropdown dropdown-authentication">
                <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <div class="media">
                        <div class="media-img-wrap">
                            <div class="avatar" style="margin-right: 5px;">
                                <img :src="avatar"
                                     alt="user" class="avatar-img rounded-circle">
                            </div>
                        </div>
                        <div class="media-body">
                            <span>{{ fullname }}</span>
                        </div>

                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                    <router-link class="dropdown-item" :to="{ name: 'Profile' }">
                      <i class="dropdown-icon zmdi zmdi-account"></i>
                      <span>{{ trans.get('keys.thong_tin_ca_nhan') }}</span>
                    </router-link>
                    <a class="dropdown-item" href="#"><i
                            class="dropdown-icon zmdi zmdi-settings"></i><span>{{ trans.get('keys.cai_dat') }}</span></a>
                    <a class="dropdown-item" href="javascript:void(0)" @click.prevent="logOut()"><i
                            class="dropdown-icon zmdi zmdi-power"></i><span>{{ trans.get('keys.dang_xuat') }}</span></a>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-notifications">
                <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <h6 class="dropdown-header">Thông báo <a href="javascript:void(0);" class="">Xem tất cả</a></h6>
                    <div class="notifications-nicescroll-bar">
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-sm">
                                        <img :src="avatar"
                                             alt="user" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="notifications-text"><span class="text-dark text-capitalize">{{ username }}</span><br>đã thêm bạn vào khóa học <strong>ABC001</strong></div>
                                        <div class="notifications-time">12 phút</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-primary rounded-circle">
                                                    <span class="initial-wrap"><span>T</span></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="notifications-text">Bạn có 1 tin nhắn từ <span class="text-dark text-capitalize">{{ username }}</span></div>
                                        <div class="notifications-time">1 giờ</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-primary rounded-circle">
                                                    <span class="initial-wrap"><span><i
                                                            class="zmdi zmdi-notifications font-18"></i></span></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="notifications-text">Khóa học<span class="text-dark text-capitalize"> ABC01</span>
                                            sẽ diễn ra vào <span
                                                    class="text-dark text-capitalize">Thứ 2, Ngày 19/08</span>
                                            lúc <span class="text-dark">10giờ : 30phút</span></div>
                                        <div class="notifications-time">2 ngày</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-primary rounded-circle">
													<span class="initial-wrap"><span>K</span></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="notifications-text">Yêu cầu từ <span
                                                class="text-dark text-capitalize">Nguyễn Đạt Khuê</span>
                                            muốn đăng ký tham gia khóa đào tạo của bạn
                                        </div>
                                        <div class="notifications-time">1 tuần</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-warning rounded-circle">
													<span class="initial-wrap"><span><i
                                                            class="zmdi zmdi-notifications font-18"></i></span></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div>
                                        <div class="notifications-text">Bạn đã bỏ lỡ khóa học <span
                                                class="text-dark text-capitalize">ABC02</span></div>
                                        <div class="notifications-time">15d</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item language_dropdown">
<!--                <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown"-->
<!--                   aria-haspopup="true" aria-expanded="false">-->
<!--                    <span v-bind:class="classObject">-->

<!--                    </span>-->
<!--                    <i class="fa fa-sort-desc"></i>-->
<!--                </a>-->
                <div class="dropdown-menu dropdown-menu-right">
<!--                  <template v-if="domainWeb.indexOf('localhost') == 0 || domainWeb.indexOf('127') == 0">-->
<!--                    <a class="dropdown-item" @click="setLang('vi')"><span-->
<!--                      class="language_icon language_vi"></span> Tiếng Việt</a>-->
<!--                  </template>-->
<!--                    <a class="dropdown-item" @click="setLang('en')"><span-->
<!--                            class="language_icon language_en"></span> English</a>-->
                </div>
            </li>
        </ul>
    </nav>
</template>

<script>
    import Auth from '../../../services/auth'
    import Ls from '../../../services/ls'

    export default {
        components: {},
        data() {
            return {
                url_redirect: '/sso/authenticate?apiKey=bd629ce2de47436e3a9cdd2673e97b17&callback=',
                username: 'guest',
                fullname: 'Guest',
                avatar: '/images/user.png',
                classObject: 'language_icon language_en',
                domainWeb: ''
            }
        },

        methods: {
            getDefaultValue() {
                // this.setLang('en');
                let obj = Ls.get('auth.user');
                // let lang = Ls.get('auth.lang');
                // if(this.domainWeb.indexOf('dev') == 0)
                let lang = 'en';
                // Ls.get('auth.lang');
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    this.username = user_info.username;
                    this.fullname = user_info.fullname;
                    this.avatar = user_info.avatar;
                    this.classObject = 'language_icon language_' + lang;
                }
                if (obj && obj !== 'undefined') {
                    this.classObject = 'language_icon language_' + lang;
                }
            },
            setLang(lang) {
                Ls.set('auth.lang', lang);
                axios.get('/setLocale/' + lang)
                    .then(response => {
                        this.classObject = 'language_icon language_' + Ls.get('auth.lang');
                        location.reload();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            logOut() {
                Auth.logout().then(() => {
                    window.location.href = '/';
                })
            },
            gotoDashboard() {
                this.$router.replace('/tms/dashboard')
            }
        },
        mounted() {
            this.getDefaultValue();
            this.domainWeb = location.hostname;
        }
    }
</script>

<style scoped>

</style>
