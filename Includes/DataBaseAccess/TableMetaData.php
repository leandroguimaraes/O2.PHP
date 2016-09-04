<?php
class TableMetaData {
	public $column_name;
	public $is_nullable;
	public $data_type;
	public $character_maximum_length;

	public function __construct($row)
	{
		$this->column_name = $row['COLUMN_NAME'];
		$this->is_nullable = (strtoupper($row['IS_NULLABLE']) == 'NO') ? false : true;

		if (isset($row['DATA_TYPE']))
			$this->data_type = $row['DATA_TYPE'];

		if (isset($row['CHARACTER_MAXIMUM_LENGTH']) && ($row['CHARACTER_MAXIMUM_LENGTH'] > 0))
			$this->character_maximum_length = intval($row['CHARACTER_MAXIMUM_LENGTH']);
	}
}
