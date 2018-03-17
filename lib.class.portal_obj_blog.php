<?php

$path_core = __DIR__.'/../wbs_portal/lib.class.portal.php';
if (file_exists($path_core )) include($path_core );
else echo "<script>console.log('Модуль wbs_portal_obj_estate требует модуль wbs_portal')</script>";

if (!class_exists('ModPortalObjBlog')) { 
class ModPortalObjBlog extends ModPortalObj {

    function __construct($page_id, $section_id) {
        parent::__construct('blog', 'Блог', $page_id, $section_id);
        $this->tbl_blog = "`".TABLE_PREFIX."mod_{$this->prefix}blog_publication`";
        $this->tbl_hashtag = "`".TABLE_PREFIX."mod_{$this->prefix}blog_hashtag`";
        $this->tbl_hashtag_publication = "`".TABLE_PREFIX."mod_{$this->prefix}blog_hashtag_publication`";
        //$this->clsStorageImg = new WbsStorageImg();
    }

    function uninstall() {
        global $database;
        
        // проверяем наличие объектов

        /*$r = select_row($this->tbl_apartment, 'COUNT(`obj_id`) as ocount');
        if ($r === false) return "Неизвестная ошибка!";
        if ($r->fetchRow()['ocount'] > 0) return "Существуют объекты!";*/
        
        // проверяем, наличие партнёров

        /*$r = select_row($this->tbl_partner, 'COUNT(`partner_id`) as pcount');
        if ($r === false) return "Неизвестная ошибка!";
        if ($r->fetchRow()['pcount'] > 0) return "Существуют партнёры!";*/

        // проверяем, наличие категорий

        /*$r = select_row($this->tbl_category, 'COUNT(`category_id`) as ccount');
        if ($r === false) return "Неизвестная ошибка!";
        if ($r->fetchRow()['ccount'] > 0) return "Существуют категории!";*/

        // удаляем модуль

        $arr = [/*"DROP TABLE ".$this->tbl_apartment,
                "DROP TABLE ".$this->tbl_category,
                "DROP TABLE ".$this->tbl_partner,*/
        ];

        $r = parent::uninstall($arr);
        if ($r === false) return "Неизвестная ошибка!";
        if ($r !== true) return $r;
        
        return true;
        
    }
    
    function install() {
        return parent::install();
    }
    
    function add_publication($fields) {
		global $database;

        $_fields = $this->split_arrays($fields);

		$r = insert_row($this->tbl_obj_settings, $_fields);
		if ($r !== true) return "Неизвестная ошибка";

		$blog_id = $database->getLastInsertId();

        $fields['obj_id'] = $blog_id;
		$r = insert_row($this->tbl_blog, $fields);
		if ($r !== true) return "Неизвестная ошибка";

		return $blog_id;
	}

    function update_publication($publication_id, $fields) {
		global $database;

        $_fields = $this->split_arrays($fields);

		$r = $this->get_obj(['obj_id'=>$publication_id]);
		if (gettype($r) === 'string') return $r;
		if ($r === null) return 'Запись не найдена (id: '.$database->escapeString($publication_id).')';


        if ($_fields) {
        	$r = update_row($this->tbl_obj_settings, $_fields, glue_fields(['obj_id'=>$publication_id], 'AND'));
	    	if ($r !== true) return $r;
        }

        if ($fields) {
        	$r = update_row($this->tbl_blog, $fields, glue_fields(['obj_id'=>$publication_id], 'AND'));
	    	if ($r !== true) return 'Неизвестная ошибка';
        }
        
        return true;
    }

    /*function get_obj($sets=[], $only_count=false) {
        global $database;

        $where = [
            "{$this->tbl_blog}.`obj_id`={$this->tbl_obj_settings}.`obj_id`",
            "{$this->tbl_obj_settings}.`obj_type_id`=".process_value($this->obj_type_id),
        ];
        $this->_getobj_where($sets, $where);

        $find_keys = ['title'=>"{$this->tbl_blog}.`title`", 'text'=>"{$this->tbl_blog}.`text`"];
        $where_find = getobj_search($sets, $find_keys);
        if ($where_find) $where[] = $where_find;

        //$where[] = "{$this->tbl_apartment}.`obj_id`={$this->tbl_obj_settings}.`obj_id` AND {$this->tbl_obj_settings}.`obj_type_id`={$this->tbl_obj_type}.`obj_type_id` AND {$this->tbl_obj_type}.`obj_type_latname`=".process_value($this->obj_type_latname);
        $where = implode(' AND ', $where);
        $select = $only_count ? "COUNT({$this->tbl_blog}.obj_id) AS count" : "*";
        $order_limit = getobj_order_limit($sets);

        $sql = "SELECT $select FROM {$this->tbl_blog}, {$this->tbl_obj_settings} WHERE $where $order_limit";

        //echo "<script>console.log(`".htmlentities($sql)."`);</script>";

        return getobj_return($sql, $only_count);
    }*/

    function get_obj($sets=[], $only_count=false) {

        $tables = [$this->tbl_blog, $this->tbl_obj_settings];

        $where = [
            "{$this->tbl_blog}.`obj_id`={$this->tbl_obj_settings}.`obj_id`",
            "{$this->tbl_obj_settings}.`obj_type_id`=".process_value($this->obj_type_id),
        ];
        $this->_getobj_where($sets, $where);
        
        $where_opts = [];
        
        $where_find = ['title'=>"{$this->tbl_blog}.`title`", 'text'=>"{$this->tbl_blog}.`text`"];
        
        return get_obj($tables, $where, $where_opts, $where_find, $sets, $only_count);

    }
    
}
}