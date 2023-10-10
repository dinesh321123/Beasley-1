/**
 * This script handles the behavior of navigation tabs and their associated tab contents.
 * It ensures that clicking on a navigation tab displays the corresponding content
 * and highlights the active tab. Additionally, when the "All Settings" tab is clicked,
 * all tab contents are displayed. This script is meant to be executed after the
 * DOM (Document Object Model) is fully loaded and ready.
 */

document.addEventListener("DOMContentLoaded", function() {
	// Select all navigation tabs and tab contents using CSS selectors.
	var navTabs = document.querySelectorAll('.nav-tab');
	var tabContents = document.querySelectorAll('.tab-content');

	// Select the first navigation tab. This will serve as the "All Settings" tab.
	var firstNavTab = document.querySelector('.nav-tab:first-child');

	// Function to set a specific tab as the active tab and deactivate others.
	function setActiveTab(tab) {
		// Iterate through all navigation tabs to remove the 'nav-tab-active' class.
		navTabs.forEach(function(item) {
			item.classList.remove('nav-tab-active');
		});

		// Add the 'nav-tab-active' class to the currently clicked tab.
		tab.classList.add('nav-tab-active');
	}

	// Function to display a specific tab's content and hide others.
	function showTabContent(contentId) {
		// Iterate through all tab contents to hide them.
		tabContents.forEach(function(content) {
			content.style.display = 'none';
		});

		// Display the content associated with the given content ID.
		document.querySelector(contentId).style.display = 'block';
	}

	// Attach a click event listener to each navigation tab.
	navTabs.forEach(function(tab) {
		tab.addEventListener('click', function(event) {
			event.preventDefault();

			// If the clicked tab is the "All Settings" tab.
			if (tab === firstNavTab) {
				// Show all tab contents by setting their display to 'block'.
				tabContents.forEach(function(content) {
					content.style.display = 'block';
				});
			} else {
				// Otherwise, show the content associated with the clicked tab.
				var tabHref = tab.getAttribute('href');
				showTabContent(tabHref);
			}

			// Set the clicked tab as the active tab.
			setActiveTab(tab);
		});
	});

	// Show all tab contents when the page loads initially.
	tabContents.forEach(function(content) {
		content.style.display = 'block';
	});

	// Set the initial active tab to the first navigation tab ("All Settings").
	setActiveTab(firstNavTab);
});
