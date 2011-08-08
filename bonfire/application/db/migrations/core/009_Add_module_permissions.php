<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_module_permissions extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;
				
		$this->db->query("INSERT INTO {$prefix}permissions(name, description) VALUES('Bonfire.Modules.Add','Allow creation of modules with the builder.')");
		$this->db->query("INSERT INTO {$prefix}permissions(name, description) VALUES('Bonfire.Modules.Delete','Allow deletion of modules.')");
		
	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;
		
		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Bonfire.Modules.Add' OR name = 'Bonfire.Modules.Delete'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
				
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Bonfire.Modules.Add')");
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Bonfire.Modules.Delete')");
	}
	
	//--------------------------------------------------------------------
	
}