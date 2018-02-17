<?php
if ($admin->is_authenticated()) {

$publication_id = $modPortalArgs['obj_id'];

include(__DIR__.'/../lib.class.portal_obj_blog.php');
$clsModPortalObjBlog = new ModPortalObjBlog($page_id, $section_id);

if ($admin->is_authenticated()) {$is_auth = true;}
else { $is_auth = false; }

$publication = null;
if ($publication_id !== null) {
    $r = $clsModPortalObjBlog->get_obj(['obj_id'=>$publication_id]);
    if (gettype($r) == 'string') $clsModPortalObjBlog->print_error($r);
	else if ($r === null) $clsModPortalObjBlog->print_error('Запись не найдена');
    else $publication = $r->fetchRow();
}

ob_start();
show_editor($publication === null ? '' : $publication['text'], __FILE__);
$editor = ob_get_contents();
ob_end_clean();

$clsModPortalObjBlog->render('edit.html',[
    'is_auth'=>$is_auth,
    'obj'=>$publication,
    'section_id'=>$section_id,
    'page_id'=>$page_id,
    'editor'=>$editor,
    'image_loader'=> echoImageLoader('photo', $clsStorageImg->get($publication === null ? null : $publication['image_storage_id'], 'origin'), '150px', '150px', true),
    'spo'=>$publication === null ? '' : "page_id: '{$page_id}',section_id:'{$section_id}',obj_id:'{$publication['obj_id']}'",
]);

} ?>