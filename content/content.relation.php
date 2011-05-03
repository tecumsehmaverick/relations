<?php
	
	/**
	 * @package content
	 */
	
	require_once EXTENSIONS . '/relations/lib/class.relation.php';
	
	/**
	 * Create and edit relations.
	 */
	class ContentExtensionRelationsRelation extends RelationsPage {
		protected $relation;
		protected $errors;
		
		/**
		* Get a list of available sections.
		*/
		public function getSectionOptions($section_id, $data = array()) {
			$sections = Symphony::Database()->fetch("
				SELECT
					s.*
				FROM
					`tbl_sections` AS s
				ORDER BY
					`sortorder` ASC
			");
			$result = array();
			
			foreach ($sections as $section) {
				$section = (object)$section;
				
				$selected = ($section->id == $section_id);
				$result[] = array(
					$section->id, $selected, $section->name
				);
			}
			
			return $result;
		}
		
		/**
		* Get a list of possible link types.
		*/
		public function getTypeOptions($type, $data = array()) {
			$result = array(
				array('one', false, __('One')),
				array('many', false, __('Many'))
			);
			
			foreach ($result as $index => $item) {
				$result[$index][1] = ($type == $item[0]);
			}
			
			return $result;
		}
		
		public function build($context) {
			// Load existing email:
			if (isset($context[0]) && Relation::exists($context[0])) {
				$this->relation = Relation::load($context[0]);
			}
			
			// Create new email:
			else {
				$this->relation = new Relation();
			}
			
			return parent::build($context);
		}
		
		/*
		public function action() {
			$relation = $this->relation;
			
			// Delete:
			if (isset($_POST['action']['delete'])) {
				if ($email->delete()) {
					redirect(sprintf(
						'%s/relations/',
						$this->root_url
					));
				}
				
				$this->pageAlert(
					__('An error occurred while processing this form. <a href="#error">See below for details.</a>'),
					Alert::ERROR
				);
			}
			
			// Edit or create:
			else {
				$action = (
					isset($relation->data()->id)
						? 'saved'
						: 'created'
				);
				
				// Update email with post data:
				if (isset($_POST['fields']) && is_array($_POST['fields'])) {
					$email->setData($_POST['fields']);
				}
				
				if (isset($_POST['overrides'])) {
					$email->setOverrides($_POST['overrides']);
				}
				
				else {
					$email->setOverrides(array());
				}
				
				// Email passes validation:
				if ($email->validate() && $email->save()) {
					redirect(sprintf(
						'%s/email/%d/%s/',
						$this->root_url,
						$relation->data()->id,
						$action
					));
				}
				
				$this->pageAlert(
					__('An error occurred while processing this form. <a href="#error">See below for details.</a>'),
					Alert::ERROR
				);
			}
		}
		*/
		
		public function view() {
			$relation = $this->relation;
			
			// Use 'Untitled' as page title when name is empty:
			$title = (
				isset($relation->data()->name) && trim($relation->data()->name) != ''
					? $relation->data()->name
					: __('Untitled')
			);
			
			$this->setPageType('form');
			$this->setTitle(__(
				(
					isset($relation->data()->id)
						? '%1$s &ndash; %2$s &ndash; %3$s'
						: '%1$s &ndash; %2$s'
				),
				array(
					__('Symphony'),
					__('Emails'),
					$title
				)
			));
			$this->appendSubheading($title);
			//$this->addScriptToHead(URL . '/extensions/emailbuilder/assets/email.js');
			
			// Status message:
			if (isset($this->_context[1])) {
				$action = null;
				
				switch ($this->_context[1]) {
					case 'saved': $action = '%1$s updated at %2$s. <a href="%3$s">Create another?</a> <a href="%4$s">View all %5$s</a>'; break;
					case 'created': $action = '%1$s created at %2$s. <a href="%3$s">Create another?</a> <a href="%4$s">View all %5$s</a>'; break;
				}
				
				if ($action) $this->pageAlert(
					__(
						$action, array(
							__('Email'), 
							DateTimeObj::get(__SYM_TIME_FORMAT__), 
							URL . '/symphony/extension/relations/relation/', 
							URL . '/symphony/extension/relations/relations/',
							__('Emails')
						)
					),
					Alert::SUCCESS
				);
			}
			
			$this->appendEssentialsFieldset($relation, $this->Form);
			
			$div = new XMLElement('div');
			$div->setAttribute('class', 'actions');
			$div->appendChild(
				Widget::Input('action[save]',
					(
						isset($relation->data()->id)
							? __('Save Changes')
							: __('Create Template')
					),
					'submit', array(
						'accesskey'		=> 's'
					)
				)
			);
			
			if (isset($relation->data()->id)) {
				$button = new XMLElement('button', 'Delete');
				$button->setAttributeArray(array(
					'name'		=> 'action[delete]',
					'class'		=> 'button confirm delete',
					'title'		=> __('Delete this email')
				));
				$div->appendChild($button);
			}
			
			$this->Form->appendChild($div);
		}
		
		public function appendEssentialsFieldset(Relation $relation, $wrapper) {
			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Essentials')));
			
			if (!empty($relation->data()->id)) {
				$fieldset->appendChild(Widget::Input(
					'fields[id]', $relation->data()->id, 'hidden'
				));
			}
			
			// Name:
			$label = Widget::Label(__('Name'));
			$label->appendChild(Widget::Input(
				'fields[name]',
				General::sanitize($relation->data()->name)
			));
			
			if (isset($relation->errors()->name)) {
				$label = Widget::wrapFormElementWithError($label, $relation->errors()->name);
			}
			
			$fieldset->appendChild($label);
			
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			// From section:
			$label = Widget::Label(__('From section'));
			$options = $this->getSectionOptions(
				$relation->data()->from_section
			);
			
			$select = Widget::Select(
				"fields[from_section]", $options
			);
			$select->setAttribute('class', 'page-picker');
			$label->appendChild($select);
			
			if (isset($relation->errors()->from_section)) {
				$label = Widget::wrapFormElementWithError($label, $relation->errors()->from_section);
			}
			
			$group->appendChild($label);
			
			// To section:
			$label = Widget::Label(__('To section'));
			$options = $this->getSectionOptions(
				$relation->data()->to_section
			);
			
			$select = Widget::Select(
				"fields[to_section]", $options
			);
			$select->setAttribute('class', 'page-picker');
			$label->appendChild($select);
			
			if (isset($relation->errors()->to_section)) {
				$label = Widget::wrapFormElementWithError($label, $relation->errors()->to_section);
			}
			
			$group->appendChild($label);
			$fieldset->appendChild($group);
			
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			// From type:
			$label = Widget::Label(__('From type'));
			$options = $this->getTypeOptions(
				$relation->data()->from_type
			);
			
			$select = Widget::Select(
				"fields[from_type]", $options
			);
			$select->setAttribute('class', 'page-picker');
			$label->appendChild($select);
			
			if (isset($relation->errors()->from_type)) {
				$label = Widget::wrapFormElementWithError($label, $relation->errors()->from_type);
			}
			
			$group->appendChild($label);
			
			// To section:
			$label = Widget::Label(__('To type'));
			$options = $this->getTypeOptions(
				$relation->data()->to_type
			);
			
			$select = Widget::Select(
				"fields[to_type]", $options
			);
			$select->setAttribute('class', 'page-picker');
			$label->appendChild($select);
			
			if (isset($relation->errors()->to_type)) {
				$label = Widget::wrapFormElementWithError($label, $relation->errors()->to_type);
			}
			
			$group->appendChild($label);
			$fieldset->appendChild($group);
			$wrapper->appendChild($fieldset);
		}
	}
	
?>