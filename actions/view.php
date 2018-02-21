<?php


include(__DIR__.'/../lib.class.portal_obj_blog.php');
$clsModPortalObjBlog = new ModPortalObjBlog($page_id, $section_id);

if ($admin->is_authenticated()) {$is_auth = true;}
else { $is_auth = false; }

?>

<?php

if ($modPortalArgs['obj_id'] === null) {

    $modPortalArgs['obj_owner'] = $clsFilter->f2($_GET, 'obj_owner', [['1', '']], 'default', 'all');

    $fields = [
        //'settlement_id'=>get_current_settlement(),
        'is_active'=>1,
        //'is_moder'=>1,
        'is_deleted'=>0,
        'section_id'=>$section_id,
        'page_id'=>$page_id,

        'find_str'=>$modPortalArgs['s'],
        //'limit_count'=>$modPortalArgs['obj_per_page'],
        //'limit_offset'=>$modPortalArgs['obj_per_page'] * ($modPortalArgs['page_num']-1),
        'order_by'=>[$clsModPortalObjBlog->tbl_blog.'.`obj_id`'],
        'order_dir'=>'DESC',
        ];
    
    if ($modPortalArgs['obj_owner'] === 'my') {
        $fields['owner_id'] = ['value'=>$admin->get_user_id()];
        unset($common_opts['is_active']);
    } else if (ctype_digit($modPortalArgs['obj_owner'])) {
        $fields['owner_id'] = ['value'=>$modPortalArgs['obj_owner']];
    }
    
    $obj_total = $clsModPortalObjBlog->get_obj($fields, true);
    if (gettype($obj_total) == 'string') $clsModPortalObjBlog->print_error($obj_total);

    $divs = calc_paginator_and_limit($modPortalArgs, $fields, $obj_total);

    $publications = $clsModPortalObjBlog->get_obj($fields);
    if (gettype($publications) == 'string') $clsModPortalObjBlog->print_error($publications);

    $objs = [];
    $page_link = page_link($wb->link);
    while (gettype($publications) !== 'string' && $publications !== null && $publication = $publications->fetchRow(MYSQLI_ASSOC)) {
        $publication['orig_image'] = $clsStorageImg->get($publication['image_storage_id'], 'origin');
        $publication['preview_image'] = $clsStorageImg->get($publication['image_storage_id'], '350x250');

        $publication['obj_url'] = $page_link.'?obj_id='.$publication['obj_id'];
        $publication['objs_from_url'] = $page_link.'?obj_owner='.$publication['user_owner_id'];
        $publication['show_panel_edit'] = $is_auth && $publication['user_owner_id'] === $admin->get_user_id() ? true : false;
        $publication['user'] = $admin->get_user_details($publication['user_owner_id']);
        $objs[] = $publication;
    }


    $clsModPortalObjBlog->render('view_list.html',[
        'is_auth'=>$is_auth,
        'objs'=>$objs,
        'page'=>$wb,
        'divs'=>$divs,
        'page_link'=>$page_link,
        'modPortalArgs'=>$modPortalArgs,
    ]);

} else {
    
    $opts = [
        'obj_id'=>$modPortalArgs['obj_id'],
        ];
    $publications = $clsModPortalObjBlog->get_obj($opts);
    if (gettype($publications) == 'string') echo $publications;
    else if ($publications->numRows() === 0) echo "Публикация не найдена";
    else {
        $publication = $publications->fetchRow();
        
        $can_edit = $is_auth && $admin->get_user_id() === $publication['user_owner_id'] ? true : false;
        
        if ($publication['is_active'] == '0' && !$can_edit) {
            echo "Публикация недоступна по воле автора.";
        } else {

            $clsModPortalObjBlog->render('view.html',[
                'is_auth'=>$is_auth,
                'obj'=>$publication,
                'user'=>$admin->get_user_details($publication['user_owner_id']),
                'can_edit'=>$can_edit,
            ]);
            
            share_page_link();
        }
        
    }
    
}

?>