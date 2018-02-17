<?php

$path_core = __DIR__.'/../wbs_portal/lib.class.portal.php';
if (file_exists($path_core )) include($path_core );
else echo "<script>console.log('Модуль wbs_portal_obj_estate требует модуль wbs_portal')</script>";

// используется только в данном файле. Пока неизвестно, включать её в sql_tools.php или нет.
if (!function_exists('guess_operator')) {
function guess_operator($value, $inverse=false) {
	if ($value === 'NULL') {
		if ($inverse) return ' is not ';
		else {return ' is ';}
	} else {
		if ($inverse) return '!=';
		else {return '=';}
	}
}
}

if (!class_exists('ModPortalObjBlog')) { 
class ModPortalObjBlog extends ModPortalObj {

    function __construct($page_id, $section_id) {
        parent::__construct('blog', 'Блог', $page_id, $section_id);
        $this->tbl_blog = "`".TABLE_PREFIX."mod_{$this->prefix}blog_publication`";
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

    function get_obj($sets=[], $only_count=false) {
        global $database;

        $where = [
            "{$this->tbl_blog}.`obj_id`={$this->tbl_obj_settings}.`obj_id`",
            "{$this->tbl_obj_settings}.`obj_type_id`=".process_value($this->obj_type_id),
        ];
        $this->_getobj_where($sets, $where);

        //if (isset($sets['owner_id'])) $where[] = "{$this->tbl_apartment}.`owner_id`=".process_value($sets['owner_id']);
        //if (isset($sets['partner_id'])) $where[] = "{$this->tbl_apartment}.`partner_id`=";
                
        if (isset($sets['owner_id'])) {
            $w = "{$this->tbl_obj_settings}.`user_owner_id`";
            $value = process_value($sets['owner_id']);
            //if ($value === 'NULL') $where[] = $w.' is '.$value;
            //else $where[] = $w.'='.$value;
            $where[] = $w.guess_operator($value).$value;
        }

        if (isset($sets['find_str'])) $find_str = $database->escapeString($sets['find_str']); else $find_str = null;
        if ( $find_str !== null ) {
            $find_str = str_replace('%', '\%', $find_str);
            $where[] = "{$this->tbl_blog}.`name` LIKE '%$find_str%'";
        }

        //$where[] = "{$this->tbl_apartment}.`obj_id`={$this->tbl_obj_settings}.`obj_id` AND {$this->tbl_obj_settings}.`obj_type_id`={$this->tbl_obj_type}.`obj_type_id` AND {$this->tbl_obj_type}.`obj_type_latname`=".process_value($this->obj_type_latname);
        $where = implode(' AND ', $where);
        $select = $only_count ? "COUNT({$this->tbl_blog}.obj_id) AS count" : "*";
        $order_limit = $this->_getobj_order_limit($sets);

        $sql = "SELECT $select FROM {$this->tbl_blog}, {$this->tbl_obj_settings} WHERE $where $order_limit";

        //echo "<script>console.log(`".htmlentities($sql)."`);</script>";

        return $this->_getobj_return($sql, $only_count);
    }
    
}
}