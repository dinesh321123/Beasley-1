export function updateElementAttribute(
	elementType,
	attributeName,
	attributeValue,
	valueName,
	newValue,
) {
	const allElements = document.getElementsByTagName(elementType);

	for (let i = 0; i < allElements.length; i++) {
		if (allElements[i].getAttribute(attributeName) === attributeValue) {
			allElements[i].setAttribute(valueName, newValue);
			break;
		}
	}
}

export function upsertElementAttribute(
	parentElement,
	elementType,
	attributeName,
	attributeValue,
	valueName,
	newValue,
) {
	const allElements = parentElement.getElementsByTagName(elementType);

	for (let i = 0; i < allElements.length; i++) {
		if (allElements[i].getAttribute(attributeName) === attributeValue) {
			allElements[i].setAttribute(valueName, newValue);
			return; // Exit Function Because Updated
		}
	}

	// Insert New Element
	const newElement = document.createElement(elementType);
	newElement.setAttribute(attributeName, attributeValue);
	newElement.setAttribute(valueName, newValue);
	parentElement.appendChild(newElement);
}

function getFirstMatchingElementAttribute(
	parentElement,
	elementType,
	attributeName,
	attributeValue,
	valueName,
) {
	const allElements = parentElement.getElementsByTagName(elementType);
	console.log(
		`getFirstMatchingElementAttribute() - ${allElements.length} ${elementType} elements `,
	);

	for (let i = 0; i < allElements.length; i++) {
		allElements[i].attributes.forEach(elAttr =>
			console.log(
				`getFirstMatchingElementAttribute() - Element${i} - ${elAttr.name} -> ${elAttr.value}`,
			),
		);

		if (allElements[i].getAttribute(attributeName) === attributeValue) {
			console.log(
				`getFirstMatchingElementAttribute() - found attribute of ${attributeName}=${attributeValue} and value ${allElements[
					i
				].getAttribute(valueName)}`,
			);
			return allElements[i].getAttribute(valueName);
		}
	}

	return null;
}

export function updateMetaPropertyValue(name, value) {
	updateElementAttribute('meta', 'property', name, 'content', value);
}

export function updateLinkRelHref(name, value) {
	updateElementAttribute('link', 'rel', name, 'href', value);
}

export function updateCanonicalUrl(url) {
	updateMetaPropertyValue('og:url', url);
	updateLinkRelHref('canonical', url);
	upsertElementAttribute(
		document.head,
		'link',
		'rel',
		'beasley-canonical',
		'href',
		url,
	);
}

export function getBeasleyCanonicalUrl() {
	return getFirstMatchingElementAttribute(
		document.head,
		'link',
		'rel',
		'beasley-canonical',
		'href',
	);
}
