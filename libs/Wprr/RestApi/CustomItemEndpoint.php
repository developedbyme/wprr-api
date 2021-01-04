<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\CustomItemEndpoint
	class CustomItemEndpoint extends EndPoint {

		function __construct() {
			//echo("\OddCore\RestApi\CustomItemEndpoint::__construct<br />");

			parent::__construct();
		}

		public function perform_call($data) {
			//echo("\OddCore\RestApi\CustomItemEndpoint::perform_call<br />");

			$item_type = $data['item_type'];
			$id = $data['id'];

			$has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_item_has_permission_'.$item_type;
			$query_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_item_get_'.$item_type;
			$encode_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_item_encode_'.$item_type;


			if(!has_filter($query_filter_name)) {
				return $this->output_error('No custom items for type '.$item_type);
			}

			$has_permission = apply_filters($has_permission_filter_name, true, $data);
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$item = apply_filters($query_filter_name, null, $id, $data);
			if(!$item) {
				return $this->output_success(null);
			}
			
			$encoded_item = apply_filters($encode_filter_name, array(), $item, $data);

			return $this->output_success($encoded_item);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CustomItemEndpoint<br />");
		}
	}
?>
