<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\UploadAttachmentEndPoint
	class UploadAttachmentEndPoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\UploadAttachmentEndPoint::__construct<br />");
			
			parent::__construct();
		}

		public function perform_call($data) {
			//echo("\OddCore\RestApi\UploadAttachmentEndPoint::perform_call<br />");
			
			$post_type = 'attachment';
			
			$original_name = $_FILES['file']['name'];
			$upload = wp_upload_bits( $original_name, null, file_get_contents( $_FILES['file']['tmp_name'] ) );
			
			$wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );
			
			$wp_upload_dir = wp_upload_dir();
			
			$attachment = array(
				'post_type' => 'attachment',
				'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', $original_name),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			
			if(isset($data['post_title'])) {
				$attachment['post_title'] = sanitize_text_field($data['post_title']);
			}
			if(isset($data['post_content'])) {
				$attachment['post_content'] = $data['post_content'];
			}
			
			$result_id = wp_insert_attachment( $attachment, $upload['file'], 0 );

			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$attach_data = wp_generate_attachment_metadata( $result_id, $upload['file'] );
			wp_update_attachment_metadata( $result_id, $attach_data );
			
			if($result_id) {
				if(isset($data['acf'])) {
					foreach($data['acf'] as $field_name => $value) {
						update_field($field_name, $value, $result_id);
					}
				}
			}

			return $this->output_success($result_id);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\UploadAttachmentEndPoint<br />");
		}
	}
