import Ls from './ls';

export default {
    login: async function (loginData) {
        try {
            let response = await axios.post('/api/auth/login', loginData);
            var data = response.data;
            if (data.status === 'SUCCESS') {
                Ls.set('auth.token', response.data.jwt);
                toastr['success']('Logged In!', 'Success');
                return response.data.jwt;
            } else if (data.status === 'FAILPASSWORD' || data.status === 'FAILUSER') {
                toastr['error']('Tài khoản hoặc Mật khẩu chưa chính xác ', 'Error');
            } else if (data.status === 'FAILBANNED') {
                toastr['error']('Tài khoản đang bị khóa, vui lòng liên hệ với quản trị viên để mở\n' +
                    '                            khóa\n' +
                    '                            tài khoản ');
            } else if (data.status === 'INVALID') {
                toastr['error']('Lỗi hệ thống hoặc định dạng dữ liệu không hợp lệ.');
            }
        } catch (error) {
            if (error.response.status === 401) {
                toastr['error']('Invalid Credentials');
            } else {
                // Something happened in setting up the request that triggered an Error
                console.log('Error', error.message);
            }
        }
    },

    async logout() {
        try {
            let token = Ls.get('auth.token');
            var data = {token: token};
            await axios.post('/api/auth/logout', data);

            Ls.remove('auth.token');
            Ls.remove('auth.user');
            Ls.remove('auth.lang');
            Ls.remove('__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye');
            // Ls.set('__c2FmYXJpVmVyaWZpY2F0aW9uVG9rZW4UfFzcvye', 'logout');
            // sslssso.logout();
        } catch (error) {
            console.log('Error', error.message);
        }
    },

    async check() {
        let token = Ls.get('auth.token');
        var data = {token: token};
        let response = await axios.post('/api/auth/check', data);
        return !!response.data.authenticated;
    }
};
