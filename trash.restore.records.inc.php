<?php
/**
 * Restore records processor.
 *
 * @package dbEdit Table Editor
 * @version 1.0
 * @author Jelle Jager
 * @copyright 2008 Jelle Jager
 * @license GPL
 *
 * @todo combine trash.purge.records.php with trash.restore.records.php into one new file trash.process.php
 */
if(IN_MANAGER_MODE!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODx Content Manager instead of accessing this file directly.");

if (!isset($dbConfig['deletedField']) ){
	$msg = "<h4>Configuration Error!</h4>
	<p>This table does not support a trash bin. Records are removed permanently when deleted.<br />
	To use the trash bin functionality your table must have a dedicated 'deleted' field and have this field declared in the module's config file.<p>";

} elseif (isset($_REQUEST['chk'])) {
	$id_keys = $_REQUEST['chk'];
	$ids_count = count($id_keys);
	//remember that ids can be strings!
	for ($i = 0; $i < count($id_keys); $i++) {
		$id_keys[$i]= "'".$modx->db->escape($id_keys[$i])."'";
	}

	$update_field = "{$dbConfig['deletedField']}='{$dbConfig['enabledValue']}'";
	$where = "{$dbConfig['keyField']} IN (" . implode(',', $id_keys) . ")";

	if ($modx->db->update($update_field, $dbConfig['tableName'], $where)) {
		$ok = true;
		$msg = ( $ids_count > 1 ) ? "{$ids_count} records were" : "1 record has been";
		$msg .= " successfully restored.";
	} else {
		$ok = false;
		$msg = "Restore operation failed. Database replied:<br />";
		$msg .= $modx->db->getLastError();
	}
} else {
	$msg = "Did not receive any records to restore!";
	$ok = false;
}

if ($ok == true) {
	$_SESSION['dbedit_message'] = array('succes', $msg);
} else {
	$_SESSION['dbedit_message'] = array('failure', $msg);
}

header("location: {$dbeHomeUrl}");
exit;
?>
