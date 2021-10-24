<?php
	
	error_reporting(E_ALL);
	
	require_once("../../setup.php");
	require_once("../settings.php");
	
	global $wprr_data_api;
	$db = $wprr_data_api->database();

	$terms = $db->query('SELECT term_taxonomy_id as id, term_id, parent FROM wp_term_taxonomy WHERE taxonomy = "dbm_type"');

	//$terms = $result->fetch_all(MYSQLI_ASSOC);
	
	$term_ids = array_map(function($item) {
		return (int)$item['term_id'];
	}, $terms);
	
	$term_names = $db->query('SELECT term_id as id, name, slug FROM wp_terms WHERE term_id IN ('.implode(',', $term_ids).')');
	
	foreach($term_names as $term) {
		if($term['slug'] === 'facility') {
			
			$query = 'SELECT object_id as id FROM wp_term_relationships WHERE term_taxonomy_id = "'.$term['id'].'"';
			$posts = $db->query($query);
			
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