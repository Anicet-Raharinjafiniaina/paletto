<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);


define('AES_128_ECB', 'aes-128-ecb');
define('AES_128_CBC', 'aes-128-cbc');
define('ENCRYPTION_KEY', 'Pa13p3rman3n!32022');

defined('URL_FILE') || define('URL_FILE', WRITEPATH . 'doc/');

/** Tables */
defined('TBL_UTILISATEUR') || define('TBL_UTILISATEUR', 'utilisateur');
defined('TBL_PROFIL') || define('TBL_PROFIL', 'profil');
defined('TBL_ACCES') || define('TBL_ACCES', 'acces');
defined('TBL_HISTORIQUE') || define('TBL_HISTORIQUE', 'historique');
defined('TBL_PAGE') || define('TBL_PAGE', 'page');
defined('TBL_ENTREPOT') || define('TBL_ENTREPOT', 'entrepot');
defined('TBL_ALLEE') || define('TBL_ALLEE', 'allee');
defined('TBL_RANGEE') || define('TBL_RANGEE', 'rangee');
defined('TBL_NIVEAU') || define('TBL_NIVEAU', 'niveau');
defined('TBL_CAGE') || define('TBL_CAGE', 'cage');
defined('TBL_EMPLACEMENT') || define('TBL_EMPLACEMENT', 'emplacement');
defined('TBL_EMPLACEMENT_ADRESSE') || define('TBL_EMPLACEMENT_ADRESSE', 'emplacement_adresse');
defined('TBL_EMPLACEMENT_STATUT') || define('TBL_EMPLACEMENT_STATUT', 'emplacement_statut');
defined('TBL_PALETTE') || define('TBL_PALETTE', 'palette');
defined('TBL_PALETTE_STATUT') || define('TBL_PALETTE_STATUT', 'palette_statut');
defined('TBL_ARTICLE') || define('TBL_ARTICLE', 'article');
defined('TBL_MOUVEMENT') || define('TBL_MOUVEMENT', 'mouvement');
defined('TBL_MOUVEMENT_TYPE') || define('TBL_MOUVEMENT_TYPE', 'mouvement_type');
defined('TBL_ARTICLE_HORS_X3') || define('TBL_ARTICLE_HORS_X3', 'article_hors_x3');
defined('TBL_ACTION') || define('TBL_ACTION', 'action');
/** Tables */

/* View */
defined('VIEW_EMPLACEMENT_ADRESSE') || define('VIEW_EMPLACEMENT_ADRESSE', 'emplacement_adresse_view');
/** /View */

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code
