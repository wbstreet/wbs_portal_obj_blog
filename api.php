<?php

require_once(__DIR__.'/lib.class.portal_obj_blog.php');

$action = $_POST['action'];

$section_id = $clsFilter->f('section_id', [['integer', "Не указана секция!"]], 'fatal');
$page_id = $clsFilter->f('page_id', [['integer', "Не указана страница!"]], 'fatal');

require_once(WB_PATH."/framework/class.admin.php");
$admin = new admin('Start', '', false, false);
$clsModPortalObjBlog = new ModPortalObjBlog(null, null);

if ($action == 'edit') {
    
    check_auth(); //check_all_permission($page_id, ['pages_modify']);
    if ($publication_id !== null) {
        // прповеряем доступ к объекту
    }
    
    $publication_id = $clsFilter->f('publication_id', [['integer', '']], 'default', null);

    $fields = [
	    'title' => $clsFilter->f('title', [['1', 'Введите заголовок!'], ['mb_strCount', 'Разрешённая длина заголовка - 255 символов', 0,255]], 'append'),
	    'text' => $clsFilter->f('content', [['1', 'Введите текст!'], ['mb_strCount', 'Разрешённая длина текста - 65535 символов', 0,65535]], 'append'),
	    'description' => $clsFilter->f('descr', [['1', 'Напишите описание!'], ['mb_strCount', 'Разрешённая длина описания - 255 символов', 0,255]], 'append'),

        'is_active' => $clsFilter->f('is_active', [['variants', '', ['true', 'false']]], 'default', 'true'),
    ];
    $fields['is_active'] = $fields['is_active'] === 'true' ? 1 : 0;
    if ($publication_id === null) $clsFilter->f('captcha', [['1', "Введите Защитный код!"], ['variants', "Введите Защитный код!", [$_SESSION['captcha']]]], 'append', '');

    if ($clsFilter->is_error()) $clsFilter->print_error();

    $img_errs = '';
    if (isset($_FILES['photo']['tmp_name'])) {
        $id = $clsStorageImg->save($_FILES['photo']['tmp_name'], ['exts'=>['png', 'gif', 'jpeg', 'jpg']]);
        if (gettype($id) === 'string') $img_errs .= $id;
        else {$fields['image_storage_id'] = $id;}
    }

    if ($publication_id === null) {

        $fields = array_merge($fields, [
    	    'page_id'=>$page_id,
	        'section_id'=>$section_id,
	        'obj_type_id'=>$clsModPortalObjBlog->obj_type_id
	    ]);

        $fields['user_owner_id'] = $admin->get_user_id();
    	$publication_id = $clsModPortalObjBlog->add_publication($fields);
    	if (gettype($publication_id) === 'string') print_error($publication_id);
    	
	    print_success('Запись успешно добавлена! '.$img_errs, ['absent_fields'=>[]]);

    } else {
    	
    	$r = $clsModPortalObjBlog->update_publication($publication_id, $fields);
    	if ($r !== true) print_error($r);

	    print_success('Изменения сохранены! '.$img_errs);

    }

} else if ($action == 'delete_photo') {



} else { print_error('Неверный apin name!'); }

?>