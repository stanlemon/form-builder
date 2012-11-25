<?php
namespace Lemon\FormBuilder\Element;

use Lemon\FormBuilder\AbstractElement;

class Select extends AbstractElement {

	public function setValue($value) {
		foreach ($this->dom->childNodes as $child) {
			if ($child->getAttribute('value') == $value) {
				$child->setAttribute('selected', 'selected');
			}
		}
	}
}
