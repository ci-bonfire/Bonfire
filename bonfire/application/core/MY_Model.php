<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: MY_Model
	
	The Base model implements standard CRUD functions that can be
	used and overriden by module models. This helps to maintain
	a standard interface to program to, and makes module creation
	faster.
	
	Author:
		Lonnie Ezell
 */
class MY_Model extends CI_Model {
	
	/*
		Var: $error
		Stores custom errors that can be used in UI error reporting.
	*/
	public $error 		= '';
	
	/*
		Var: $table
		The name of the db table this model primarily uses.
		
		Access:
			Protected
	*/
	protected $table 	= '';
	
	/*
		Var: $key
		The primary key of the table. Used as the 'id' throughout.
		
		Access:
			Protected
	*/
	protected $key		= 'id';
	
	/*
		Var: $set_created
		Whether or not to auto-fill a 'created_on' field on inserts.
		
		Access: 
			Protected
	*/
	protected $set_created	= TRUE;
	
	/*
		Var: $set_modified
		Whether or not to auto-fill a 'modified_on' field on updates.
		
		Access:
			Protected
	*/
	protected $set_modified = TRUE;
	
	/*
		Var: $date_format
		The type of date/time field used for created_on and modified_on fields. 
		Valid types are: 'int', 'datetime', 'date'
		
		Access: 
			protected
	*/
	protected $date_format = 'int';
	
	/*
		Var: $soft_deletes
		If false, the delete() method will perform a true delete of that row.
		If true, a 'deleted' field will be set to 1.
		
		Access:
			Protected
	*/
	protected $soft_deletes = FALSE;
	
	//---------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: find()
		
		Searches for a single row in the database.
		
		Parameter:
			$id		- The primary key of the record to search for.
			
		Return:
			An object representing the db row, or false.
	*/
	public function find($id='')
	{
		if ($this->_function_check($id) === FALSE)
		{
			return false;
		}
		
		$query = $this->db->get_where($this->table, array($this->table.'.'. $this->key => $id));
		
		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		
		return false;
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: find_all()
		
		Returns all records in the table. 
		
		By default, there is no 'where' clause, but you can filter
		the results that are returned by using either CodeIgniter's
		Active Record functions before calling this function, or 
		through method chaining with the where() method of this class.
		
		Return:
			An array of objects representing the results, or FALSE on failure or empty set.
	*/
	public function find_all()
	{
		if ($this->_function_check() === FALSE)
		{
			return false;
		}
		
		$this->db->from($this->table);
		
		$query = $this->db->get();
		
		if (!empty($query) && $query->num_rows() > 0)
		{
			return $query->result();
		}
		
		$this->error = 'Invalid selection.';
		return false;
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: find_all_by()
		
		A convenience method that combines a where() and find_all()
		call into a single call.
		
		Paremeters:
			$field	- The table field to search in.
			$value	- The value that field should be.
			
		Return:
			An array of objects representing the results, or FALSE on failure or empty set.
	*/
	public function find_all_by($field=null, $value='') 
	{		
		if (empty($field)) return false;

		// Setup our field/value check
		$this->db->where($field, $value);
		
		return $this->find_all();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: find_by()
		
		Returns the first result that matches the field/values passed.
		
		Parameters:
			$field	- Either a string or an array of fields to match against.
					  If an array is passed it, the $value parameter is ignored
					  since the array is expected to have key/value pairs in it.
			$value	- The value to match on the $field. Only used when $field is a string.
			$type	- The type of where clause to create. Either 'and' or 'or'. 
			
		Return: 
			An object representing the first result returned.
	*/
	public function find_by($field='', $value='', $type='and')
	{
		if (empty($field) || (!is_array($field) && empty($value)))
		{
			$this->error = 'Not enough information to find by.';
			return false;
		}
	
		if (is_array($field))
		{
			foreach ($field as $key => $value)
			{
				if ($type == 'or')
				{
					$this->db->or_where($key, $value);
				}
				else
				{
					$this->db->where($key, $value);
				}
			}
		}
		else
		{
			$this->db->where($field, $value);
		}
		
		$query = $this->db->get($this->table);
		
		if ($query && $query->num_rows() > 0)
		{
			return $query->result()->row();
		}
		
		return false;
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: insert()
		
		Inserts a row of data into the database.
		
		Parameters:
			$data	- an array of key/value pairs to insert.
			
		Return:
			Either the $id of the row inserted, or false on failure.
	*/
	public function insert($data=null)
	{
		if ($this->_function_check(FALSE, $data) === FALSE)
		{
			return FALSE;
		}
	
		// Add the created field
		if ($this->set_created === TRUE && !array_key_exists('created', $data))
		{
			$data['created_on'] = $this->set_date();
		}
		
		// Insert it
		$status = $this->db->insert($this->table, $data);
		
		if ($status != FALSE)
		{
			return $this->db->insert_id();
		} else
		{
			$this->error = mysql_error();
			return false;
		}

	}
	
	//---------------------------------------------------------------
	
	/*
		Method: update()
		
		Updates an existing row in the database.
		
		Parameters:
			$id		- The primary_key value of the row to update.
			$data	- An array of key/value pairs to update.
			
		Return:
			true/false
	*/
	public function update($id=null, $data=null)
	{
		
		if ($this->_function_check($id, $data) === FALSE)
		{
			return FALSE;
		}
		
		// Add the modified field
		if ($this->set_modified === TRUE && !array_key_exists('modified_on', $data))
		{
			$data['modified_on'] = $this->set_date();
		}
	
		$this->db->where($this->key, $id);
		if ($this->db->update($this->table, $data))
		{
			return true;
		}
	
		return false;
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: update_where()
		
		A convenience method that allows you to use any field/value pair
		as the 'where' portion of your update.
		
		Parameters:
			$field	- The field to match on.
			$value	- The value to search the $field for.
			$data	- An array of key/value pairs to update.
			
		Return:
			true/false
	*/
	public function update_where($field=null, $value=null, $data=null) 
	{
		if (empty($field) || empty($value) || !is_array($data))
		{
			$this->error = 'Not enough data.';
			return false;
		}
			
		return $this->db->update($this->table, $data, array($field => $value));
	}
	
	//--------------------------------------------------------------------
	
	
	/*
		Method: delete()
		
		Performs a delete on the record specified. If $this->soft_deletes is TRUE,
		it will attempt to set a field 'deleted' on the current record
		to '1', to allow the data to remain in the database.
		
		Parameters:
			$id		- The primary_key value to match against.
			
		Return:
			true/false
	 */
	public function delete($id=null)
	{
		if ($this->_function_check($id) === FALSE)
		{
			return FALSE;
		}
	
		if ($this->soft_deletes === TRUE)
		{
			$this->db->where($this->key, $id);
			$this->db->update($this->table, array('deleted' => 1));
		} 
		else 
		{
			$this->db->delete($this->table, array($this->key => $id));
		}

		$result = $this->db->affected_rows();

		if ($result)
		{
			return true;
		} 
		
		$this->error = 'DB Error: ' . mysql_error();
	
		return false;
	}
	
	//---------------------------------------------------------------
	
	//---------------------------------------------------------------
	// HELPER FUNCTIONS
	//---------------------------------------------------------------
	
	/*
		Method: is_unique()
		
		Checks whether a field/value pair exists within the table.
		
		Parameters:
			$field	- The field to search for.
			$value	- The value to match $field against.
			
		Return:
			true/false
	*/
	public function is_unique($field='', $value='')
	{
		if (empty($field) || empty($value))
		{
			$this->error = 'Not enough information to check uniqueness.';
			return false;
		}

		$this->db->where($field, $value);			
		$query = $this->db->get($this->table);
					
		if ($query && $query->num_rows() == 0)
		{
			return true;
		}
		
		return false;
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: count_all()
		
		Returns the number of rows in the table.
		
		Return:
			int
	*/
	public function count_all()
	{
		return $this->db->count_all($this->table);
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: count_by()
		
		Returns the number of elements that match the field/value pair.
		
		Parameters:
			$field	- The field to search for.
			$value	- The value to match $field against.
			
		Return:
			int
	*/
	public function count_by($field='', $value='') 
	{
		if (empty($field) || empty($value))
		{
			$this->error = 'Not enough information to count results.';
			return false;
		}
		
		$this->db->where($field, $value);
		
		return $this->db->count_all_results($this->table);
	}
	
	//---------------------------------------------------------------
	
	/*
		Method: get_field()
		
		A convenience method to return only a single field of the specified row.
		
		Parameters:
			$field	- The field to search for.
			$value	- The value to match $field against.
			
		Return:
			An object with the field value in it.
	*/
	public function get_field($id=null, $field='') 
	{
		if (!is_numeric($id) || $id === 0 || empty($field))
		{
			$this->error = 'Not enough information to fetch field.';
			return false;
		}
		
		$this->db->select($field);
		$this->db->where($this->key, $id);
		$query = $this->db->get($this->table);
		
		if ($query && $query->num_rows() > 0)
		{
			return $query->row();
		}
		
		return false;
	}
	
	//---------------------------------------------------------------
	
	//--------------------------------------------------------------------
	// !CHAINABLE UTILITY METHODS
	//--------------------------------------------------------------------
	
	/*
		Method: where()
		
		Sets the where portion of the query in a chainable format.
		
		Parameters:
			$field	- The field to search the db on.
			$value	- The value to match the field against.
			
		Return:
			An instance of this class.
	*/
	public function where($field=null, $value=null) 
	{
		if (!empty($field) && !empty($value))
		{
			$this->db->where($field, $value);
		}
		
		return $this;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: select()
		
		Sets the select portion of the query in a chainable format.
		
		Parameters:
			$selects	- A string representing the selection.
			
		Return:
			An instance of this class.
	*/
	public function select($selects=null) 
	{
		if (!empty($selects))
		{		
			$this->db->select($selects);
		}
		
		return $this;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: limit()
		
		Sets the limit portion of the query in a chainable format.
		
		Parameters:
			$limit	- An int showing the max results to return.
			$offset	- An in showing how far into the results to start returning info.
			
		Return:
			An instance of this class.
	*/
	public function limit($limit=0, $offset=0) 
	{
		$this->db->limit($limit, $offset);
		
		return $this;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: order_by()
		
		Inserts a chainable order_by method from either a string or an
		array of field/order combinations. If the $field value is an array,
		it should look like:
		 
		 array(
		 	'field1' => 'asc',
		 	'field2' => 'desc'
		 );
		 
		 Parameters:
		 	$field	- The field to order the results by.
		 	$order	- Which direction to order the results ('asc' or 'desc')
		 	
		 Return:
		 	An instance of this class.
	 */
	public function order_by($field=null, $order='asc') 
	{
		if (!empty($field))
		{
			if (is_string($field))
			{
				$this->db->order_by($field, $order);
			}
			else if (is_array($order_by))
			{
				foreach ($field as $f => $o)
				{
					$this->db->order_by($f, $o);
				}
			}
		}
		
		return $this;
	}
	
	//--------------------------------------------------------------------
	
	//---------------------------------------------------------------
	// !UTILITY FUNCTIONS
	//---------------------------------------------------------------
	
	/*
		Method: _function_check()
		
		A utility method that does some error checking and cleanup for other methods:
		
		- Makes sure that a table has been set at $this->table.
		- If passed in, will make sure that $id is of the valid type.
		- If passed in, will verify the $data is not empty.
	*/
	protected function _function_check($id=FALSE, &$data=FALSE) 
	{
		// Does the model have a table set?
		if (empty($this->table))
		{
			$this->error = 'Model has unspecified database table.';
			return false;
		}
		
		// Check the ID, but only if it's a non-FALSE value
		if ($id !== FALSE)
		{
			if (!is_numeric($id) || $id == 0)
			{
				$this->error = 'Invalid ID passed to model.';
				return false;
			}
		}
		
		// Check the data
		if ($data !== FALSE)
		{
			if (!is_array($data) || count($data) == 0)
			{
				$this->error = 'No data available to insert.';
				return false;
			}
		}
		
		// Strip the 'submit' field, if set
		if (isset($data['submit']))
		{
			unset($data['submit']);
		}
		// Strip the 'func' field, if set
		if (isset($data['func']))
		{
			unset($data['func']);
		}
		
		return true;
	}
    
    //---------------------------------------------------------------
	
	/*
		Method: set_date()
		
		A utility function to allow child models to use the type of
		date/time format that they prefer. This is primarily used for
		setting created_on and modified_on values, but can be used by
		inheriting classes.
		
		The available time formats are:
			'int'		- Stores the date as an integer timestamp.
			'datetime'	- Stores the date and time in the SQL datetime format.
			'date'		- Stores teh date (only) in the SQL date format.
			
		Parameters:
			$user_date	- An optional PHP timestamp to be converted.
			
		Return:
			The current/user time converted to the proper format.
	*/
	protected function set_date($user_date=null) 
	{
		$curr_date = !empty($user_date) ? $user_date : time();
		
		switch ($this->date_format)
		{
			case 'int':
				return $curr_date;
				break;
			case 'datetime':
				return date('Y-m-d H:i:s', $curr_date);
				break;
			case 'date':
				return date( 'Y-m-d', $curr_date);
				break;
		}
	}
	
	//--------------------------------------------------------------------
	
} 

// END: Class MY_model

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */


