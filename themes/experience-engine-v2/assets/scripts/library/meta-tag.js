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
}

export function getCanonicalUrl() {
	return getFirstMatchingElementAttribute(
		document.head,
		'link',
		'rel',
		'canonical',
		'href',
	);
}
