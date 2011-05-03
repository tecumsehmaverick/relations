<?php
	
	/**
	 * @package libs
	 */
	
	require_once EXTENSIONS . '/relations/lib/class.iterator.php';
	require_once EXTENSIONS . '/relations/lib/class.page.php';
	
	/**
	 * The Relations class contains methods for loading and saving section relations.
	 */
	class Relation {
		static public $instances;
		
		/**
		 * Does a test case exist?
		 * @param string $handle The handle of the test case.
		 * @access public
		 * @static
		 */
		static public function exists($id) {
			return in_array($id, self::fetchAll());
		}
		
		/**
		* Fetch all relations as an array of IDs.
		*/
		static public function fetchAll() {
			$ids = Symphony::Database()->fetchCol('id', '
				SELECT
					r.id
				FROM
					`tbl_relations` AS `r`
			');
			
			if (is_array($ids)) {
				return $ids;
			}
			
			return array();
		}
		
		/**
		 * Load a section relation.
		 * @param string $path The full path to the test case.
		 * @access public
		 * @static
		 */
		static public function load($id) {
			if (!isset(self::$instances)) {
				self::$instances = array();
			}
			
			if (!in_array($class, self::$instances)) {
				$row = Symphony::Database()->fetchRow(0, '
					SELECT
						r.*
					FROM
						`tbl_relations` AS `r`
				');

				$instance = new self();
				$instance->setData($row);

				self::$instances[$class] = $instance;
			}
			
			return self::$instances[$class];
		}
		
		protected $data;
		protected $errors;
		
		public function __construct() {
			$this->data = (object)array();
			$this->errors = (object)array();
		}
		
		/**
		* Access the internal relation data.
		*/
		public function data() {
			return $this->data;
		}
		
		/**
		* Get the internal list of validation errors.
		*/
		public function errors() {
			return $this->errors;
		}
		
		/**
		* Set the relations data.
		* @param array|object $data
		*/
		public function setData($data) {
			$this->data = (object)array();
			
			foreach ($data as $key => $value) {
				$this->data->{$key} = $value;
			}
		}
	}
	
?>