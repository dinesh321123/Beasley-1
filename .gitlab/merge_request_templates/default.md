# Merge Request QA Checklist

Fill out the sections relevant to your merge request. Use your best judgement on which sections are applicable. If unsure, reach out to your Director of Engineering.

## Functional Testing & Documentation
- [ ] This code has passed all automated testing including [PHPCS](https://github.com/10up/phpcs-composer) and [JavaScript Linting](https://www.npmjs.com/package/@10up/eslint-config)
- [ ] My code follows the 10up Engineering Best Practices (or exceptions are listed below):
- [ ] The functionality of this feature follows to the relevant section of the Functional Requirements document. If this feature doesn’t match it’s requirements, I’ve outlined why.
- [ ] This feature has all the proper inline documentation.

## Visual Testing
- [ ] This feature has been compared to and matches the design at all provided breakpoints. If this feature doesn’t match the design, I’ve outlined why.
- [ ]  This feature has been tested in all browsers that this project supports.
	- [ ] Chrome, latest
	- [ ] Firefox, latest
	- [ ] Safari, latest
	- [ ] Edge
	- [ ] IE 11

## Accessibility Testing
- [ ] This feature passes [WCAG 2.1 Level A](https://www.w3.org/WAI/WCAG21/quickref/) compliance    
- [ ] This feature is fully functional without the use of a mouse.
- [ ] This feature has all the correct HTML and alternative text (including screen reader text).
