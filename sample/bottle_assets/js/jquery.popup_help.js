(function($) {
  $.fn.popupHelp=function(config) {
    /**
      * marginTop: 表示対象とウィンドウの高さの差分です。
      *   0を指定すると、上端が揃います。
      * marginLeft: 表示対象とウィンドウの横の差分です。
      *   0を指定すると、左端が揃います。
      * className: ウィンドウに設定するクラス名です。
      * speed: ウィンドウを表示する際の秒数[ms]です。
      */
    var defaults = {
      marginTop: 0,
      marginLeft: 20,
      className: "popup_help_window",
      speed: 300
    }

    var options = $.extend(defaults, config);

    // ヘルプウィンドウのオブジェクトを準備します。
    var popupObj = $("<p/>").addClass(defaults.className).appendTo($("body"));

    return this.each(function() {

      $(this).mouseover(function() {
        // 表示対象にマウスが重なった時の処理です。

        // ウィンドウにメッセージを設定します。
        popupObj.text($(this).attr('data-message'));

        // ウィンドウのオフセットを計算します。
        var offsetTop = $(this).offset().top + defaults.marginTop;
        var offsetLeft = $(this).offset().left + defaults.marginLeft;

        // ウィンドウの位置を整え、表示します。
        popupObj.css({
          "top": offsetTop,
          "left": offsetLeft
        }).show(defaults.speed);

      }).mouseout(function() {
        // 表示対象にマウスが重なった時の処理です。
        // テキストを空にして、ウィンドウを隠します。
        popupObj.text("").hide("fast");
      });
    });
  };
})(jQuery);
