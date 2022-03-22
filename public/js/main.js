(function($) {
    $(document).ready(function() {

        // $('ul.navbar-nav .nav-item .nav-link.has-submenu').click(function(){
        //   console.log(11111);
        // 	//var level = $(this).attr('data-level');
    		// /*$('ul.navbar-nav .nav-item ul.nav.'+level).removeClass('show');
    		// $('ul.navbar-nav .nav-item ul.nav.'+level).slideUp();*/
        // 	if(!$(this).hasClass('active')){
        // 		//$('ul.navbar-nav .nav-item .nav-link.has-submenu.'+level).removeClass('active');
        // 		$(this).addClass('active');
        // 		$('+ ul.nav',$(this)).slideDown();
        // 	}else{
        // 		//$('ul.navbar-nav .nav-item .nav-link.has-submenu.'+level).removeClass('active');
        //         $(this).removeClass('active');
        //         $('+ ul.nav',$(this)).slideUp();
        // 	}
        // });

        //search box
        $('body').on('click','.btn_search_box',function(){
            if(!$(this).hasClass('disabled')){
                var parent = $(this).parent();
                if($('.content_search_box',parent).hasClass('active')){
                    $('.content_search_box',parent).removeClass('active');
                    $('.overwrap_search_box').removeClass('active');
                }else{
                    $('.content_search_box',parent).addClass('active');
                    $('.overwrap_search_box').addClass('active');
                }
            }

        });
        $('body').on('click','.overwrap_search_box',function(){
            $('.content_search_box').removeClass('active');
            $('.overwrap_search_box').removeClass('active');
        });
        $('body').on('click','.content_search_box ul li',function(){
            if(!$(this).hasClass('disable')){
                var parent = $(this).parents('.wrap_search_box');
                var content = $(this).html();
                $('.overwrap_search_box').removeClass('active');
                $('.content_search_box',parent).removeClass('active');
                $('.content_search_box ul li',parent).removeClass('active');
                $(this).addClass('active');
                if(!$(parent).hasClass('multi')){
                    $('.btn_search_box span',parent).html(content);
                }
            }
        });

        $('body').on('focus','#inputUsername,#inputPassword',function(){
            $(this).removeAttr('readonly');
        });

        $('body').on('click','a#nav_close,#hk_nav_backdrop',function(){
            $('.hk-wrapper').removeClass('hk-nav-toggle');
        });

        $('body').on('click','.btn_open_select',function(){
            var parent = $(this).parents('tr');
            if($(this).hasClass('actives')){
                $(this).removeClass('actives');
                $('.wrap_select',parent).removeClass('show');
            }else{
                $('.btn_open_select').removeClass('actives');
                $('.wrap_select').removeClass('show');
                $(this).addClass('actives');
                $('.wrap_select',parent).addClass('show');
            }
            return false;
        });
    });
})(jQuery);
