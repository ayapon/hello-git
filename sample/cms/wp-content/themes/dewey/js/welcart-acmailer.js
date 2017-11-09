jQuery(function ($) {
    
    $('#newmember input[name="custom_member[mailmagazinem][受け取る]"]').change(function(){
    var mailaddress = $('#newmember input[name="member[mailaddress1]"]').val();
    if ($(this).is(':checked')) {
            $.ajax({
                type: "POST",
                url: "https://nightworks.jp/mail/reg.cgi",
                data: "email=" + mailaddress + "&reg=add&encode=文字コード",
                timeout: 5000,
                dataType: 'text'
            });  
    } else {
            $.ajax({
                type: "POST",
                url: "https://nightworks.jp/mail/reg.cgi",
                data: "email=" + mailaddress + "&reg=del&encode=文字コード",
                timeout: 5000,
                dataType: 'text'
            });
    }
    });
    
    if (0 < $("#memberinfo .customer_form").size()) {
        var mailaddress0 = $('.customer_form input[name="member[mailaddress1]"]').attr('value');
        var regdata0 = "email=" + mailaddress0 + "&reg=del&encode=文字コード";
        
            $('#memberinfo input[name="custom_member[mailmagazinem][受け取る]"]').change(function(){
                var mailaddress1 = $('.customer_form input[name="member[mailaddress1]"]').val();
                if ($(this).is(':checked')) {
                    $.ajax({
                        type: "POST",
                        url: "https://nightworks.jp/mail/reg.cgi",
                        data: "email=" + mailaddress1 + "&reg=add&encode=文字コード",
                        timeout: 5000,
                        dataType: 'text'
                    });  
                } else {
                    $.ajax({
                        type: "POST",
                        url: "https://nightworks.jp/mail/reg.cgi",
                        data: "email=" + mailaddress1 + "&reg=del&encode=文字コード",
                        timeout: 5000,
                        dataType: 'text'
                    });
                }
            });
        
        $('.customer_form input[name="member[mailaddress1]"]').change(function(){
            var mailaddress2 = $('.customer_form input[name="member[mailaddress1]"]').val();
            if ($('#memberinfo input[name="custom_member[mailmagazinem][受け取る]"]').is(':checked')) {
            $.ajax({
                type: "POST",
                url: "https://nightworks.jp/mail/reg.cgi",
                data: regdata0,
                timeout: 5000,
                dataType: 'text'
            }).done(function(data){
                mailaddress0 = $('.customer_form input[name="member[mailaddress1]"]').val();
                regdata0 = "email=" + mailaddress0 + "&reg=del&encode=文字コード";
            });
            $.ajax({
                type: "POST",
                url: "https://nightworks.jp/mail/reg.cgi",
                data: "email=" + mailaddress2 + "&reg=add&encode=文字コード",
                timeout: 5000,
                dataType: 'text'
            });
            }
        });
        
        $('.send input[name="deletemember"]').hide();
        $('.send input[name="deletemember"]').after('<input name="pre_deletemember" type="submit" value="退会する" class="deletemember">');
        
        $('#memberinfo .send input[name="pre_deletemember"]').on('click',function () {
            if(confirm('退会と同時にすべてのご利用プランも契約解除されます。よろしいですか？')==true){
            $.ajax({
                type: "POST",
                url: "https://nightworks.jp/mail/reg.cgi",
                data: regdata0,
                timeout: 5000,
                dataType: 'text'
            });
            deletemember_check();
           }
        }); 
    }
});

function deletemember_check(){
  var ret = confirm("会員に関する全ての情報が削除されます。よろしいですか？");
  if (ret == true){
    jQuery(function(){
      $('.send input[name="pre_deletemember"]').after(
        '<input name="deletemember" value="会員情報を削除する" type="hidden">'
      );
    });
  }
}