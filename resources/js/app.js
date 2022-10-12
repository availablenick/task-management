require ("./bootstrap");

document.addEventListener("DOMContentLoaded", () => {
	let sidebar = document.querySelector(".custom-sidebar");
	let content = document.querySelector(".custom-content");
	let filler = document.querySelector(".custom-filler");

	const hideSidebar = () => {
		sidebar.style.display = "none";
		filler.style.display = "none";
		content.classList.add("col-12");
		content.classList.remove("col-10");
	};
	
	const showSidebar = () => {
		sidebar.style = null;
		filler.style = null;
		content.classList.add("col-10");
		content.classList.remove("col-12");
	}

	document.querySelector(".custom-hider").addEventListener("click", hideSidebar);
	document.querySelector(".custom-shower").addEventListener("click", showSidebar);
	let deleteButtons = document.querySelectorAll(".custom-btn-delete");
	for (let button of deleteButtons) {
		button.addEventListener("click", (event) => {
			event.preventDefault();
			let message = event.target.getAttribute("data-message");
			let shouldDelete = confirm(message);
			if (shouldDelete) {
				event.target.form.submit();
			}
		})
	}

	let alertBtn = document.querySelector(".custom-btn-alert");
	let alertPanel = document.querySelector(".custom-alert-panel");
	alertBtn.addEventListener("click", () => {
		let alertCount = document.querySelector(".custom-alert-count");
		if (alertCount) {
			alertCount.remove();
			axios.post("/assignment-alerts/note");
		}

		if (alertPanel) {
			if (alertPanel.style.display === "block") {
				alertPanel.style.display = "";
			} else {
				alertPanel.style.display = "block";
			}
		}
	});

	document.addEventListener("click", (event) => {
		if (!alertPanel.contains(event.target) && !alertBtn.contains(event.target)) {
			alertPanel.style.display = null;
		}
	});
});
