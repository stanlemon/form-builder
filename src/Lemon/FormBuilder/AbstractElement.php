<?php
namespace Lemon\FormBuilder;

use Lemon\FormBuilder\Element;
use Lemon\FormBuilder\Validator;

class AbstractElement implements Element {

	const ATTR_TYPE = 'type';
	const ATTR_NAME = 'name';
	const ATTR_VALUE = 'value';
	const ATTR_CHECKED = 'checked';
	const ATTR_SELECTED = 'selected';
	const ATTR_REQUIRED = 'required';
	const ATTR_MAXLENGTH = 'maxlength';
	const ATTR_MINLENGTH = 'minlength';
	const ATTR_PATTERN = 'pattern';
	const ATTR_CLASS = 'class';

	protected $validators;
	protected $errors = array();
	protected $dom;

	public function __construct() {}

	public function validate($value) {
		$ret = true;

		foreach ($this->getValidators() as $validator) {
			if (!$validator->validate($value)) {
				if (method_exists($validator, 'getParams')) {
					$error = array(
						'class' 	=> get_class($validator),
						'params' 	=> $validator->getParams(),
					);
				} else {
					$error = get_class($validator);
				}

				$this->errors[] = $error;

				$ret = false;
			}
		}

		return $ret;
	}

	public function getValidators() {
		if (is_null($this->validators)) {
			$this->loadValidators();
		}
		return $this->validators;
	}

	public function loadValidators() {
		$this->validators = array();

		$this->addValidator($this->dom->getAttribute(self::ATTR_TYPE));

		for ($i = 0; $i<$this->dom->attributes->length; $i++) {
			$attr = $this->dom->attributes->item($i);

			$this->addValidator($attr->name, $attr->value);
		}
	}

	/**
	 * @todo Need to use a ValidatorResolver so that the class lookups can be overridden
	 */
	public function addValidator($name, $value = null) {
		if (!empty($name)) {
			$className = "Lemon\\FormBuilder\\Validator\\" . ucfirst(strtolower($name));

			if (class_exists($className)) {
				$validator = new $className($value);

				if (!$validator instanceof Validator) {
					throw new Exception("Class {$className} must implement the Lemon\\FormBuilder\\Validator interface.");
				} else {
					$this->validators[] = $validator;
				}
			}
		}
	}

	public function getErrors() {
		return $this->errors;
	}

	public function setDom(\DOMElement $dom) {
		$this->dom = $dom;
	}

	public function getDom() {
		return $this->dom;
	}

	public function getName() {
		return $this->dom->getAttribute(self::ATTR_NAME);
	}

	public function getValue() {
		return $this->dom->getAttribute(self::ATTR_VALUE);
	}

	public function setValue($value) {
		$this->dom->setAttribute(self::ATTR_VALUE, $value);
	}
}
