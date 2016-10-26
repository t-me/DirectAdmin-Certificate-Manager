<?php

$root = dirname(__FILE__);;

class quickprotect {

    private static $tries;
    public static $errormsg;
    public $settings;

    public function __construct() {
        session_start();

		require_once $GLOBALS['root'] . '/../functions.php';
		$this->func = new cm_function();
		
		require_once $GLOBALS['root'] . '/../config.php';
		$config = new Config_Lite($GLOBALS['root'] . '/../../config/config.ini');
        $this->settings = $config->get('dashboard');
		$this->root = $config->get('root');
		
        if (!$this->settings) {
            die("Failed to load the config file located at: $this->ini_file_location.");
        }
    }

    public function login($username, $password) {
    //Performs login by initializing $_SESSION vars
    //Returns true or false, plus $_SESSION message on success/failiure
    //Contains brute force protection
        if (!isset($_SESSION['tries'])) $_SESSION['tries'] = 1;

        if ($_SESSION['tries'] <= intval($this->settings["MAX_TRIES"]) || $this->settings["UNLIMITED_TRIES"] == true) {//Try to prevent brute forcing
            if ($this->func->encrypt($password,$this->root["privatekey"]) == trim($this->settings["PASSWORD"]) && $username === $this->func->decrypt($this->settings["USERNAME"],$this->root["privatekey"])) {
                if (session_start()) {
                    $_SESSION['tries'] = 0; //Resets brute force prevention
                    $_SESSION['logged_in'] = md5($this->settings["PASSWORD"]); //used for checking if the user is logged in or not
                    return true;
                }
            }
            else {
                self::$errormsg = "Buddy, nice try, but wrong password or username. This is your attempt #".$_SESSION['tries'];
                $_SESSION['tries']++; // Records the incorrect try
                return false;
            }
        }
        else {self::$errormsg = "You are trying to login too much. <b>THIS SOFTWARE IS NOW IN LOCKDOWN MODE. YOU CANNOT ENTER.</b> <br><br> If you are a legit admin, edit the login.ini file, scroll down to the bottom, and edit the appropriate settings to disable this lockdown."; return false;}
    }

    public function is_logged_in() {
    //Determines if a user is logged in or not. Returns true or false;
        if ($_SESSION['logged_in'] === md5($this->settings["PASSWORD"])) {
            return true;
        }
        else return false;
    }

    public function checkLoginAndDirect($loginpage = "") {
    //If user is not logged in, this function directs the guy to the login page
    //Setting $loginpage is optional. The default is stored in the INI file
    //$loginpage path must be in relation to the file that is calling this function.

        if (empty($loginpage)) $loginpage = $this->root["base_url"];

        $_SESSION['goAfterLogin'] = $_SERVER['REQUEST_URI'];

        if (!$this->is_logged_in()) {
        //Tries both PHP and JS redirects or dies
            header("Location: $loginpage");
            echo'<script type="text/javascript"><!-- window.location = "'.$loginpage.'" //--></script>';
            die ("I would love to serve you the current page, but unfortunately you are not logged in, my friend.");
        }
    }

    public function setNewPassword($username, $oldpassword, $newpassword) {
    //This function sets a new password by changing the login.ini.php file
    //The file must have write permissions for this to work
    //Returns true or false on failure, as well as $_SESSION error/success message

        if ($this->login($username, $oldpassword)) {
            $inifile = file_get_contents($this->ini_file_location);

            $inifile = str_replace(sha1($oldpassword),sha1($newpassword),$inifile);

            $fh = fopen($this->ini_file_location, 'w') or die("Can't open INI file");

            if (fwrite($fh, $inifile)) {
                self::$errormsg = "Changed password successfully.";
                return true;
            }
            else {
                self::$errormsg = "Could not write to INI file. Please check file permissions";
                return false;
            }

            fclose($fh);
        }
        else {
            return false;
            self::$errormsg = "The old password/username you entered was not valid.";
        }
    }

    public function logout() {
    // Performs logout. Returns true or false on success/failiure.
        $_SESSION['logged_in'] = false;
        unset($_SESSION['logged_in']);
        session_destroy(); //Destroy all session vars
        session_start(); //Start new session (for storing info about new login)

        if ($this->is_logged_in()) return false;
        else {return false; self::$errormsg = "There was an error logging you out.";}
    }

    public function echoMsg() {
    //This simply prints any generated messages.
    //If you have a custom error message handler, you can implement it here.
        echo self::$errormsg;

        //Unsetting errormsg
        self::$errormsg='';
    }
}
?>