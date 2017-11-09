<style>
    #gopro{
        width: 100%;
        display: block;
        clear: both;
        padding: 10px;
        margin: 10px 8px 15px 5px;
        border: 1px solid #e1e1e1;
        background: #464646;
        color: #ffffff;
        overflow: hidden;
    }
    #wrapper{
        border: 1px solid #f0f0f0;
        width: 95%;

    }
    #wrapper{
        border: 1px solid #f0f0f0;
        width: 95%;

    }
    table.widefat{
        margin-bottom: 15px;
    }
    table.widefat tr{
        transition: 0.3s all ease-in-out;
        -moz-transition: 0.3s all ease-in-out;
        -webkit-transition: 0.3s all ease-in-out;
    }
    table.widefat tbody tr:nth-child(odd){
        background: #F5F5F5;
    }

    #wrapper input[type='text']{
        width: 80%;
        transition: 0.3s all ease-in-out;
        -moz-transition: 0.3s all ease-in-out;
        -webkit-transition: 0.3s all ease-in-out;
    }
    #wrapper input[type='text']:focus{
        border: 1px solid #1784c9;
        box-shadow: 0 0 7px #1784c9;
        -moz-box-shadow: 0 0 5px #1784c9;
        -webkit-box-shadow: 0 0 5px #1784c9;
    }
    #wrapper input[type='text'].small-text{
        width: 20%;
    }
    .proversion{
        color: red;
        font-style: italic;
    }
    .choose-progress{
        display: none;
    }
    .sc_popup_mode{
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 100%;
        position: relative;
        top: 2px;
        box-shadow: 0 0 3px #333;
        -moz-box-shadow: 0 0 3px #333;
        -webkit-box-shadow: 0 0 3px #333;
    }

    .sc_popup_mode_off{
        background: #F54412;
    }
    .sc_popup_mode_live{
        background: #84E11F;
    }
    .sc_popup_mode_test{
        background: #FF9717;
    }
    .left{ float: left;}
    .right {float: right;}
    .center{text-align: center;}
    .width70{ width: 70%;}
    .width25{ width: 25% !important; margin-left: 2%; }
    .width50{ width: 50%;}
    .larger{ font-size: larger;}
    .bold{ font-weight: bold;}
    .editcursor{ cursor: text}
    .red{ color: #CC0000; font-size: 12px;}
</style>
<?php $license = get_option('smartcat_testimonials_key'); $status = get_option('smartcat_testimonials_status'); ?>
<div id="wrapper">
    <div id="gopro">
        <div class="left ">
            <h1><b><?php _e('Testimonials & Reviews Showcase Settings', 'smartcat_testimonials'); ?></b></h1>
            <!--<div><em>Why go pro?</em> 4 More templates including a slider and grid display. Frontend submission form allowing your site visitors to leave you reviews & testimonials!</div>-->

        </div>

        <div class="right ">
            <a href="https://smartcatdesign.net/our-testimonials-showcase-demo/" target="_blank" class="button-secondary" style="padding: 20px;line-height: 0;font-size: 16px">View Demo</a>
            <a href="https://smartcatdesign.net/downloads/testimonials-reviews-showcase/" target="_blank" class="button-primary" style="padding: 20px;line-height: 0;font-size: 16px">Get the Pro Version</a>
        </div>
              
        
        
    </div>
