<?php
abstract class SysObject {
	protected $query;
	protected $dbVars;

	public function __construct($conn = null)
	{
		$this->query = new Query($conn);
	}

	public function get_table()
	{
		global $o2_config;

		return $o2_config->tbprefix.$this->table;
	}

	public function get_dbVars()
	{
		$sql = 'SELECT DISTINCT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @table';

		$this->query->AddParameter('@table', $this->get_table(), PDO::PARAM_STR);
		$rs = $this->query->ExecuteReader($sql);

		$dbVars = array();
		while ($row = $rs->fetch())
			$dbVars[] = new TableMetaData($row);

		return $dbVars;
	}

	public function Insert()
	{
		if ($this->dbVars == null)
			$this->dbVars = $this->get_dbVars();

		$sql_values = '';
		$sql = 'INSERT INTO `'.$this->get_table().'` (';
		foreach ($this->dbVars as $dbVar)
		{
			if ($dbVar->column_name != 'id')
			{
				$sql .= '`'.$dbVar->column_name.'`,';

				$sql_values .= ',@'.$dbVar->column_name;

				$column_name = $dbVar->column_name;
				if (isset($this->$column_name))
				{

					$data_type = null;
					switch(strtolower($dbVar->data_type))
					{
						case 'int': $data_type = PDO::PARAM_INT; break;
						case 'varchar': $data_type = PDO::PARAM_STR; break;
						default: $data_type = PDO::PARAM_STR; break;
					}
					$this->query->AddParameter('@'.$dbVar->column_name, $this->$column_name, $data_type);
				}
				else
					$this->query->AddParameter('@'.$dbVar->column_name, null, PDO::PARAM_NULL);
			}
		}

		$sql = substr($sql, 0, strlen($sql) - 1).') VALUES ('.substr($sql_values, 1).')';

		$this->id = $this->query->ExecuteNonQuery($sql);
	}

	public function LoadBy_array($array)
	{
		foreach ($array as $key => $value)
			$this->$key = $value;
	}

	public function Load($id)
	{
		$sql = 'SELECT * FROM '.$this->get_table().' WHERE id = @id';

		$this->query->AddParameter('@id', $id, PDO::PARAM_INT);
		$rs = $this->query->ExecuteReader($sql);

		if ($rs->rowCount() == 1)
			$this->LoadBy_array($rs->fetch());
		else
			throw new Exception(get_class($this).' não encontrado para id = '.$id);
	}

	public function Update()
	{
		if ($this->dbVars == null)
			$this->dbVars = $this->get_dbVars();

		$sql_values = '';
		$sql = 'UPDATE `'.$this->get_table().'` SET ';
		foreach ($this->dbVars as $dbVar)
		{
			if ($dbVar->column_name != 'id')
			{
				$sql .= '`'.$dbVar->column_name.'` = @'.$dbVar->column_name.',';

				$column_name = $dbVar->column_name;
				if (isset($this->$column_name))
				{
					$data_type = null;
					switch(strtolower($dbVar->data_type))
					{
						case 'int': $data_type = PDO::PARAM_INT; break;
						case 'varchar': $data_type = PDO::PARAM_STR; break;
						default: $data_type = PDO::PARAM_STR; break;
					}
					$this->query->AddParameter('@'.$dbVar->column_name, $this->$column_name, $data_type);
				}
				else
					$this->query->AddParameter('@'.$dbVar->column_name, null, PDO::PARAM_NULL);
			}
		}

		$this->query->AddParameter('@id', $this->id, PDO::PARAM_INT);
		$sql = substr($sql, 0, strlen($sql) - 1).' WHERE id = @id';

		$this->query->ExecuteNonQuery($sql);
	}

	public function Delete()
	{
		$this->query->AddParameter('@id', $this->id, PDO::PARAM_INT);
		$result = $this->query->ExecuteNonQuery('DELETE FROM '.$this->get_table().' WHERE id = @id');

		if ($result == 0)
			throw new Exception('Nenhum registro excluído para '.get_class($this).'.id = '.$this->id);
	}
}