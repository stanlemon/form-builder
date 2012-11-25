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
	protected $errorPlacement;
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
			$value = (!isset($input[$element->getName()])) ? null : $input[$element->getName()];
	
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

		if (is_callable($this->errorPlacement)) {
			call_user_func($this->errorPlacement, $element);
		}
	}

	public function render() {
		if ($this->isProcessed() && $this->hasInvalid()) {
			foreach ($this->getElements() as $element) {
				$element->setValue($this->processed[$element->getName()]);

				if (in_array($element, $this->getInvalid())) {
					$this->handleError($element);
				}
			}
		}
		return $this->dom->saveHTML();
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

	public function setErrorPlacement(\Closure $errorPlacement) {
		$this->errorPlacement = $errorPlacement;
	}

	public function getMessageResolver() {
		if (is_null($this->messageResolver)) {
			$this->messageResolver = new \Lemon\FormBuilder\MessageResolver;
		}
		return $this->messageResolver;
	}
}
