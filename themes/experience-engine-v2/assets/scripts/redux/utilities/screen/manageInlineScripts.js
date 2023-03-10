export default function manageInlineScripts(scriptArray) {
	if (scriptArray && scriptArray.length > 0) {
		scriptArray.forEach(inlineScript => {
			try {
				// eslint-disable-next-line no-eval
				eval(inlineScript.replace(`\\`, `\\\\`));
			} catch {
				console.error(`ERROR RUNNING INLINE SCRIPT -> '${inlineScript}'`);
			}
		});
	}
}
