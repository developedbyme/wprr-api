<?php
	
	error_reporting(E_ALL);
	
	require_once("../../setup.php");
	require_once("../settings.php");
	
	$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	$result = $db->query('SELECT term_taxonomy_id as id, term_id, parent FROM wp_term_taxonomy WHERE taxonomy = "dbm_type"');

	$terms = $result->fetch_all(MYSQLI_ASSOC);
	
	$term_ids = array_map(function($item) {
		return (int)$item['term_id'];
	}, $terms);
	
	$result = $db->query('SELECT term_id as id, name, slug FROM wp_terms WHERE term_id IN ('.implode(',', $term_ids).')');
	
	$term_names = $result->fetch_all(MYSQLI_ASSOC);
	
	foreach($term_names as $term) {
		if($term['slug'] === 'facility') {
			
			$query = 'SELECT object_id as id FROM wp_term_relationships WHERE term_taxonomy_id = "'.$term['id'].'"';
			$result = $db->query($query);
			$posts = $result->fetch_all(MYSQLI_ASSOC);
			
			$reposonse = array(
				'data' => array(
					'posts' => $posts
				)
			);

			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
	
			echo(json_encode($reposonse));
			die();
		}
	}
	
	
?>