<?php
require_once($GLOBALS['DOCUMENT_ROOT']."/swcms/lib/php/DOLib.php");
class StructuredFieldValueLabel {
	public static function create ($unitId) {
		$unit = StructuredUnit::getUnit($unitId);
		$records = StructuredFieldValueLabel::getSorted($unitId);
		$rank=1;
		if ($records) {
			$rank = $records[sizeOf($records)-1][sizeOf(StructuredFieldValueLabel::getStructure())+2] + 1;
		}
		$query="INSERT INTO structured_field_value_label (unit_id, label, type, rank) VALUES ($unitId, '".$unit[3]."', 'text', $rank);";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
	}
	
	public static function update ($id, $values) {
		$query = "UPDATE structured_field_value_label SET ";
		$structure = StructuredFieldValueLabel::getStructure();
		$index=0;
		foreach ($structure as $column) {
			$args[]= $column." = '".$values[$index++]."'";
		}
		$query .= implode(', ', $args)." WHERE id=$id";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
	}
	
	public static function read ($id) {
		$query="SELECT * FROM structured_field_value_label WHERE id=$id";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
		$row=mysql_fetch_row($result);
		return $row;
	}

	public static function delete($id) {
		$query="delete from structured_field_value_label where id=$id";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
	}
	
	public static function getSorted ($unitId) {
		if (!$unitId) return;
		$query="select * from structured_field_value_label where unit_id=$unitId order by rank";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
		while ($row=mysql_fetch_row($result)) {
			$records[]=$row;
		}
		return $records;
	}
	
	public static function switchRank ($id1, $id2) {
		$record1=StructuredFieldValueLabel::read($id1);
		$record2=StructuredFieldValueLabel::read($id2);
		$offset = sizeOf(StructuredFieldValueLabel::getStructure())+2;
		$rank1=$record1[$offset];
		$rank2=$record2[$offset];
		$query = "UPDATE structured_field_value_label SET rank=$rank2 WHERE id=$id1;";
		$result1 = mysql_query($query)
		or die(sqlError(__FILE__, __LINE__,$query));
		$query = "UPDATE structured_field_value_label SET rank=$rank1 WHERE id=$id2;";
		$result2 = mysql_query($query)
		or die(sqlError(__FILE__, __LINE__,$query));
	}
	
	public static function getStructure () {
		$query="SHOW COLUMNS FROM structured_field_value_label";
		$result=mysql_query($query) or
		die(sqlError(__FILE__, __LINE__,$query));
		while ($row=mysql_fetch_row($result)) {
			$columnNames[]=$row[0];
		}
		// we remove the last element (rank) and the 2 first elements (id, unitId)
		return array_slice($columnNames, 2, sizeOf($columnNames)-3);
	}
}
?>