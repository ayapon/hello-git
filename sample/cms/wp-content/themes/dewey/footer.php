
	<?php if(! wp_is_mobile()): ?>
	
		<div id="toTop" class="wrap fixed"><a href="#<?php echo esc_attr( $post->post_name ); ?>" class="scroll"><i class="fa fa-2 fa-chevron-circle-up"></i></a></div>
	
	<?php endif; ?>
	
    <footer>
      <div class="container">
        
        <div class="row margin-30 news-container">
          <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 wow fadeInLeft" data-wow-delay="1.2s">
            <a href="http://www.dewey.co.jp" target="_blank">
            <img class="news-img pull-left" src="/images/dewey-logo.png" alt="DEWEY Inc." style="margin: 5px 0 20px 0;">
            <p class="black"><span class="x-small">Produced by</span><br />
<strong>株式会社デューイ</strong><br /> 
            <span class="small">〒650-0011 神戸中央区下山手通2-13-11 トーアロードビル5F<br />
<span style="font-size:86%;line-height:1.2em;"> Tel：078-381-9877　Fax：078-330-0044</span></p>
            </a>
          </div>
        </div>
        
        <div class="row">
          <div class="col-sm-6 margin-20">
            <ul class="list-inline social">
              <li>Connect with us on</li>
<!--              <li><a href="#"><i class="fa fa-twitter"></i></a></li>-->
              <li><a href="http://www.dewey.co.jp" target="_blank" class="homepage"><i class="fa fa-home"></i></a></li>
              <li><a href="https://www.facebook.com/dewey.kobe" target="_blank" class="facebook"><i class="fa fa-facebook"></i></a></li>
              <li><a href="https://line.me/ti/p/%40krz2754l" target="_blank" class="line_at"><img src="/images/icon_line.png" alt="icon_line" /></a></li>
<!--              <li><a href="#"><i class="fa fa-instagram"></i></a></li>-->
            </ul>
            <p><small><a href="https://nightworks.jp/privacy">プライバシーポリシー</a></small></p>
          </div>
          
          <div class="col-sm-6 text-right">
            <p><small>Copyright &copy; <script type="text/javascript">myDate = new Date();myYear = myDate.getFullYear();document.write(myYear);</script>. All rights reserved. <br>
	            Produced by <a href="http://www.dewey.co.jp">DEWEY Inc.</a></small></p>
          </div>
        </div>
        
      </div>
    </footer>
    
	<?php wp_footer(); ?>
    
    <!-- Javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-1.11.0.min.js"><\/script>')</script>
    <script src="/js/wow.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/main.js"></script>

  <script src="/js/modernizr-2.7.1.js"></script>

  <script src="<?php echo get_template_directory_uri(); ?>/js/iframe-auto-height.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/historyback.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/welcart-acmailer.js"></script>

<?php if(is_page( array('usces-cart', 'usces-member' ))): ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.validationEngine.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.validationEngine-ja.js"></script>
<script type="text/javascript">
jQuery(function(){
    jQuery("form").validationEngine();
});
</script>
<?php endif; ?>

	</body>
</html>
