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
?>

<?php echo $publication_id == null ? '<h2 style="display: inline-block;">Добавление записи в блог</h2>' : "<h2>Редактирование записи</h2>"; ?>

<?php } ?>

<form>
	<?php echo $publication_id == null ? '' : "<input type='hidden' name='publication_id' value='{$publication_id}'>"; ?>

	<input type="hidden" name="section_id" value="<?=$section_id?>">
	<input type="hidden" name="page_id" value="<?=$page_id?>">

    <span style="width:8%;display: inline-block;">Заголовок:</span> <input type="text" name='title' style="width:90%" value="<?php echo $publication !== null ? $publication['title'] : ''; ?>">
    
    <table>
        <tr>
            <td style="width:22%">
                Заглавное фото
                <?php echoImageLoader('photo', $clsStorageImg->get($publication['image_storage_id'], 'origin'), '150px', '150px') ?>
            </td>
            <td style="width:79%">
                Описание: <br>
                <textarea name="descr" style="width:100%;" rows="5"><?php echo $publication !== null ? $publication['description'] : ''; ?></textarea>
            </td>
        </tr>
    </table>
    
    <?php show_editor($publication !== null ? $publication['text'] : '', __FILE__); ?>
    <!-- <textarea name="text" style="width:100%;" rows="15"><?php echo $publication !== null ? $publication['text'] : ''; ?></textarea>  -->
    <br>
    
    <input type="checkbox" name='is_active' <?php echo $publication_id == null ? 'checked' : ($publication['is_active'] == '1' ? 'checked' : ''); ?>> Отображать запись <br>

	<?php if ($publication_id == null) { ?>
    <table><tr>
        <td width="20%"><span style="text-align: right;">Защита от спама:</span></td>
        <td class="captcha" class='captcha'><?php call_captcha('image'); echo ' = '; call_captcha('input'); ?></td>
    </tr></table>
    <?php } ?>
    
    <br>
    
    <input type="button" value="<?php echo $publication_id == null ? 'Опубликовать' : 'Сохранить изменения'; ?>" onclick="this.form.content.innerHTML = get_from_ckEditor(this.form); sendform(this, 'edit', {url:WB_URL+'/modules/wbs_portal_obj_blog/api.php', wb_captcha_img:this.closest('form').querySelector('td.captcha img')})">
    
</form>