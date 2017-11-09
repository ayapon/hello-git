=== Welcart e-Commerce ===
Contributors: Collne Inc., uscnanbu
Tags: Welcart, e-Commerce, shopping, cart, eShop, store, admin, calendar, manage, plugin, shortcode, widgets, membership
Requires at least: 4.0
Tested up to: 4.8
Requires PHP: 5.6 or greater
Stable tag: 1.9.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Welcart is a free e-commerce system plugin with top market share in JAPAN.

== Description ==

Welcart is a free e-commerce system plugin with top market share in JAPAN.
Welcart has all features with flexibility to make an online shop.
You can easy to make your original shop on Wordpress.


= SHOPPING CART SYSTEM =

You can sell any type of product (physical, digital, download contents). 
No limit the number of products, item photos, categories.
(The server specs require is different on each web site. You need to prepare the appropriate server.)
You can manage the items by SKU. (Stock Keeping Unit)
Welcart has many options of the price and the shipping settings.
You can apply to 16 payment services through welcart official page. (Sony Payment, Paypal, Softbank Payment etc.)
Please refer to below link (Japanese environment).
[Welcart Payment services](https://www.welcart.com/wc-settlement/)


= DESIGN =
Welcart has free standard templates.
You can customize the design and the layout as you like.
Welcart design theme is compliant to Wordpress standard.
You can get the free parent theme for responsive web design(Welcart Basic) from the below link.

[Welcart Theme downloads](https://www.welcart.com/wc-theme/)


= MANAGING SYSTEM =
Orders will be updated the database automatically.
Welcart has a highly functional orders list.
You can search the order data by customer information, furthermore, purchasing items, etc. 
And  you can easy to manage the order on order editing page. (confirm, edit, send e-mail)


= MEMBERSHIP SYSTEM =
You can use special settings for membership customer without plugins.
As well as the orders list, welcart has a highly functional menbers list.
You can search the member data by customer information, the history of purchasing items, etc.
Membership orders can be edited individually.
And you can use a point system as standard function.

[Welcart Community(Japanese)](http://www.welcart.com/wc-com/).


== Installation ==

= AUTOMATIC INSTALLATION =

In your WordPress admin panel, go to Plugins > New Plugin, search for Welcart e-Commerce for WordPress and click “Install now “.


= MANUAL INSTALLATION =

1. Alternatively, download the plugin and upload the entire usc-e-shop folder to the /wp-content/plugins/ directory.
2. Activate the plugin.


= ATTENTION =
 
In the process of activation of plugin, Welcart writes data on tables such as postmeta, options, and terms. When you install a blog existing already, to avoid unexpected damages or any other unexpected situation, backing up is strongly recommended.

Welcart is not responsible or does not have any guarantee for any kind of damage that you get by using or installing Welcart.
All the procedures must be done with self-responsibility of each user.


= RECOMMENDED ENVIRONMENT =

WordPress Ver.4.0 or greater
PHP 5.6, 7.0
MySQL 5.1 or greater
Original domain and SSL (Shared SSL is not recommended)

Note: The server which PHP is working as safe mode is not supported.

Please refer to [Server Requirement (Japanese environment)](https://www.welcart.com/wc-condition/)


== Frequently Asked Questions ==

Please see [Welcart Forum](http://www.welcart.com/community/forums).


== Screenshots ==

1. Item List page on the admin screen
2. Editing orders page on the admin screen
3. Top page(Free official theme 'Welcart Basic') 
4. Item page(Free official theme 'Welcart Basic') 


== Changelog ==

= V1.9.4 =
-----------
12 Sep 2017
* Fixed an object injection vulnerability
  We discovered a dangerous Object Injection vulnerability in front page.
  Please upgrade Ver.1.9.4 immediately. All the past versions are the target.
  Technical countermeasure details are here the link.
  https://plugins.trac.wordpress.org/changeset?sfp_email=&sfph_mail=&reponame=&new=1728429%40usc-e-shop&old=1728428%40usc-e-shop&sfp_email=&sfph_mail=

* Changed the print of the first page footer information on delivery note PDF doesn’t show in case of multiple pages.
* Added 【UnionPay】 option in the payment module of Softbank Payment.
* Changed to no- public category is displayed on the selection on item lists in case of the search item category.
* Changed the consumption tax is included when the payment is points only.
* Fixed the bug of delivery date settings.
* Fixed the duplicate id value of “mailadress1” on “wc_customer_page.php”.
* Fixed the lack information of order data in case of PayPal webpayment plus.
* Fixed the bug of point form.
* Corresponded the specifications change of “Yahoo! wallet”.

= V1.9.3 =
-----------
5 Jul 2017
* Fixed the bug of duplicated payments on specific servers. 【WelcartPay】【e-SCOTT Smart】
* Fixed the bug of error message on【e-SCOTT Smart】.
* Changed the specification of 【WelcartPay】.
* Fixed the bug of the item name length of 【Pay design】.
* Fixed the bug of PayPal settings caused by API expired.
* Fixed the bug of the link display on the member page of 【ZEUS】.
* Fixed the bug of calculating the price in case of using points.
* Changed JAVA scripts alert on confirmation page to decrease abandonment.

= V1.9.2 =
-----------
28 Apr 2017
* Added the feature of post payment “ATODENE” on 【WelcartPay】.
* Fixed the bug of calculating the price in case of BankCheck payment on 【CloudPayment】.
* Fixed the bug of installment payment number on e-mail. 【WelcartPay】.
* Fixed the bug of the item list page on admin page.
* Corresponded the version upgrade of WCEX DLSeller 3.0.
* Fixed the bug of credit card information display on 【WelcartPay】.
* Changed to make the failure log of auto-renewable subscriptions on 【WelcartPay】.
* Fixed the bug of filtering by delivery method on order list.
* Fixed the bug of payment error in case of using coupons on 【WelcartPay】【SONY Payment】.
* Fixed the bug of notice message on admin and order data editing page.
* Fixed the bug of the data getting of receiving agency of online transaction on 【WelcartPay】.
* Fixed the bug of the link display on my page of 【WelcartPay】.
* Added the feature of updating the data only “the stock amount” or “the stock status” on item batch registration and item data export.
* Fixed the country code of Sweden.

= V1.9.1 =
-----------
26 Dec 2016
* Added the feature of CSV export that items have multiple custom field and same key.
* Changed the specification of 【E-SCOTT】 and 【WelcartPay】 to disable to delete account on My Page.
* Fixed the calculate system of point.
* Added SKU code to item code of item number on 【PayPal EC】.
* Added the function of item batch registration and item data export to resister/export the category by slug.
* Fixed the bug of point adding on order editing page.
* Fixed the bug of registration member information on front page.
* Fixed the bug of the error in comment meta box on item master.
* Fixed the bug of updating member data.
* Fixed the bug of the base country data getting in payment method page.
* Fixed the bug of the advance value disappearing in case of updating sku information when using WCEX_SKU_SELECT + advance field.
* Added the feature of countermeasure for protect the member registration spam.
* Fixed the bug of character automatically changing from [+] to [ ] on some prints and e-mails.
* Fixed the bug of the totals print on invoice PDF.
* Fixed the bug of the error when the agencies unavailable on credit payment selecting page.
* Changed the color of “Credit sales accounting” on 【WelcartPay】.
* Fixed the bug of sorting on delivery /payment page.
* Fixed the bug of exporting payment error log.
* Fixed the bug of displaying the status of deposit confirmation on order editing page.

= V1.9 =
-----------
5 Oct 2016
* Added the new payment module ‘WelcartPay’
* Fixed the bug that the sub-image is not recognized

= V1.8 =
-----------
8 Apr 2016
* Corresponding to WordPress 4.5
* Added the new order list function
* Added the new member list function

= V1.7 =
-----------
29 Jan 2016
* Changed “wc_templates” of child theme.
* Added session checking when user opens multiple tabs in the confirmation page.
* Fixed some bugs.

= V1.6 =
-----------
30 Oct 2015
* Added the function to search an address using the Japanese postal codes.
* Added the function to output the Ganbare-Tencho-CSV data. [Extensions]
* Added the option to describe customer name at the beginning of e-mail message.
* Changed the trucking number and shipping company name can register in order edit screen.
* Fixed the bug that reservations post is not published in that time, when product register in bulk.

= V1.5 =
-----------
1 Oct 2015
* Added check boxes and radio buttons in the product options
* Added compatible with PayPal WebPaymentPlus
* Fixed some vulnerabilities 

= V1.4 =
-----------
30 May 2014
* Corresponded PHP5.4
* Changed the database structure of orders related
* Changed the order list can search by the trade name
* Changed Welcart members can register from the management screen
* Added the payment module of Veritrans
* Added the user rights only Welcart

= V1.3 =
-----------
15 Mar 2013
* Added Japanese credit payment service.

= V1.2 =
-----------
8 Sep 2012
* Added OGP function on the product details page 
* Added the purchase upper limit in case of the C.O.D.
* Added the recalculate function of the consumption tax or the point of the order data editing
* Added the option of automatically emailing to the administrator
* Added the backup & reset function with an option level (basic setting, others)

= V1.1 =
-----------
25 Jan 2012
* Changed the amount of product data export and import by CSV at once
* Added the disable option of ‘usces_cart.css’.
* Added the function of making random string order number.
* Added the function of selecting the applicable rules of product Sub-Image.
* Changed the function that SKU and product option ordering can be changed by dragging.
* Changed the function that common options and payment methods ordering can be changed by dragging.

= V1.0 =
-----------
30 Apr 2011
* Welcart Default theme is upgraded to 1.1. Added wc_templates.
* Supported the globalization of global currency and form entry.
* Added the function of calculating the date of requested delivery.
* Added the payment module of PayPal Express Checkout.
* Changed the function of exporting PDF to be able to export invoice and receipt.

== Upgrade Notice ==

= 1.9.4 =
This version fixes an object injection vulnerability. Upgrade immediately.

