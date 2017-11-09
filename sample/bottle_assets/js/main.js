//history backの改善版
$(document).ready(function(){
	(function(){
	    var ans; //1つ前のページが同一ドメインかどうか
	    var bs  = false; //unloadイベントが発生したかどうか
	    var ref = document.referrer;
	    $(window).bind("unload beforeunload",function(){
	        bs = true;
	    });
	    re = new RegExp(location.hostname,"i");
	    if(ref.match(re)){
	        ans = true;
	    }else{
	        ans = false;
	    }
	    $('.historyback').bind("click",function(){
                var that = this;
	        if(ans){
	            history.back();
	            setTimeout(function(){
	                if(!bs){
	                    location.href = $(that).attr("href");
	                }
	            },100);
	        }else{
                    location.href = $(this).attr("href");
                }
	        return false;
	    });
	})();
});

//pageup
$(document).ready(function() {
	var pageup = $('.pageup');
	$(window).scroll(function () {
		if ($(this).scrollTop() > 500) {
			pageup.fadeIn();
		} else {
			pageup.fadeOut();
		}
	});
	pageup.click(function () {
		$('body, html').animate({ scrollTop: 0 }, 300);
		return false;
	});
});


//ふりがな入力のひらがな制限
function FuriganaCheck() {
   var str = document.form.kana.value;
   if( str.match( /[^ぁ-ん　s]+/ ) ) {
      alert("ふりがなは「ひらがな」のみで入力してください。");
      return 1;
   }
   return 0;
}

//ライトボックス
$(document).ready(function() {
	$('.fancybox').fancybox();
});
