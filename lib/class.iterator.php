<?php
	
	/**
	 * @package libs
	 */
	
	/**
	 * Iterate over available relations.
	 */
	class RelationsIterator extends ArrayIterator {
		/**
		 * Iterate over all available relations. Optionally
		 * accepts a list of relation IDs instead.
		 * @param array $items Relation IDs.
		 */
		public function __construct(array $items = null) {
			if (is_array($items)) {
				parent::__construct($items);
			}
			
			else {
				parent::__construct(Relation::fetchAll());
			}
		}
	}
	
?>