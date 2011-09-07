<?php

 // There are no doubt more types where a value/length isn't possible - needs investigating
$no_length = array('TEXT', 'DATE', 'DATETIME', 'BLOB', 'TINYBLOB', 'TINYTEXT', 'MEDIUMBLOB', 'MEDIUMTEXT', 'LONGBLOB', 'LONGTEXT');

$db_migration = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

class Migration_Install_'.$table_name.' extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->add_field(\'`'.$primary_key_field.'` int(11) NOT NULL AUTO_INCREMENT\');';
		for($counter=1; $field_total >= $counter; $counter++)
		{
			//Due to the requiredif rule if the first field is set the the others must be
			if (set_value("view_field_label$counter") == NULL)
			{
				continue; 	// move onto next iteration of the loop
			}

			$db_migration .= '
			$this->dbforge->add_field("`'.$module_name_lower.'_'.strtolower(set_value("view_field_name$counter")).'` '.addcslashes(set_value("db_field_type$counter"),'"');
		
			if (!in_array(set_value("db_field_type$counter"), $no_length))
			{
				$db_migration .= '('.addcslashes(set_value("db_field_length_value$counter"),'"').')';
			}
		

		$db_migration .= ' NOT NULL");';
		
		}
		$db_migration .= '
		$this->dbforge->add_key(\''.$primary_key_field.'\', true);
		$this->dbforge->create_table(\''.$table_name.'\');

	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->drop_table(\''.$table_name.'\');

	}
	
	//--------------------------------------------------------------------
	
}';

echo $db_migration;
?>
