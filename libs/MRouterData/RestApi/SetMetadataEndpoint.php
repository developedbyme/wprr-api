<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\SetMetadataEndpoint
	class SetMetadataEndpoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::perform_call<br />");

			return $this->output_success('Added data (not!)');
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\SetMetadataEndpoint<br />");
		}
	}
