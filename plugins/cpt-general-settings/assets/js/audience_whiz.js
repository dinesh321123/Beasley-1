
(function ($) {
    var $document = $(document);
    $document.ready(function () {
		const elements = document.querySelectorAll(".audience-embed");

		for (let audiencei = 0, len = elements.length; audiencei < len; audiencei++) {
			const element = elements[audiencei];
			const extraAttributes = getParamsAudienceEmbed(element) ? getParamsAudienceEmbed(element) : {};
			const placeholder = document.createElement('div');
			placeholder.setAttribute(
				'id',
				extraAttributes.id || `__cd-${(audiencei + 1)}`,
			);
			placeholder.classList.add('placeholder');
			placeholder.classList.add(`placeholder-audience`);

			element.parentNode.replaceChild(placeholder, element);

			renderAudienceWidgetWhiz( placeholder.getAttribute('id'), extraAttributes );
		}
		stnvideorender();
    });
})(jQuery);

function getParamsAudienceEmbed(element) {
	const { dataset } = element;
	return {
		widgetid: dataset.widgetid,
	};
}

function renderAudienceWidgetWhiz(placeholder, dataset) {
	let widgetid = dataset.widgetid ? dataset.widgetid : '';
	const container = document.getElementById(placeholder);
	if (!container || !widgetid) {
		return;
	}
	const element = document.createElement('div');
	element.setAttribute('widget-id', widgetid);
	element.setAttribute('widget-type', 'app');
	element.setAttribute(
		'style',
		'background:#ffffff url(https://cdn2.aptivada.com/images/iframeLoader.gif) no-repeat center; min-height:500px;',
	);
	container.appendChild(element);
}