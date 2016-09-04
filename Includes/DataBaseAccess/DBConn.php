<?php
class DBConn {
	const DBTYPE_MYSQL = 'mysql';

	public $conn;

	public function __construct($dbtype, $host, $dbname, $dbuser, $dbpsw) {
		$this->conn = new PDO($dbtype.':host='.$host.';dbname='.$dbname, $dbuser, $dbpsw);
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}
