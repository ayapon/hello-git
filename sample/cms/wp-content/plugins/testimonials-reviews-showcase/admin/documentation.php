<script type="text/javascript">
    jQuery(document).ready(function ($) {

//  $("#nav").sticky({topSpacing:0});

        $('a[href^="#"]').on('click', function (e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 950, 'swing');
        });
    });


</script>
<style type="text/css">

    .sub-section {
        border: thin solid #91c6c0;
        border-radius: 15px;
        width: 80%;
        padding: 25px;
        margin-bottom: 15px;
    }

    div#wrapper {
        /*background: #ffffff;*/
        color: #313131;
        margin-top: 5px;
    }

    div#header {
        margin-right: 125px;
        background: #178dc4;
        border-radius: 0px 0px 50px 0px;
        padding: 20px;
        color: #fff;
    }

    div#main {
        height: 100%;
        background: blue;
    }

    div#nav {
        height: 100% !important;
        width: 15% !important;
        float: left !important;
    }

    div#content {
        float: right !important;
        width: 82% !important;
        /*background: #ffffff;*/
    }

    div#nav-sticky-wrapper {
        display: inline;
        margin-right: 0;
    }

    div#nav-sticky-wrapper.is-sticky {
        float: left;
    }

    p {
        padding-left: 25px;
        padding-right: 125px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    p.warning {
        color: #ff7777;
        font-style: italic;
    }

    div#header img#logo {
        float: left;
        width: 75px;
        padding-right: 15px;
    }


    h1#title{
        font-family: "Raleway", Verdana, sans-serif;
        font-weight: 100;
        font-size: 30px;    
    }
    h1#subtitle{

        font-size: 22px;
        font-weight: 100;
    }

    div#nav{
        border-right: 1px solid #f9f9f9;
        background: #DDDDDD;
    }
    h1.navheading{
        font-size: 14px;
        padding-top: 15px;
        margin-right: 0px;    
    }

    #nav ul {
        list-style: none;
        padding-left: 15px;
        padding-top: 10px;
    }

    #nav li {
        display: table;
        font-size: 12px;
        font-style: normal;
        background-color: #ffffff;
        color: #178dc4;
        width: 85%;
        border: thin solid #178dc4;
        border-radius: 5px;
        margin-bottom: 2px;
        text-align: center;
        min-height: 25px;
    }

    #nav li:hover {
        background-color: #178dc4;
        color: #ffffff;
        
        cursor: pointer;
    }
    #nav li:hover a{
        color: #fff;
    }
    a.navlink {
        font-size: 12px;
        color: #178dc4;
        text-decoration: none;
        display: table-cell;
        vertical-align: middle;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    ul {
        padding-left: 65px;
    }

    li {
        /*color: #91c6c0;*/
        /*font-style: italic;*/
        list-style: square;
    }

    footer {
        text-align: right;
        padding-right: 125px;
        padding-left: 25px;
        padding-top: 50px;
        padding-bottom: 50px;
    }

    i.fa {
        padding-right: 5px;
    }    
    em.pro{
        margin-left: 5px;
        font-size: 11px;
        color: #CC0000;
        
    }
</style>

<div id="wrapper">
    <div id="header">
        <img src="<?php echo SMARTCAT_TESTIMONIALS_URL ?>inc/img/smartcat_icon.png" alt="Smartcat" id="logo">
        <h1 id="title">Testimonials & Reviews Showcase</h1>
        <h1 id="subtitle">A WordPress Plugin by Smartcat</h1>
    </div>
    <div id="main">
        <div id="nav">

            <h1 class="navheading"><i class="fa fa-cube"></i>Setup</h1>
            <ul>
                <li><a href="#welcome" class="navlink">Welcome</a></li>
                <li><a href="#overview" class="navlink">Plugin Overview</a></li>
                <li><a href="#downloading" class="navlink">Downloading</a></li>
                <li><a href="#installing" class="navlink">Installing</a></li>
                <li><a href="#shortcodes" class="navlink">Shortcodes</a></li>
            </ul>
<!--            <h1 class="navheading"><i class="fa fa-plug"></i>Plugin Usage</h1>
            <ul>	
                <li><a href="#include-sc" class="navlink">Including the Shortcode</a></li>
                <li><a href="#add-member" class="navlink">Add a New Testimonial/Review</a></li>
                <li><a href="#manage-members" class="navlink">Managing Testimonial/Reviews</a></li>
                <li><a href="#groups" class="navlink">Groups</a></li>
                <li><a href="#templates" class="navlink">Templates</a></li>
                <li><a href="#custom_templates" class="navlink">Custom Template</a></li>
                <li><a href="#view-settings" class="navlink">Settings</a></li>
                <li><a href="#sidebar-widget" class="navlink">Sidebar Widget</a></li>
            </ul>
            <h1 class="navheading"><i class="fa fa-question-circle"></i>Miscellaneous</h1>
            <ul>	
                <li><a href="#faq" class="navlink">FAQ</a></li>
                </ul>-->
                    </div>
                    <div id="content">
                        <h2 id="welcome">Welcome!</h2>
                        <p>
                            This is the documentation page for the 'Testimonials & Reviews Showcase' plugin, by Smartcat.
                            This document covers the details for both the free and the Pro versions of the plugin. <br>
                            Some of the items that only exist in the Pro version are labelled as such.
                        </p>

                        <h2 id="overview">Plugin Overview</h2>
                        <p>
                            Easily create, edit and display your Testimonials and Reviews on your site in a lightweight and appealing design.
                            This plugin has mutiple templates to display the testimonials, and allows you to control the appearance and display settings.
                            You can use the Settings page to customize the plugin behaviour and appearance.
                            
                        </p>

                        <h2 id="downloading">Downloading</h2>
                        <p>
                            After your purchase, you will receive an email receipt containing the a link
                            to download the plugin, 'Our Team Showcase Pro'. To start your download, click the product link 
                            and your download will begin. You now have two options to install the plugin.
                        </p>

                        <h2 id="installing">Installing</h2>
                        <div class="sub-section">
                            <h3>Method One</h3>
                            <p>
                                If you decide to unzip / decompress the zip file, to install the plugin you must put the unzipped folder in 
                                your WordPress directory. Navigate to the root folder of your WordPress install and open the folder labelled
                                "wp-content". From there, open the folder "plugins", and drag or copy the plugin folder into it.
                                Reload your WordPress Dashboard and you will now see the plugin appear in your plugins list. You can go ahead
                                and activate it.
                            </p>
                        </div>
                        <div class="sub-section">
                            <h3>Method Two</h3>
                            <p>
                                If you want to install the plugin directly from the WordPress Dashboard, you must leave the downloaded file in its
                                current, format (.zip). From the WordPress Dashboard, select "Plugins" from the sidebar menu. At the top of the 
                                next page, click a button labelled "Add New". Click another button, labelled "Upload Plugin", and you 
                                will be directed to a simple page that will let you upload the file. Simply navigate to and choose 
                                the plugin zip file, and select "Install Now" then Activate it.
                            </p>
                        </div>

                        <h2>Shortcodes</h2>
                        <div class="sub-section" id="shortcodes">
                            
                            <h3>Displaying The Testimonials/Reviews List</h3>

                            <p>The quick way to display the testimonials, add the shortcode <strong>[our-testimonials]</strong>.
                            Adding this shortcode will display your testimonials/reviews with the settings that you can customize from the plugin Settings page.
                            </p>
                            <p>The following parameters are also available for use in the shortcode</p>
                            
                            <ul>
                                <li><strong>group</strong>: Specify the group of testimonials/reviews to display</li>
                                <li><strong>template</strong>: Specify the template you want to use to display the testimonials/reviews. 
                                    <h4>Template Options</h4>
                                    <ul>
                                        <li>centered_list</li>
                                        <li>stacked_list</li>
                                        <li>conversation_list</li>
                                        <li>grid <em class="pro">Pro version</em></li>
                                        <li>slider <em class="pro">Pro version</em></li>
                                        <li>slider2 <em class="pro">Pro version</em></li>
                                        <li>slider3 <em class="pro">Pro version</em></li>                                        
                                    </ul>
                                    <br>
                                </li>
                                <li><strong>limit</strong>: Specify the number of testimonials/reviews to display. </li>
                                <li><strong>images</strong>: "yes" to display images. "no" to not display images </li>
                                <li><strong>italic</strong>: "yes" to display the testimonial text in italic.</li>
                                <li><strong>ratings</strong>: "yes" to display the ratings. "no" to not display ratings.</li>
                                <li><strong>date</strong>: "yes" to display the date. "no" to not display date.</li>
                            </ul>
                            
                            <p>
                                <strong>[our-testimonials group="Name of Group" template="grid" limit="5" images="yes" italic="yes" ratings="yes"]</strong>
                            </p>
                            
                            
                            <img style=" width: 400px; float: right" src="<?php echo SMARTCAT_TESTIMONIALS_URL ?>screenshot-3.jpg"/>
                            <h3>Displaying The Submission Form <em class="pro">Pro version</em></h3>
                            
                            <p>To display the front-end submission form, add the shortcode: <strong>[our-testimonials-form]</strong></p>
                            <p>The form allows your site visitors to submit their reviews, ratings and write comments about your website/business/services.
                            You can use the form to allow your customers to leave you comments on their experience. By default, all submissions are held for 
                            moderation. You can approve the submissions which will automatically add them to the list.</p>
                            
                            <h3>Form Fields:</h3>
                            <ul>
                                <li>Name <em class="pro">required</em></li>
                                <li>Title / Company Name</li>
                                <li>Website / URL</li>
                                <li>Rating ( 1 to 5 Stars )</li>
                                <li>Upload Logo/Picture/Avatar</li>
                                <li>Review / Testimonial <em class="pro">required</em></li>
                                <li>Human Verification <em class="pro">required</em></li>
                            </ul>
                            
                            <p>The front-end review/testimonial submission form has some options that can be changed from the settings page.</p>
                            <h3>Form Options</h3>
                            <ul>
                                <li>Toggle Prevent Multiple Submissions</li>
                                <li>Toggle Auto-approve/moderate submissions</li>
                                <li>Toggle Human Verification</li>
                                <li>Toggle Image Upload</li>
                            </ul>
                        </div>	
                       
                        <footer>Copyright © <a href="http://smartcat.ca">Smartcat</a></footer>
                    </div>
                    </div>
                    </div>