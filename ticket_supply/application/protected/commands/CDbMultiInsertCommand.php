<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 9/4/14
 * Time: 3:52 PM
 */
class CDbMultiInsertCommand extends CDbCommand
{

	/** @var CActiveRecord $class */
	private $class;

	/** @var string $insert_template */
	private $insert_template = "insert into %s(%s) ";

	/** @var string $value_template */
	private $value_template = "(%s)";

	/** @var string $query */
	public $query;

	/** @var CDbColumnSchema[] $columns */
	private $columns;

	/** @var boolean $fresh */
	private $fresh;

	/** @var CDbConnection $db */
	private $db;

	/** @param CActiveRecord $class
	 * @param CDbConnection $db
	 */
	public function __construct($class, $db = null) {
		$this->class = $class;
		$this->createTemplate();
		if (is_null($db)) {
			$this->db = Yii::app()->db;
		} else {
			$this->db = $db;
		}
	}

	private function createTemplate() {
		$this->fresh    = true;
		$value_template = "";
		$columns_string = "";
		$this->columns  = $this->class->getMetaData()->tableSchema->columns;
		$counter        = 0;
		foreach ($this->columns as $column) {
			/** @var CDbColumnSchema $column */
			if ($column->autoIncrement) {
				$value_template .= "0";
			} else if ($column->type == "integer" || $column->type == "boolean" || $column->type == "float" || $column->type == "double") {
				$value_template .= "%d";
			} else {
				$value_template .= "\"%s\"";
			}
			$columns_string .= $column->name;
			$counter++;
			if ($counter != sizeof($this->columns)) {
				$columns_string .= ", ";
				$value_template .= ", ";
			}
		}

		$this->insert_template = sprintf($this->insert_template, $this->class->tableName(), $columns_string);
		$this->value_template  = sprintf($this->value_template, $value_template);
	}

	/** @param boolean $validate
	 * @param CActiveRecord $record
	 */
	public function add($record, $validate = true, $params) {
		$values = array();
		if ($validate) {
			if (!$record->validate()) {
				return false;
			}
		}
		$counter = 0;
		foreach ($this->columns as $column) {
			if ($column->autoIncrement) {
				continue;
			}
			$values[$counter] = isset($params[$column->name]) ? $params[$column->name] : null;
			$counter++;
		}
		if (!$this->fresh) {
			$this->query .= ",";
		} else {
			$this->query = "values";
		}
		$this->fresh = false;
		$this->query .= vsprintf($this->value_template, $values);
		return true;
	}

	public function getConnection() {
		return $this->db;
	}

	public function execute($params=array()) {
		return $this->insert_template . " " . $this->query;
		$this->setText($this->insert_template . " " . $this->query);
		return parent::execute($params);
	}
}
