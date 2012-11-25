<?php
namespace Lemon\FormBuilder\Element;

use Lemon\FormBuilder\AbstractElement;

class Textarea extends AbstractElement {

	public function setValue($value) {
		$this->dom->appendChild(new \DOMText($value));
	}
}
