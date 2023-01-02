<?php
class db
{
	public function view($allowed_fields, $table, $id, $where_query='', $orderby='', $limit='', $groupby='')
	{
		global $connect;
		
		if($groupby != "")
		{
			$groupby = "group by {$groupby}";
		}
		if($orderby != "")
		{
			$orderby = "order by {$orderby}";
		}
		if($limit != "")
		{
			$limit = "limit {$limit}";
		}
		
		$query = "SELECT {$allowed_fields} FROM {$table} where {$id} != '' {$where_query} {$groupby} {$orderby} {$limit}";
		$queryResult = $connect->query($query);
		if(!$queryResult)
		{
			return false;
		}
		$num_rows = $queryResult->num_rows;
		$result = array();
		while($row = $queryResult->fetch_assoc())
		{
			$result[] = $row;
		}
		
		return array("result"=>$result, "num_rows"=>$num_rows);
		
		$connect->close();
	}
	
	public function insert($table, $params=array())
	{
		global $connect;
		
		$columns = '';
		$x = 1;
		foreach($params as $key => $value)
		{
			$columns .= "$key='$value'";
			if($x < count($params))
			{
				$columns .= ",";
			}
			$x++;
		}

		$query = "INSERT INTO {$table} SET {$columns}";
		$queryResult = $connect->query($query);
		if(!$queryResult)
		{
			return false;
		}
		else
		{
			return $connect->insert_id;
		}
		
		$connect->close();
	}
	
	public function update($table, $params=array(), $where_query=array())
	{
		global $connect;
		
		$columns = '';
		$x = 1;
		foreach($params as $key => $value)
		{
			$columns .= "$key='$value'";
			if($x < count($params))
			{
				$columns .= ",";
			}
			$x++;
		}
		
		$conditions = '';
		$y = 1;
		foreach($where_query as $key => $value)
		{
			$conditions .= "$key='$value'";
			if($y < count($where_query))
			{
				$conditions .= " and ";
			}
			$y++;
		}

		$query = "UPDATE {$table} SET {$columns} where {$conditions}";
		$queryResult = $connect->query($query);
		if(!$queryResult)
		{
			return false;
		}
		else
		{
			return $connect->affected_rows;
		}
		
		$connect->close();
	}
	
	public function delete($table, $where_query=array())
	{
		global $connect;
		
		$conditions = '';
		$y = 1;
		foreach($where_query as $key => $value)
		{
			$conditions .= "$key='$value'";
			if($y < count($where_query))
			{
				$conditions .= " and ";
			}
			$y++;
		}
		
		if($conditions != "")
		{
			$conditions = "where {$conditions}";
		}
		
		$query = "DELETE from {$table} {$conditions}";
		$queryResult = $connect->query($query);
		if(!$queryResult)
		{
			return false;
		}
		else
		{
			return $connect->affected_rows;
		}
		
		$connect->close();
	}
	
	public function custom($query)
	{
		global $connect;
		
		$query = $query;
		$queryResult = $connect->query($query);
		if(!$queryResult)
		{
			return false;
		}
		else
		{
			return $queryResult;
		}
		
		$connect->close();
	}
	
	public function check_duplicates($table, $idname, $id, $fieldname, $fieldid, $mode)
	{
		if($mode == "insert")
		{
			$result = $this->view($fieldname, $table, $idname, "and LOWER($fieldname) = '$fieldid'");
			return $result['num_rows'];
		}
		
		if($mode == "edit")
		{
			$checkresult = $this->view($fieldname, $table, $idname, "and $idname = '$id'");
			$checkrow = $checkresult['result'][0];
			if(strtolower($checkrow[$fieldname]) != strtolower($fieldid))
			{
				$result = $this->view($fieldname, $table, $idname, "and $fieldname = '$fieldid'");
				return $result['num_rows'];
			}
		}
	}
	
	public function get_maxorder($table)
	{
		$queryResult = $this->custom("select MAX(order_custom) as max_order from {$table}");
		$row = $queryResult->fetch_assoc();
		return $row['max_order'];
	}
	
	public function unique_visitors($table, $table2='', $fieldname='', $fieldid='', $user_ip, $regid='')
	{
		if($regid != '' and $regid != '0')
		{
			$where_query = "and regid='{$regid}'";
		}
		else
		{
			$where_query = "and user_ip='{$user_ip}'";
		}
		if($table2 != "" and $fieldid != "")
		{
			$queryResult = $this->view($fieldname, $table, $fieldname, "and {$fieldname}='{$fieldid}' {$where_query}");
		}
		else
		{
			$queryResult = $this->view($fieldname, $table, $fieldname, "{$where_query}");
		}
		$count = $queryResult['num_rows'];
		
		if($count === 0)
		{
			if($regid=='')
			{
				$regid = 0;
			}
			$fields = array('regid'=>$regid, 'status'=>'active', 'user_ip'=>$user_ip);
			if($table2 != "" and $fieldid != "")
			{
				$fields[$fieldname] = $fieldid;
			}
			$fields['createtime'] = date('H:i:s');
			$fields['createdate'] = date('Y-m-d');
			
			$visitorsResult = $this->insert($table, $fields);
			if($visitorsResult and $table2 != "")
			{
				$updateCounts = $this->custom("update {$table2} set views = views+1 where {$fieldname}='{$fieldid}'");
				if(!$updateCounts)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				return false;
			}
		}
	}
}

$db = new db;
?>