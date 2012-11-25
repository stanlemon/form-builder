<?php
namespace Lemon;

use Lemon\FormBuilder\Exception;
use Lemon\FormBuilder\Element;

class FormBuilder {

	protected $html;
	protected $dom;
	protected $nodes = array();
	protected $elementClasses = array(
		'input'		=> 'Lemon\FormBuilder\Element\Input',
		'select'	=> 'Lemon\FormBuilder\Element\Select',
		'textarea'	=> 'Lemon\FormBuilder\Element\Textarea',
	);
	protected $elements = array();
	protected $valid = array();
	protected $invalid = array();
	protected $processed;
	protected $errorTemplate = '<span class="help-inline">%s</span>';
	protected $invalidHandler;
	protected $validatorResolver;
	protected $messageResolver;

	public function __construct($html) {
		$this->html = $html;
	}

	public function build() {
		$this->loadDom();
		$this->findFields('input', 'select', 'textarea');
	}

	public function loadDom() {
		$this->dom = new \DOMDocument();
		$this->dom->loadHTML($this->html);
	}

	public function findFields() {
		$args = func_get_args();

		foreach ($args as $name) {
			foreach ($this->dom->getElementsByTagName($name) as $node) {
				$className = $this->elementClasses[strtolower($node->tagName)];

				if (!class_exists($className)) {
					throw new Exception("Class {$className} does not exist for element {$name}");
				} else {
					$element = new $className();

					if (!$element instanceof Element) {
						throw new Exception("Class {$className} must implement the Lemon\\FormBuilder\\Element interface.");
					} else {
						$element->setDom($node);
						$element->setValidatorResolver($this->getValidatorResolver());

						$this->elements[] = $element;
					}
				}
			}
		}
	}

	public function process($input) {
		$this->processed = $input;

		$valid = true;

		$this->valid = array();
		$this->invalid = array();

		foreach ($this->elements as $element) {
			$value = $this->findValueByFieldName($element->getName(), $this->processed);
	
			if ($element->validate($value)) {
				$this->valid[] = $element;
			} else {
				$this->invalid[] = $element;
				$valid = false;
			}
		}

		return $valid;
	}

	protected function handleError($element) {
		$errors = $element->getErrors();

		$first = reset($errors);

		$dom = new \DOMDocument();
		$dom->loadHTML(sprintf($this->errorTemplate, $this->getMessageResolver()->resolve($first)));

		// html > body > span (if using default)
		$error = $dom->documentElement->firstChild->firstChild;

		$element->getDom()->parentNode->appendChild(
			$this->dom->importNode($error, true)
		);

		if (is_callable($this->invalidHandler)) {
			call_user_func($this->invalidHandler, $element);
		}
	}

	public function render() {
		if ($this->isProcessed() && $this->hasInvalid()) {
			foreach ($this->getElements() as $element) {
				$value = $this->findValueByFieldName($element->getName(), $this->processed);

				$element->setValue($value);

				if (in_array($element, $this->getInvalid())) {
					$this->handleError($element);
				}
			}
		}
		return $this->dom->saveHTML($this->dom->documentElement->firstChild->firstChild);
	}

	public function findValueByFieldName($name, $input) {
		$var = array();

		parse_str($name, $var);

		$lookup = $input;

		while (null !== ($key = key($var))) {
			$var = $var[$key];

			if (isset($lookup[$key])) {
				$lookup = $lookup[$key];
			} else {
				$lookup = null;
			}

			if (!is_array($var)) {
				break;
			}
		}

		return $lookup;
	}

	public function isProcessed() {
		return !is_null($this->processed);
	}

	public function hasInvalid() {
		return count($this->getInvalid()) > 0;
	}

	public function registerElementClass($element, $class) {
		$this->elementClasses[$element] = $class;
	}

	public function getElements() {
		return $this->elements;
	}

	public function getValid() {
		return $this->valid;
	}

	public function getInvalid() {
		return $this->invalid;
	}

	public function setErrorTemplate($errorTemplate) {
		if (strpos("%s", $errorTemplate) === false) {
			throw new Exception("You must use %s in your error template for error messages to be set properly.");
		}
		$this->errorTemplate = $errorTemplate;
	}

	public function setInvalidHandler(\Closure $invalidHandler) {
		$this->invalidHandler = $invalidHandler;
	}

	public function setMessageResolver(MessageResolverInterface $messageResolver) {
		$this->messageResolver = $messageResolver;
	}

	public function getMessageResolver() {
		if (is_null($this->messageResolver)) {
			$this->messageResolver = new \Lemon\FormBuilder\MessageResolver;
		}
		return $this->messageResolver;
	}

	public function setValidatorResolver(ValidatorResolverInterface $validatorResolver) {
		$this->validatorResolver = $validatorResolver;
	}

	public function getValidatorResolver() {
		if (is_null($this->validatorResolver)) {
			$this->validatorResolver = new \Lemon\FormBuilder\ValidatorResolver;
		}
		return $this->validatorResolver;
	}
}
