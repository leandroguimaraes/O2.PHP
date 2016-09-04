<?php
class O2Config {
	public $db_type;
	public $db_host;
	public $db_name;
	public $db_user;
	public $db_psw;

	public $tbprefix;

	public function __construct($o2_db_type, $o2_db_host, $o2_db_name, $o2_db_user, $o2_db_psw, $tbprefix)
	{
		$this->db_type = $o2_db_type;
		$this->db_host = $o2_db_host;
		$this->db_name = $o2_db_name;
		$this->db_user = $o2_db_user;
		$this->db_psw = $o2_db_psw;
		$this->tbprefix = $tbprefix;
	}
}