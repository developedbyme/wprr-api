<?php
	namespace Wprr;

	use \Wprr\OddCore\PluginBase;

	class Plugin extends PluginBase {

		function __construct() {
			//echo("\Wprr\Plugin::__construct<br />");
			
			parent::__construct();
			
		}

		protected function create_additional_hooks() {
			//echo("\Wprr\Plugin::create_additional_hooks<br />");
			
			$this->add_additional_hook(new \Wprr\ChangePostHooks());
			$this->add_additional_hook(new \Wprr\GlobalItemHooks());
			$this->add_additional_hook(new \Wprr\ApiActionHooks());
		}

		protected function create_rest_api_end_points() {
			//echo("\Wprr\Plugin::create_rest_api_end_points<br />");
			
			$api_namespace = 'wprr';
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\GlobalItemEndpoint(), 'global/(?P<item>[a-z0-9\-\_\/]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$current_end_point = new \Wprr\RestApi\Admin\CreatePostEndpoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('admin/(?P<post_type>[a-z0-9\-\_]+)/create', $api_namespace, 1, 'POST');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \Wprr\RestApi\Admin\ChangePostEndpoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('admin/post/(?P<post_id>\d+)/edit', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \Wprr\RestApi\Admin\BatchChangePostsEndpoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('admin/batch/edit-posts', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \Wprr\RestApi\ActionEndpoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('action/(?P<action_name>[a-zA-Z0-9\-\_\/]+)', $api_namespace, 1, 'POST');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \Wprr\RestApi\Admin\ImportItemEndpoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('admin/import/(?P<type>[a-zA-Z0-9\-\_]+)/(?P<id>[a-zA-Z0-9\-\_\/]+)', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
		}
		
		public function filter_paths($paths) {
			
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			
			$paths['current'] = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$paths['home'] = get_home_url();
			$paths['site'] = get_site_url();
			$paths['theme'] = get_stylesheet_directory_uri();
			$paths['rest'] = rest_url();
			$paths['wprrData'] = WPRR_URL.'/data';
			
			if(function_exists('wc_get_page_id')) {
				$paths['shop'] = get_permalink(wc_get_page_id('shop'));
				$paths['cart'] = wc_get_cart_url();
				$paths['checkout'] = wc_get_checkout_url();
			}
			
			$upload_dir = wp_upload_dir(null, false);
			$paths['uploads'] = $upload_dir['baseurl'];
			
			return $paths;
		}
		
		public function filter_image_sizes($image_sizes) {
			
			global $_wp_additional_image_sizes;
			
			foreach(get_intermediate_image_sizes() as $current_size) {
				
				if(in_array($current_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
					$image_sizes[$current_size]['width'] = get_option( "{$current_size}_size_w");
					$image_sizes[$current_size]['height'] = get_option( "{$current_size}_size_h");
					$image_sizes[$current_size]['crop'] = (bool) get_option( "{$current_size}_crop");
				}
				else if(isset($_wp_additional_image_sizes[$current_size])) {
					$image_sizes[$current_size] = array(
						'width' => $_wp_additional_image_sizes[$current_size]['width'],
						'height' => $_wp_additional_image_sizes[$current_size]['height'],
						'crop' => $_wp_additional_image_sizes[$current_size]['crop'],
					);
				}
			}
			$image_sizes['full'] = array(
				'width' => 0,
				'height' => 0,
				'crop' => false,
			);
			
			
			return $image_sizes;
		}
		
		protected function create_filters() {
			//echo("\Wprr\Plugin::create_filters<br />");
			
			$prefix = WPRR_DOMAIN.'/';
			
			add_filter($prefix.'configuration_image_sizes', array($this, 'filter_image_sizes'), 10, 1);
			add_filter($prefix.'configuration_paths', array($this, 'filter_paths'), 10, 1);
			
			add_action($prefix.'prepare_api_request', array($this, 'hook_prepare_api_request'), 10, 1);
			
			add_action('wpml_before_init', array($this, 'hook_wpml_before_init'));
			
			$admin_prefix = $prefix.'admin/';
			$create_post_prefix = $admin_prefix.'create_post/';
			
			add_filter($create_post_prefix.'valid_combination', array($this, 'filter_create_post_valid_combination'), 10, 4);
			add_filter($create_post_prefix.'insert/draft', array($this, 'filter_create_post_insert_draft'), 10, 3);
			add_filter($create_post_prefix.'insert/orderForCheckout', array($this, 'filter_create_post_insert_orderForCheckout'), 10, 3);
			
			add_filter('wprr/data-api/generate-settings', array($this, 'filter_data_api_generate_settings'), 10, 1);
			add_filter('wprr/data-api/generate-ranges', array($this, 'filter_data_api_generate_ranges'), 10, 1);
		}
		
		public function filter_create_post_valid_combination($is_valid, $post_type, $data_type, $creation_method) {
			
			if(!post_type_exists($post_type)) {
				return false;
			}
			if(!has_filter('wprr/admin/create_post/insert/'.$creation_method)) {
				return false;
			}
			
			return $is_valid;
		}
		
		public function filter_create_post_insert_draft($post_id, $title, $post_type) {
			$insert_arguments = array(
				'post_title' => $title,
				'post_status' => 'draft',
				'post_type' => $post_type,
			);
			
			$post_id = wp_insert_post($insert_arguments);
			
			return $post_id;
		}
		
		public function filter_create_post_insert_orderForCheckout($post_id, $title, $post_type) {
			
			$time_zone = get_option('timezone_string');
			if($time_zone) {
				date_default_timezone_set($time_zone);
			}
			
			$insert_arguments = array(
				'post_title' => $title,
				'post_status' => 'draft',
				'post_type' => 'shop_order',
			);
			
			$post_id = wp_insert_post($insert_arguments);
			
			$order = wc_get_order($post_id);
			
			$user_id = get_current_user_id();
			
			if($user_id) {
				$customer = new \WC_Customer($user_id);
				
				$order->set_billing_first_name($customer->get_billing_first_name());
				$order->set_billing_last_name($customer->get_billing_last_name());
				$order->set_billing_company($customer->get_billing_company());
				$order->set_billing_address_1($customer->get_billing_address_1());
				$order->set_billing_address_2($customer->get_billing_address_2());
				$order->set_billing_postcode($customer->get_billing_postcode());
				$order->set_billing_city($customer->get_billing_city());
				$order->set_billing_country($customer->get_billing_country());
				$order->set_billing_phone($customer->get_billing_phone());
				
				$order->set_customer_id($user_id);
			}
			
			$now = time();
			$time = date('Y-m-d', $now).'T'.date('H:i:s', $now);
			
			//METODO: figure this out
			$order->set_date_created(strtotime($time));
			$order->set_date_modified(strtotime($time));
			
			update_post_meta($post_id, '_created_via', 'api');
			
			update_post_meta($post_id, '_customer_ip_address', $_SERVER['REMOTE_ADDR']);
			update_post_meta($post_id, '_customer_user_agent', $_SERVER['HTTP_USER_AGENT']);
			
			$order->set_order_key( wc_generate_order_key() );
			
			$order->save($post_id);
			
			return $post_id;
		}
		
		protected function define_varaible_code($name, $value, $delimiter = '\'') {
			$return_string = '';
			$return_string .= "if(!defined('".$name."')) {"."\n";
			$return_string .= "	define('".$name."', ".$delimiter.$value.$delimiter.");"."\n";
			$return_string .= "}"."\n";
			
			return $return_string;
		}
		
		public function filter_data_api_generate_settings($code) {
			
			global $wpdb;
			
			$code .= $this->define_varaible_code('DB_NAME', DB_NAME);
			$code .= $this->define_varaible_code('DB_USER', DB_USER);
			$code .= $this->define_varaible_code('DB_PASSWORD', DB_PASSWORD);
			$code .= $this->define_varaible_code('DB_HOST', DB_HOST);
			$code .= $this->define_varaible_code('DB_CHARSET', DB_CHARSET);
			$code .= $this->define_varaible_code('DB_TABLE_PREFIX', $wpdb->prefix);
			
			$code .= $this->define_varaible_code('THEME_NAME', basename(get_template_directory()));
	
			$code .= $this->define_varaible_code('SITE_URL', get_site_url());
			
			$upload_dir = wp_upload_dir(null, false);
			$code .= $this->define_varaible_code('UPLOAD_URL', $upload_dir['baseurl']);
			$code .= $this->define_varaible_code('UPLOAD_DIR', $upload_dir['basedir']);
	
			$code .= $this->define_varaible_code('AUTH_KEY', AUTH_KEY);
			$code .= $this->define_varaible_code('SECURE_AUTH_KEY', SECURE_AUTH_KEY);
			$code .= $this->define_varaible_code('LOGGED_IN_KEY', LOGGED_IN_KEY);
			$code .= $this->define_varaible_code('NONCE_KEY', NONCE_KEY);
			$code .= $this->define_varaible_code('AUTH_SALT', AUTH_SALT);
			$code .= $this->define_varaible_code('SECURE_AUTH_SALT', SECURE_AUTH_SALT);
			$code .= $this->define_varaible_code('LOGGED_IN_SALT', LOGGED_IN_SALT);
			$code .= $this->define_varaible_code('NONCE_SALT', NONCE_SALT);
			
			$code .= $this->define_varaible_code('COOKIEHASH', COOKIEHASH);
			$code .= $this->define_varaible_code('LOGGED_IN_COOKIE', LOGGED_IN_COOKIE);
			
			$code .= $this->define_varaible_code('NONCE_LIFE', apply_filters( 'nonce_life', DAY_IN_SECONDS ), '');
			
			$post_types = get_post_types();
			
			$public_types = array();
			$rewrites = array();
			
			foreach($post_types as $post_type) {
				$post_type_data = get_post_type_object($post_type);
				if($post_type_data->public) {
					
					if($post_type_data->rewrite) {
						$rewrites[trim($post_type_data->rewrite["slug"], "/")] = $post_type;
					}
					else {
						$public_types[] = $post_type;
					}
				}
				
			}
			
			$code .= $this->define_varaible_code('PUBLIC_POST_TYPES', "array('".implode('\',\'', $public_types)."')", '');
			
			$array_code = "array("."\n";
			foreach($rewrites as $slug => $type) {
				$array_code .= "\t'".$slug."' => '".$type."',\n";
			}
			$array_code .= ")";
			
			$code .= $this->define_varaible_code('REWRITE_POST_TYPES', $array_code, '');
			
			$languages = apply_filters( 'wpml_active_languages', array());
			
			$default_language = null;
			
			$site_url = get_site_url();
			
			$array_code = "array("."\n";
			foreach($languages as $language_code => $language_data) {
				$current_url = $language_data['url'];
				
				if (substr($current_url, 0, strlen($site_url)) == $site_url) {
					$current_url = substr($current_url, strlen($site_url));
				}
				
				if($current_url === '') {
					$default_language = $language_code;
				}
				else {
					$array_code .= "\t'".ltrim($current_url, '/')."' => '".$language_code."',\n";
				}
			}
			$array_code .= ")";
			
			$code .= $this->define_varaible_code('LANGUAGE_BASE_URLS', $array_code, '');
			$code .= $this->define_varaible_code('DEFAULT_LANGUAGE', $default_language, '\'');
			
			return $code;
		}
		
		public function filter_data_api_generate_ranges($code) {
			
			$code .= 'global $wprr_data_api;'."\n";
			$code .= '$range_controller = $wprr_data_api->range();'."\n";
			
			$select_prefix = WPRR_DIR.'/libs/Wprr/DataApi/Data/Range/Select/';
			$select_namespace = '\\Wprr\\DataApi\\Data\\Range\\Select\\';
			
			$selections = array(
				'idSelection' => 'IdSelection',
				'relation' => 'Relation',
				'menu' => 'Menu',
				'postRelation' => 'PostRelation',
				'wpmlLanguage' => 'WpmlLanguage',
				'anyStatus' => 'AnyStatus',
				'byPostStatus' => 'ByPostStatus',
				'posts' => 'Posts',
				'byTaxonomyTerm' => 'ByTaxonomyTerm',
				'inDateRange' => 'InDateRange',
				'products' => 'Products',
				'includePrivate' => 'IncludePrivate',
				'includeDraft' => 'IncludeDraft',
				'subscriptionsForProduct' => 'SubscriptionsForProduct',
				'subscriptionsForUser' => 'SubscriptionsForUser',
				'ordersForProduct' => 'OrdersForProduct',
				'mySubscriptions' => 'MySubscriptions',
				'myOrders' => 'MyOrders',
				'myDraftOrders' => 'MyDraftOrders',
				'orders' => 'Orders',
				'subscriptions' => 'Subscriptions',
				'byPostType' => 'ByPostType',
				'ordersForDiscountCode' => 'OrdersForDiscountCode',
				'objectRelation' => 'ObjectRelation',
				'ordersForSubscriptions' => 'OrdersForSubscriptions',
				'globalObjectRelation' => 'GlobalObjectRelation',
				'globalPointer' => 'GlobalPointer',
				'typeObjectRelation' => 'TypeObjectRelation',
				'openSignupInvitation' => 'OpenSignupInvitation',
				'parentsOf' => 'ParentsOf',
				'childrenOf' => 'ChildrenOf',
				'search' => 'Search',
				'objectProperty' => 'ObjectProperty',
				'unpaidOrderByKey' => 'UnpaidOrderByKey',
				'byObjectType' => 'ByObjectType',
				'byTrigger' => 'ByTrigger',
				'byDateMeta' => 'ByDateMeta',
			);
			
			foreach($selections as $id => $class_name) {
				$code .= wprr_get_data_api_select_registration_code($id, $select_prefix.$class_name.'.php', $select_namespace.implode('\\', explode('/', $class_name)))."\n";
			}
			
			$encode_prefix = WPRR_DIR.'/libs/Wprr/DataApi/Data/Range/Encode/';
			$encode_namespace = '\\Wprr\\DataApi\\Data\\Range\\Encode\\';
			
			$encodings = array(
				'id' => 'Id',
				'postTitle' => 'PostTitle',
				'featuredImage' => 'FeaturedImage',
				'postTerms' => 'PostTerms',
				'permalink' => 'Permalink',
				'preview' => 'Preview',
				'image' => 'Image',
				'menuItem' => 'MenuItem/MenuItem',
				'menuItem/post_type' => 'MenuItem/Types/PostType',
				'menuItem/custom' => 'MenuItem/Types/Custom',
				'menuItem/taxonomy' => 'MenuItem/Types/Taxonomy',
				'postContent' => 'PostContent',
				'page' => 'Page',
				'postExcerpt' => 'PostExcerpt',
				'pageSettings' => 'PageSettings',
				'pageSetting' => 'PageSetting',
				'type' => 'Type',
				'type/translations' => 'TypeTranslations',
				'pageDataSources' => 'PageDataSources',
				'dataSource' => 'DataSource',
				'messagesInGroup' => 'MessagesInGroup',
				'internalMessage' => 'InternalMessage',
				'internalMessage/change-comment' => 'InternalMessageTypes/ChangeComment',
				'internalMessage/field-changed' => 'InternalMessageTypes/FieldChanged',
				'fields' => 'Fields',
				'fields/translations' => 'FieldsTranslations',
				'fieldTemplate' => 'FieldTemplate',
				'fieldTemplate/relation' => 'FieldTemplateTypes/Relation',
				'relations' => 'Relations',
				'relation' => 'Relation',
				'relationOrder' => 'RelationOrder',
				'userRelation' => 'UserRelation',
				'objectTypes' => 'ObjectTypes',
				'postStatus' => 'PostStatus',
				'publishDate' => 'PublishDate',
				'subscriptionDates' => 'SubscriptionDates',
				'orderItems' => 'OrderItems',
				'product' => 'Product',
				'order/items' => 'Order/Items',
				'order/totals' => 'Order/Totals',
				'order/details' => 'Order/Details',
				'order/user' => 'Order/User',
				'order/paymentMethod' => 'Order/PaymentMethod',
				'order/paidDate' => 'Order/PaidDate',
				'order/creationType' => 'Order/CreationType',
				'order/subscription' => 'Order/Subscription',
				'order/refunds' => 'Order/Refunds',
				'pageTemplate' => 'PageTemplate',
				'postType' => 'PostType',
				'breadcrumb' => 'Breadcrumb',
				'sequenceNumber' => 'SequenceNumber',
				'relatedProducts' => 'RelatedProducts',
				'task' => 'Task',
				'process' => 'Process',
				'processPart' => 'ProcessPart',
				'itemInProcess' => 'ItemInProcess',
				'contentTemplate' => 'ContentTemplate',
				'contentTemplate/translations' => 'ContentTemplateTranslations',
				'templatePosition' => 'TemplatePosition',
				'description' => 'Description',
				'imagesFor' => 'ImagesFor',
				'tags' => 'Tags',
				'identifier' => 'Identifier',
				'value' => 'Value',
				'value/translations' => 'ValueTranslations',
				'dataImage' => 'DataImage',
				'subscription/orders' => 'Subscription/Orders',
				'discountCode' => 'DiscountCode/DiscountCode',
				'discountCode/recurring_percent' => 'DiscountCode/Types/RecurringPercent',
				'triggers' => 'Triggers',
				'trigger' => 'Trigger',
				'action' => 'Action',
				'group/orderedGroup' => 'Group/OrderedGroup',
				'group/orderedTypeGroup' => 'Group/OrderedTypeGroup',
				'group/orderedImageGroup' => 'Group/OrderedImageGroup',
				'form/submission' => 'Form/Submission/Submission',
				'uploadedFile' => 'UploadedFile',
				'signupInvite' => 'SignupInvite/SignupInvite',
				'communication/transactionalEmail' => 'Communication/TransactionalEmail',
				'communication/content' => 'Communication/CommunicationContent',
				'communication/title' => 'Communication/CommunicationTitle',
				'formSubmission' => 'FormSubmission'
			);
			
			foreach($encodings as $id => $class_name) {
				$code .= wprr_get_data_api_encode_registration_code($id, $encode_prefix.$class_name.'.php', $encode_namespace.implode('\\', explode('/', $class_name)))."\n";
			}
			
			$data_function_prefix = WPRR_DIR.'/libs/Wprr/DataApi/Data/Range/DataFunction/';
			$data_function_namespace = '\\Wprr\\DataApi\\Data\\Range\\DataFunction\\';
			
			$data_functions = array(
				'example' => 'Example',
				'cart' => 'Cart'
			);
			
			foreach($data_functions as $id => $class_name) {
				$code .= wprr_get_data_api_data_function_registration_code($id, $data_function_prefix.$class_name.'.php', $data_function_namespace.implode('\\', explode('/', $class_name)))."\n";
			}
			
			$action_namespace = '\\Wprr\\DataApi\\Data\\Action\\Actions\\';
			
			$code .= wprr_get_data_api_registry_registration_code('action/example', json_encode($action_namespace.'Example::apply_action'));
			$code .= wprr_get_data_api_registry_registration_code('action/privateFile', json_encode($action_namespace.'PrivateFile::apply_action'));
			
			return $code;
		}
		
		public function hook_wpml_before_init() {
			global $sitepress, $wprr_stored_cookie_language;
			
			if($sitepress) {
				$wprr_stored_cookie_language = $sitepress->get_language_cookie();
			}
		}
		
		public function hook_prepare_api_request($data) {
			
			wprr_performance_tracker()->start_meassure('Plugin(wprr-api) hook_prepare_api_request');
			
			global $sitepress;
			
			if(isset($sitepress)) {
				
				$language = null;
				if(isset($data['language'])) {
					$language = $data['language'];
				}
				else if(isset($_COOKIE['wp-wpml_current_language'])) {
					$language = $_COOKIE['wp-wpml_current_language'];
				}
				
				if($language) {
					$sitepress->switch_lang($language);
				
					if(function_exists('acf_update_setting')) {
						acf_update_setting('current_language', $language);
					}
				}
			}
			
			$time_zone = get_option('timezone_string');
			if($time_zone) {
				date_default_timezone_set($time_zone);
			}
			
			if(isset($data["asUser"]) && current_user_can('administrator')) {
				wp_set_current_user($data["asUser"]);
			}
			
			wprr_performance_tracker()->stop_meassure('Plugin(wprr-api) hook_prepare_api_request');
		}
		
		public function hook_save_post($post_id, $post, $update) {
			//echo("hook_save_post<br />");

			if(wp_is_post_revision($post_id)) {
				return;
			}

			remove_action('save_post', array($this, 'hook_save_post'));

			parent::hook_save_post($post_id, $post, $update);
			
			$post_type = get_post_type($post_id);
			if(($post_type === 'shop_order' || $post_type === 'shop_subscription') && function_exists('wc_get_order')) {
				$order = wc_get_order($post_id);
				$meta_name = 'wprr_product_id';
				delete_post_meta($post_id, $meta_name);
				foreach($order->get_items() as $item_id => $item_data) {
					$current_id = $item_data->get_product_id();
					add_post_meta($post_id, $meta_name, $current_id);
				}
			}
		}
		
		public static function test_import() {
			echo("Imported \Wprr\Plugin<br />");
		}
	}
?>
