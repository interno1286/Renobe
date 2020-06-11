<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class defaultModel extends ormModel {

    public $schema = 'public';
    public $table = 'sites';

	function getList() {

		$sql = "
			select
				*
			from
				list
		";

		return $this->s_fetchCol($sql);
	}

}
