<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper text-center">
                    <h5 class="hk-sec-title">{{message}}</h5>
                    <br>
                    <a @click="logout()" class="btn btn-primary font-bold" style="color: white">Sign in </a>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["no_id", "email"],
        data() {
            return {
                message: ""
            };
        },
        methods: {
            confirm() {
                axios.get('/api/users/email/confirm/' + this.no_id + '/' + this.email)
                    .then(response => {
                        this.message = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            logout() {
                let current_pos = this;
                $.ajax({
                    type: "POST",
                    url: '/bgtlogout',
                    data: {
                        _token: '{{csrf_token()}}'
                    },
                    success: function (data) {
                        if (data.status) {
                            localStorage.setItem(logoutCookie, 'logout');
                            sslssso.logout();
                            window.location.href = "/";
                        } else {
                            swal({
                                title: current_pos.trans.get('keys.thong_bao'),
                                text: current_pos.trans.get('keys.logout_error'),
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }

                    }
                });
            }
        },
        mounted() {
            this.confirm();
        }
    };
</script>
