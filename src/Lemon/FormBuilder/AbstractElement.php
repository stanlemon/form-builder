<?php
namespace Lemon\FormBuilder;

use Lemon\FormBuilder\Element;
use Lemon\FormBuilder\Validator;

class AbstractElement implements Element {

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

		$this->addValidator($this->dom->getAttribute('type'));

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
		return $this->dom->getAttribute('name');
	}

	public function getValue() {
		return $this->dom->getAttribute('value');
	}

	public function setValue($value) {
		$this->dom->setAttribute('value', $value);
	}
}
