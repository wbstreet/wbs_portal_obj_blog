<?php


include(__DIR__.'/../lib.class.portal_obj_blog.php');
$clsModPortalObjBlog = new ModPortalObjBlog($page_id, $section_id);

if ($admin->is_authenticated()) {$is_auth = true;}
else { $is_auth = false; }

$modPortalArgs['obj_owner'] = $clsFilter->f2($_GET, 'obj_owner', [['1', '']], 'default', 'all');

?>

<?php if ($is_auth) { ?>
    <a href="?action=edit"><input type="button" value="Написать в блог"></a> <input type="button" value="Мои записи" onclick="set_params({obj_owner:'my'});">
<?php } ?>

<?php
$common_opts = [
	//'settlement_id'=>get_current_settlement(),
	'category_id'=>$modPortalArgs['category_id'],
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
	]);
$publications = $clsModPortalObjBlog->get_publication($opts);
if (gettype($publications) == 'string') $clsModPortalObjBlog->print_error($publications);


$text = "<div style='text-align:center;'>";
$x = 0;
while ($publications !== null && $publication = $publications->fetchRow(MYSQLI_ASSOC)) {
    $orig_image = ''; $preview_image ='';
    if ($publication['image_storage_id'] !== null) {
        $orig_image = $clsStorageImg->get($publication['image_storage_id'], 'origin');
        $preview_image = $clsStorageImg->get($publication['image_storage_id'], '350x250');
    }

    $panel_edit = "<div class='obj_panel_edit'>
    <span onclick=\"set_params({action:'edit', obj_id:'{$publication['obj_id']}'})\">Изменить</span>
    </div>
    ";
    
    $active_class = $publication['is_active'] === '0' ? ' obj_nonactive' : '';
    
    $user = $admin->get_user_details($publication['user_owner_id']);

	$text .= "
	<div class='publication {$active_class}'>
        <a href='{$orig_image}' class='fm'>
            <img class='publication_image' src='{$preview_image}' align='left'>
        </a>
        <div class='block_info'>
		    <span style=\"font-size:14pt;\">{$publication['title']}</span>
		    <br><a style='cursor:pointer;float:right;font-size:10pt;' onclick=\"set_params({obj_owner:'{$publication['user_owner_id']}'});\">{$user['username']}</a>
            <br>
    	    <div style='font-size:11pt;'>
	            {$publication['description']}
	    	</div>
	    </div>
	    $panel_edit
	</div>
	";
	$x += 1;
}
$text .= "</div>";

echo $text;

?>

<style>
    .publication {
    	width: 100%;
    	font-size:12pt;
    	border: 1px solid #FFC68E;
    	background: #FFF8D4;
    	overflow:auto;
   	    margin-top: 10px;
   	    vertical-align: top;
   	    text-align:left;
   	    padding:5px;
   	    position:relative;
    }

    .publication .fm {
    	width:40%;
    	max-height: 150px;
    	overflow:hidden;
   	    display: inline-block;
   	    border-radius:5px;
    }

    .publication .fm .publication_image {
    	width: 100%;
    	margin:0;
    }
    
    .publication .block_info {
    	display:inline-block;
    	vertical-align:top;
    	width: 58%;
    }
    
    .publication .obj_panel_edit {
        position:absolute;
        bottom: 0;
        right: 0;
        background-color: #aaaaaaaa;
    }
    .publication .obj_panel_edit span {
        display:inline-block;
        cursor:pointer;
    }
    .publication .obj_panel_edit span:hover {
        background-color: #bbbbbbaa;
    }
    
    .publication.obj_nonactive {
        opacity: 0.5;
    }

    @media screen and (max-width: 420px) {
        .publication .fm {
        	width:100%;
        }
	    .publication .block_info {
	    	width: 100%;
	    }
    }
</style>