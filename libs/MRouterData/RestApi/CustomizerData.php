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

      $section = $data['section'];

      $return_object = array();
      $return_object = get_section( $section );

			$encoder = new \MRouterData\MRouterDataEncoder();

			return $this->output_success($return_object);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CustomizerData<br />");
		}
	}
