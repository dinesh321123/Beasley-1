export default function handleInjectos() {
	const injectos = document.getElementsByClassName('injecto');

	for (let i = 0; i < injectos.length; i++) {
		const target = document.getElementsByClassName(injectos[i].dataset.target);
		if (target.length > 0) {
			if (injectos[i].dataset.placement === 'before') {
				target[0].prepend(...injectos[0].childNodes);
			} else {
				target[0].append(...injectos[0].childNodes);
			}
		}
	}
}
