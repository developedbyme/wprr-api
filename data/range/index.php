<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	$db = $wprr_data_api->database();

	$terms = $db->query('SELECT term_taxonomy_id as id, term_id, parent FROM wp_term_taxonomy WHERE taxonomy = "dbm_type"');
	
	$term_ids = array_map(function($item) {
		return (int)$item['term_id'];
	}, $terms);
	
	$term_names = $db->query('SELECT term_id as id, name, slug FROM wp_terms WHERE term_id IN ('.implode(',', $term_ids).')');
	
	foreach($term_names as $term) {
		if($term['slug'] === 'facility') {
			
			$query = 'SELECT object_id as id FROM wp_term_relationships WHERE term_taxonomy_id = "'.$term['id'].'"';
			$posts = $db->query($query);
			
			$wprr_data_api->output()->output_api_repsponse(array(
				'posts' => $posts
			));
		}
	}
	
	
?>