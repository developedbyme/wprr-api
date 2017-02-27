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

			$options = explode( ',', $data['options'] );

      $return_object = array();

			if ($options) :
				foreach ( $options as $option ) :
					$return_object[$option] = get_option( $option );
				endforeach;
			endif;

			$encoder = new \MRouterData\MRouterDataEncoder();

			return $this->output_success($return_object);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CustomizerData<br />");
		}
	}
