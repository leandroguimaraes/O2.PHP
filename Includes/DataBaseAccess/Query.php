<?php
class Query {
	public $parameters;

	protected $conn;

	public function __construct($connection = null)
	{
		global $conn, $o2_config;

		$this->parameters = array();

		if ($connection != null)
			$this->conn = $connection;
		else
		{
			if ($conn == null)
				$conn = new DBConn($o2_config->db_type, $o2_config->db_host, $o2_config->db_name, $o2_config->db_user, $o2_config->db_psw, array(PDO::ATTR_PERSISTENT => true));

			$this->conn = $conn;
		}
	}

	public function AddParameter($parameterName, $value, $var_type)
	{
		$this->parameters[$parameterName]['val'] = $value;
		$this->parameters[$parameterName]['type'] = $var_type;
	}

	protected function ExecuteQuery($sql)
	{
		$sql = str_replace('@', ':', $sql);
		$rs = $this->conn->conn->prepare($sql);

		foreach ($this->parameters as $key => $value)
			$rs->bindParam(str_replace('@', ':', $key), $value['val'], $value['type']);

		$rs->execute();

		$this->parameters = array();

		return $rs;
	}

	public function ExecuteReader($sql)
	{
		return $this->ExecuteQuery($sql);
	}

	public function ExecuteNonQuery($sql)
	{
		$rs = $this->ExecuteQuery($sql);

		if (strpos(strtoupper($sql), 'INSERT INTO') > -1)
			return $this->conn->conn->lastInsertId('id');
		else
			return $rs->rowCount();
	}
}
