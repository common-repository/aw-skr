<?php
/**
 * CSV DATA FILTER Classes.
 */
if ( ! class_exists( 'csvData') ) :
class csvData
{

	private $delimiter;
	private $head;
	private $data;
	private $operator;

	function __construct($csvInput, $delimiter=";")
	{
		$this->operator = array(
					preg_quote("=="),
					preg_quote("!="),
					preg_quote(">="),
					preg_quote("<="),
					preg_quote("~="),
					preg_quote("~>"),
					preg_quote("~<"),
					preg_quote("="),
					preg_quote("~"),
					preg_quote("<"),
					preg_quote(">")
		);
		$this->setDelimiter($delimiter);
		$this->data = $this->create($csvInput);
	}

	function __call($callname, $args)
	{
		list($operator, $value) = $args;
		if( in_array($callname, $this->head) )
		{
			return $this->filter($callname, $operator, $value);
		}
		return false;
	}

	function replaceDataWith($newData)
	{
		$this->data = $newData;
	}

	function getData()
	{
		return $this->data;
	}

	function toJson()
	{
		return json_encode(array_values($this->data));
	}

	function toCsv()
	{
		$str = implode($this->delimiter, $this->head)."\n";
		foreach( $this->data as $data )
		{
			$str.= implode($this->delimiter, $data)."\n";
		}
		return $str;
	}

	function toArray()
	{
		return $this->data;
	}

	function query($query)
	{
		$query = trim($query);
		if( !$query )
		{
			$this->replaceDataWith(array());
			return $this;
		}
		$s[] = ",";
		$r[] = "[LIST]";
		$s[] = "&&";
		$r[] = "[AND]";
		$s[] = " and ";
		$r[] = "[AND]";
		$query = str_ireplace($s, $r, $query);
		$singleQuerys = explode("[LIST]", $query);
		if( $singleQuerys ) foreach( $singleQuerys as $key => $singleQuery )
			{
				$singleQuery = trim($singleQuery);
				$andQuerys = explode("[AND]", $singleQuery);
				$list[$key] = clone $this;
				if( $andQuerys ) foreach( $andQuerys as $andQuery )
					{
						$andQuery = trim($andQuery);
						preg_match("/(?<field>.*?)(?<operator>".implode("|", $this->operator).")(?<value>.*)/", $andQuery, $match);
						$list[$key]->filter($match["field"], $match["operator"], $match["value"]);
					}
			}
		$this->merge($list);
		return $this;
	}

	private function merge($objectArray)
	{
		foreach( $objectArray as $key => $object )
		{
			foreach( $object->getData() as $datasetpos => $dataset )
			{
				$newData[$datasetpos] = $dataset;
			}
		}
		$this->replaceDataWith($newData);
	}

	function filter($fieldname, $operator, $value)
	{
		$filter = new csvFilter($this);
		$filtered = $filter->filter($fieldname, $operator, $value);
		return $filtered;
	}

	private function create($input)
	{
		return $this->csv2array(trim($input));
	}

	private function csv2array($input)
	{
		$this->head = null;
		$data = array();
		$csvData = explode("\n", $input);

		foreach( $csvData as $csvLine )
		{
			if( is_null($this->head) )
			{
				$this->head = explode($this->delimiter, $csvLine);
			}
			else
			{
				$items = explode($this->delimiter, $csvLine);
				foreach( $this->head as $key => $val )
				{
					$prepareData[$this->head[$key]] = $items[$key];
				}
				$data[] = $prepareData;
			}
		}

		return $data;
	}

	private function setDelimiter($char)
	{
		$this->delimiter = $char;
	}

}
endif;

if ( ! class_exists( 'csvFilter') ) :
class csvFilter
{

	private $csvObject;

	function __construct($csvObject)
	{
		$this->csvObject = $csvObject;
	}

	function filter($field, $operator, $value)
	{

		if( $operator == "==" || $operator == "=" || $operator == ">" || $operator == "<" || $operator == ">=" || $operator == "<=" || $operator == "!=" )
		{
			foreach( $this->csvObject->getData() as $key => $data )
			{
				if( $this->compare($value, $operator, $data[$field]) )
				{
					$newData[(int) $key] = $data;
				}
			}
		}
		elseif( $operator == "~=" || $operator == "~" || $operator == "~>" || $operator == "~<" )
		{
			$newData = $this->process($value, $operator, $field, $this->csvObject->getData());
		}
		//print_r($newData);
		//exit;
		$this->csvObject->replaceDataWith($newData);
		return $this->csvObject;
	}

	function process($value, $operator, $field, $dataset)
	{

		foreach( $dataset as $datasetpos => $data )
		{
			$distance = ($data[$field] - $value);
			//echo $datasetpos." - ".$distance."\n";
			$current = array("distance" => $distance, "datasetpos" => $datasetpos);
			if( $distance <= 0 )
			{
				if( !isset($low["distance"]) || $current["distance"] >= $low["distance"] )
				{
					$low = $current;
				}
			}
			if( $distance >= 0 )
			{
				if( !isset($high["distance"]) || $current["distance"] <= $high["distance"] )
				{
					$high = $current;
				}
			}
		}
		//print_r($low);
		//print_r($high);

		switch ($operator)
		{
			case "~":
			case "~=":
				if( abs($low["distance"]) <= abs($high["distance"]) ) return array($low["datasetpos"] => $dataset[$low["datasetpos"]]);
				if( abs($low["distance"]) >= abs($high["distance"]) ) return array($high["datasetpos"] => $dataset[$high["datasetpos"]]);
			case "~>":
				if(!$high) $high = $low;
				return array($high["datasetpos"] => $dataset[$high["datasetpos"]]);
			case "~<":
				if(!$low) $low = $high;
				return array($low["datasetpos"] => $dataset[$low["datasetpos"]]);
		}
	}

	function compare($value, $operator, $givenvalue)
	{
		switch ($operator)
		{
			case "=":
			case "==":
				return ($givenvalue == $value ? true : false);
			case ">":
				return ($givenvalue > $value ? true : false);
			case "<":
				return ($givenvalue < $value ? true : false);
			case ">=":
				return ($givenvalue >= $value ? true : false);
			case "<=":
				return ($givenvalue <= $value ? true : false);
			case "!=":
				return ($givenvalue != $value ? true : false);
		}
	}

}
endif;
