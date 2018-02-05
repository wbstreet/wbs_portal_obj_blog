<?php


include(__DIR__.'/../lib.class.portal_obj_blog.php');
$clsModPortalObjBlog = new ModPortalObjBlog($page_id, $section_id);

if ($admin->is_authenticated()) {$is_auth = true;}
else { $is_auth = false; }

?>

<?php

if ($modPortalArgs['obj_id'] === null) {

    $modPortalArgs['obj_owner'] = $clsFilter->f2($_GET, 'obj_owner', [['1', '']], 'default', 'all');

    $common_opts = [
    	//'settlement_id'=>get_current_settlement(),
    	'is_active'=>1,
    	//'is_moder'=>1,
    	'is_deleted'=>0,
    	];
    
    if ($modPortalArgs['obj_owner'] === 'my') {
        $common_opts['owner_id'] = ['value'=>$admin->get_user_id()];
        unset($common_opts['is_active']);
    } else if (ctype_digit($modPortalArgs['obj_owner'])) {
        $common_opts['owner_id'] = ['value'=>$modPortalArgs['obj_owner']];
    }
    
    	
    $opts = array_merge($common_opts, [
    	'find_str'=>$modPortalArgs['s'],
    	'limit_count'=>$modPortalArgs['obj_per_page'],
    	'limit_offset'=>$modPortalArgs['obj_per_page'] * ($modPortalArgs['page_num']-1),
        'order_by'=>[$clsModPortalObjBlog->tbl_blog.'.`obj_id`'],
        'order_dir'=>'DESC',
    ]);
    $publications = $clsModPortalObjBlog->get_publication($opts);
    if (gettype($publications) == 'string') $clsModPortalObjBlog->print_error($publications);
    
    
    $objs = [];
    $page_link = page_link($wb->link);
    while (gettype($publications) !== 'string' && $publications !== null && $publication = $publications->fetchRow(MYSQLI_ASSOC)) {
        $publication['orig_image'] = ''; $publication['preview_image'] ='';
        if ($publication['image_storage_id'] !== null) {
            $publication['orig_image'] = $clsStorageImg->get($publication['image_storage_id'], 'origin');
            $publication['preview_image'] = $clsStorageImg->get($publication['image_storage_id'], '350x250');
        }
    
        $publication['publication_url'] = $page_link.'?obj_id='.$publication['obj_id'];
        $publication['publication_from_url'] = $page_link.'?obj_owner='.$publication['user_owner_id'];
        $publication['show_panel_edit'] = true;
        $publication['user'] = $admin->get_user_details($publication['user_owner_id']);
        $objs[] = $publication;
    }

    $loader = new Twig_Loader_Array(array(
        'view' => file_get_contents(__DIR__.'/view.html'),
    ));
    $twig = new Twig_Environment($loader);
        
    echo $twig->render('view', [
        'is_auth'=>$is_auth,
        'objs'=>$objs,
    ]);

} else {
    
    $opts = [
    	'obj_id'=>$modPortalArgs['obj_id'],
    	];
    $publications = $clsModPortalObjBlog->get_publication($opts);
    if (gettype($publications) == 'string') echo $publications;
    else if ($publications->numRows() === 0) echo "Публикация не найдена";
    else {
        $publication = $publications->fetchRow();
        
        if ($publication['is_active'] == '0' && $publication['user_owner_id'] !== $admin->get_user_id()) {
            echo "Публикация недоступна по воле автора.";
        } else {

            $user = $admin->get_user_details($publication['user_owner_id']);
            
            echo "<h2>", $publication['title'], "</h2>";

            echo "<div style=\"width:100%;text-align:right\">",
                "<a style='cursor:pointer;font-size:10pt;' onclick=\"set_params({obj_id:null,obj_owner:'{$obj['user_owner_id']}'});\">{$user['username']}</a>",
                $publication['is_active'] == '0' ? "<br><span class='attention_block'>Публикация неактивна</span>" : "",
            "</div>";
            
            echo "<br>";
            echo $publication['text'];
        }
        
    }
    
}

?>