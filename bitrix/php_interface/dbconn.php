<?
define("DBPersistent", false);
$DBType = "mysql";
$DBHost = getenv('DBHost');
$DBLogin = getenv('DBLogin');
$DBPassword = getenv('DBPassword');
$DBName = getenv('DBName');
$DBDebug = false;
$DBDebugToFile = false;
date_default_timezone_set("Etc/GMT-3");

define("DELAY_DB_CONNECT", true);
define("CACHED_b_file", 3600);
define("CACHED_b_file_bucket_size", 10);
define("CACHED_b_lang", 3600);
define("CACHED_b_option", 3600);
define("CACHED_b_lang_domain", 3600);
define("CACHED_b_site_template", 3600);
define("CACHED_b_event", 3600);
define("CACHED_b_agent", 3660);
define("CACHED_menu", 3600);

define("BX_FILE_PERMISSIONS", 0644);
define("BX_DIR_PERMISSIONS", 0755);
@umask(~(BX_FILE_PERMISSIONS|BX_DIR_PERMISSIONS)&0777);

define("MYSQL_TABLE_TYPE", "INNODB");
define("SHORT_INSTALL", true);
define("VM_INSTALL", true);

define("BX_UTF", true);
define("BX_CRONTAB_SUPPORT", false);
define("BX_CRONTAB", false);
define("BX_COMPRESSION_DISABLED", true);

define("BX_DISABLE_INDEX_PAGE", true);

define("BX_USE_MYSQLI", true);
?>
