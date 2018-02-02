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

$name = substr(basename(__DIR__), 4);
$class_name = "Mod".ucfirst(str_replace('_', '', ucwords($name, '_'))); // portal_obj_blog int ModPortalObjBlog

include(__DIR__."/lib.class.$name.php");
$GLOBALS['cls'.$class_name] = new $class_name(null, null);
$r = $GLOBALS['cls'.$class_name]->uninstall();
if ($r !== true) {
    $admin->print_error($r);
}

?> 
