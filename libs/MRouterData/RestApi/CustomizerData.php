<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\CustomizerData
	class CustomizerData extends EndPoint {

		function __construct() {
			//echo("\OddCore\RestApi\CustomizerData::__construct<br />");


		}

		public function perform_call($data) {
			//echo("\OddCore\RestApi\CustomizerData::perform_call<br />");

      $option = $data['option'];

      $return_object = array();
      $return_object['option'] = get_option( $option );

			$encoder = new \MRouterData\MRouterDataEncoder();
			$return_object["data"] = $encoder->encode_post($post);

			return $this->output_success($return_object);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CustomizerData<br />");
		}
	}
