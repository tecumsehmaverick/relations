<?php
	
	/**
	 * @package relations
	 */
	
	/**
	 * Section relationship management.
	 */
	class Extension_Relations extends Extension {
		/**
		 * Extension information.
		 */
		public function about() {
			return array(
				'name'			=> 'Relations',
				'version'		=> '0.1',
				'release-date'	=> '2011-04-27',
				'author'		=> array(
					array(
						'name'			=> 'Rowan Lewis',
						'website'		=> 'http://rowanlewis.com/',
						'email'			=> 'me@rowanlewis.com'
					)
				)
			);
		}
		
		/**
		 * Add navigation items.
		 */
		public function fetchNavigation() {
			return array(
				array(
					'location'	=> 'Blueprints',
					'name'		=> 'Relations',
					'link'		=> '/list/'
				)
			);
		}
		
		/**
		* Cleanup tables.
		*/
		public function uninstall() {
			$this->_Parent->Database->query("DROP TABLE `sym_relations`");
			$this->_Parent->Database->query("DROP TABLE `sym_relations_entries`");
		}
		
		/**
		* Create tables.
		*/
		public function install() {
			$this->_Parent->Database->query("
				CREATE TABLE `sym_relations` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`name` varchar(255) DEFAULT NULL,
					`from_limit` int(11) NOT NULL DEFAULT '0',
					`from_section` int(11) NOT NULL DEFAULT '0',
					`from_type` enum('one','many') NOT NULL DEFAULT 'one',
					`to_limit` int(11) NOT NULL DEFAULT '0',
					`to_section` int(11) NOT NULL DEFAULT '0',
					`to_type` enum('one','many') NOT NULL DEFAULT 'one',
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");
			
			$this->_Parent->Database->query("
				CREATE TABLE `sym_relations_entries` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`relation_id` int(11) NOT NULL,
					`from_entry` int(11) NOT NULL,
					`to_entry` int(11) NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");
			
			return true;
		}
	}

?>