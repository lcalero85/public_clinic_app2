<?php
// Desactiva los errores deprecated y notices
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

define("DEFAULT_TIMEZONE", ""); // set php date functions timezone
define("DEVELOPMENT_MODE" , true);// set to false when in production

// return full path of application directory
define("ROOT", str_replace("\\", "/", dirname(__FILE__)) . "/");

// return the application directory name.
define("ROOT_DIR_NAME", basename(ROOT));

define("SITE_NAME", "My ClinicSystem ");


// Get Site Address Dynamically
$site_addr = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]);

//Must end with /
$site_addr = rtrim($site_addr, "/\\") . "/";

// Can Be Set Manually Like "http://localhost/mysite/".
if (php_sapi_name() === 'cli') {
    // Si corre desde CLI (cron o batch)
    $site_addr = "http://localhost/public_clinic_app/";
} else {
    // Si corre desde navegador
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $site_addr = "http://" . $host . "/public_clinic_app/";
}

define("SITE_ADDR", $site_addr);

define("APP_ID", "43fbc25307f8808c1df494b2811849f4");

// Application Default Color (Mostly Used By Mobile)
define("META_THEME_COLOR", "#000000");


//Application resource access status
define("AUTHORIZED", 200);
define("UNAUTHORIZED", 401);
define("NOROLE", 404);
define("FORBIDDEN", 403);

// Application Files and Directories 
define("IMG_DIR",  "assets/images/");
define("FONTS_DIR",  "assets/fonts/");
define("SITE_FAVICON", IMG_DIR . "favicon.jpg");
define("SITE_LOGO", IMG_DIR . "logo.jpg");

define("CSS_DIR", SITE_ADDR . "assets/css/");
define("JS_DIR", SITE_ADDR . "assets/js/");

define("APP_DIR", "app/");
define("SYSTEM_DIR", "system/");
define("HELPERS_DIR", "helpers/");
define("LIBS_DIR", "libs/");
define("LANGS_DIR", "languages/");
define("MODELS_DIR", APP_DIR . "models/");
define("CONTROLLERS_DIR", APP_DIR . "controllers/");
define("VIEWS_DIR", APP_DIR . "views/");
define("LAYOUTS_DIR", VIEWS_DIR . "layouts/");
define("PAGES_DIR", VIEWS_DIR . "partials/");
define("AUDIT_LOGS_DIR", "logs/");

// File Upload Directories 
define("UPLOAD_DIR", "uploads/");
define("UPLOAD_FILE_DIR", UPLOAD_DIR . "files/");
define("UPLOAD_IMG_DIR", UPLOAD_DIR . "photos/");
define("MAX_UPLOAD_FILESIZE", trim(ini_get("upload_max_filesize")));

// First page to see after user login 
define("HOME_PAGE", "home");
define("DEFAULT_PAGE", "index"); //Default Controller Class
define("DEFAULT_PAGE_ACTION", "index"); //Default Controller Action
define("DEFAULT_LAYOUT", LAYOUTS_DIR . "main_layout.php");
define("DEFAULT_LANGUAGE", "english"); //Default Language

// Page Meta Information
define("META_AUTHOR", "");
define("META_DESCRIPTION", "");
define("META_KEYWORDS", "");
define("META_VIEWPORT", "width=device-width, initial-scale=1.0");
define("PAGE_CHARSET", "UTF-8");

// Email Configuration Default Settings
define("USE_SMTP", true);
define("SMTP_USERNAME", "katalontest258@gmail.com");  
define("SMTP_PASSWORD", "dohpqvtovfyiqyaf"); // App Password generado en Google
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", "587");
define("SMTP_SECURE", "tls"); // STARTTLS

// Default Email Sender Details
define("DEFAULT_EMAIL", SMTP_USERNAME);
define("DEFAULT_EMAIL_ACCOUNT_NAME", "MyClinicSystem - Notifications");

// Database Configuration Settings
define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "clinicdb");
define("DB_TYPE", "mysql");
define("DB_PORT", "");
define("DB_CHARSET", "utf8");

define("MAX_RECORD_COUNT", 20); //Default Max Records to Retrieve  per Page
define("ORDER_TYPE", "DESC");  //Default Order Type

// Active User Profile Details
define('USER_ID',(isset($_SESSION[APP_ID.'user_data']) ? $_SESSION[APP_ID.'user_data']['id_user'] : null ));
define('USER_NAME',(isset($_SESSION[APP_ID.'user_data']) ? $_SESSION[APP_ID.'user_data']['user_name'] : null ));
define('USER_EMAIL',(isset($_SESSION[APP_ID.'user_data']) ? $_SESSION[APP_ID.'user_data']['email'] : null ));
//define('USER_ROLE',(isset($_SESSION[APP_ID.'user_data']) ? $_SESSION[APP_ID.'user_data']['rol'] : null ));
// Constante con el ID del rol
define(
    'USER_ROLE_ID',
    (isset($_SESSION[APP_ID . 'user_data']) ? $_SESSION[APP_ID . 'user_data']['id_role'] : null)
);

// Constante con el nombre del rol (suponiendo que ya lo guardas en la sesión como 'role_name')
define(
    'USER_ROLE_NAME',
    (isset($_SESSION[APP_ID . 'user_data']) ? $_SESSION[APP_ID . 'user_data']['role_name'] : null)
);
// Si existe foto, será un BLOB convertido a Base64
define('USER_IMAGE', 
    (isset($_SESSION[APP_ID . 'user_data']['photo']) && !empty($_SESSION[APP_ID . 'user_data']['photo'])) 
    ? 'data:image/jpeg;base64,' . base64_encode($_SESSION[APP_ID . 'user_data']['photo']) 
    : null
);
