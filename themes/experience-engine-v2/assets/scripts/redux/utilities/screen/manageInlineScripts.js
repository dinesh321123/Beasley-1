export default function manageInlineScripts(scriptArray) {
	if (scriptArray && scriptArray.length > 0) {
		// eslint-disable-next-line no-eval
		scriptArray.forEach(s => eval(s.replace(`\\`, `\\\\`)));
	}
}
