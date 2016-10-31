![Licence](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square) ![Build](https://img.shields.io/badge/Build-Alpa-red.svg?style=flat-square) ![Version](https://img.shields.io/badge/Version-V_0.2-green.svg?style=flat-square)
# DirectAdmin-Certificate-Manager

## **WARNING**
> **This project is still in development**
> **See #TO DO**

### About DirectAdmin-Certificate-Manager
This is a little programm for the users that has DirectAdmin as Control panel on there hosting
With this manager you can manage all your site's Certificate that you have under your account.
You can **make** and **renew** Let's Encrypt Certificates and see all the info of your current Certificates.

### Requirements
* Needs hosting with Directadmin api

### How to use
 * Download the complete package
 * Upload to your server, preferred method inside a subdirectory
 * change "config.example.ini" to "config.ini" in the directory "config"
 * Go to yourserver.com/subdirectory
 * install
 * Login and use it

### ToDo
 * SSL Cert install Step 3 : If error or warning don't show button step 4.
 * SSL Cert install Step 3 : If Everything is Correct remove the .well-know directory.
 * SSL Cert install Step 4 : Enable SSL When it is not enabled (DA API)

### Why i created DirectAdmin Certificate Manager?
Because of a simple interface to see if my websites hava a SSL Certificate and till when are the valid.

####  Thanks to
 * [Analogic (Let's Encrypt PHP class)](https://github.com/analogic/lescript)
 * [Pear (Config Lite class)](https://github.com/pear/Config_Lite)
 * [Gurayyarar (Admin Template)](https://github.com/gurayyarar/AdminBSBMaterialDesign)


License
----------
**DirectAdmin Certificate Manager** is an open source project that is licensed under the [MIT license](http://opensource.org/licenses/MIT).
