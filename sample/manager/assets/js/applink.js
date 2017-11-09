//anchor linkのページ遷移対策
$(document).on('click', 'a', function(e) {
  var $a = $(e.target);
  if (!$a.attr('href').match(/^#/)) {
    e.preventDefault();
    window.location = $a.attr('href');
  }
});

