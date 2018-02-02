<?php
/**
 *
 * @category        module
 * @package         wbs_portal_obj_blog
 * @author          Konstantin Polyakov
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.10.0
 * @requirements    PHP 7.0 and higher
 *
 */

if(!defined('WB_PATH')) {
        require_once(dirname(dirname(__FILE__)).'/framework/globalExceptionHandler.php');
        throw new IllegalFileException();
}
/* -------------------------------------------------------- */

if (!file_exists(WB_PATH.'/modules/wbs_portal/lib.class.portal.php')) $admin->print_error('Модуль wbs_portal_obj_blog требует модуль wbs_portal');

// create tables from sql dump file
$filename = __DIR__.'/install-struct.sql';
if (file_exists($filename) && is_readable($filename)) {
    $r = $database->SqlImport($filename, TABLE_PREFIX, __FILE__ );
    if ($database->is_error()) {
        $admin->print_error($database->get_error());
    }
}

$name = substr(basename(__DIR__), 4);
$class_name = "Mod".ucfirst(str_replace('_', '', ucwords($name, '_'))); // portal_obj_blog int ModPortalObjBlog
/*foreach ($name as $i => $letter) {
    if ($letter === '_') { $upper = true; continue;}
    else { $upper = false; }
    if ($upper) $letter = strtoupper($letter);
    $class_name .= $letter;
}
$class_name = "Mod".ucfirst($class_name);*/

include(__DIR__."/lib.class.$name.php");
$GLOBALS['cls'.$class_name] = new $class_name(null, null);
$r = $GLOBALS['cls'.$class_name]->install();
if ($r !== true) {
    $admin->print_error($r);
}
?> 
