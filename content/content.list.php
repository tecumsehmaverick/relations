<?php
	
	/**
	 * @package content
	 */
	
	require_once EXTENSIONS . '/relations/lib/class.relation.php';
	
	/**
	 * Display a table view of available relations.
	 */
	class ContentExtensionRelationsList extends RelationsPage {
		/**
		 * Greate the page form.
		 */
		public function view() {
			$sm = new SectionManager(Symphony::Engine());
			$tests = new RelationsIterator();
			
			$this->setPageType('table');
			$this->setTitle(__(
				'%1$s &ndash; %2$s',
				array(
					__('Symphony'),
					__('Relations')
				)
			));
			
			$this->appendSubheading('Relations', Widget::Anchor(
				__('Create New'), $this->root_url . '/edit/',
				__('Create a new relation'), 'create button'
			));
			
			$table = new XMLElement('table');
			$table->appendChild(
				Widget::TableHead(array(
					array(__('Name'), 'col'),
					array(__('Relationship'), 'col')
				))
			);
			
			if (!$tests->valid()) {
				$table->appendChild(Widget::TableRow(array(
					Widget::TableData(
						__('None Found.'),
						'inactive',
						null, 2
					)
				)));
			}
			
			else foreach ($tests as $path) {
				$relation = Relation::load($path);
				$from_type = $relation->data()->{'from_type'};
				$to_type = $relation->data()->{'to_type'};
				
				$from_section = $sm->fetch($relation->data()->{'from_section'});
				$to_section = $sm->fetch($relation->data()->{'to_section'});
				
				if ($from_type == 'one' && $to_type == 'one') {
					$type = 'One %1$s to one %2$s';
				}
				
				else if ($from_type == 'one' && $to_type == 'many') {
					$type = 'One %1$s to many %2$s';
				}
				
				else if ($from_type == 'many' && $to_type == 'one') {
					$type = 'Many %1$s to one %2$s';
				}
				
				else if ($from_type == 'many' && $to_type == 'many') {
					$type = 'Many %1$s to many %2$s';
				}
				
				$from_section_xml = Widget::Anchor(
					$from_section->get('name'),
					sprintf(
						'%s/blueprints/sections/edit/%d/',
						SYMPHONY_URL,
						$from_section->get('id')
					)
				);
				$to_section_xml = Widget::Anchor(
					$to_section->get('name'),
					sprintf(
						'%s/blueprints/sections/edit/%d/',
						SYMPHONY_URL,
						$to_section->get('id')
					)
				);
				
				$row = new XMLElement('tr');
				$table->appendChild($row);
				
				$row->appendChild(Widget::TableData(
					Widget::Anchor(
						$relation->data()->name,
						sprintf(
							'%s/relation/%s/',
							$this->root_url,
							$relation->data()->id
						)
					)
				));
				
				$row->appendChild(Widget::TableData(__($type, array(
					$from_section_xml->generate(),
					$to_section_xml->generate()
				))));
			}
			
			$this->Form->appendChild($table);
		}
	}
	
?>