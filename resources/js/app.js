document.addEventListener("DOMContentLoaded", () => {
	$sidebar = document.querySelector(".custom-sidebar");
	$content = document.querySelector(".custom-content");
	$filler = document.querySelector(".custom-filler");

	const hideSidebar = () => {
		$sidebar.style.display = "none";
		$filler.style.display = "none";
		$content.classList.add("col-12");
		$content.classList.remove("col-10");
	};
	
	const showSidebar = () => {
		$sidebar.style = null;
		$filler.style = null;
		$content.classList.add("col-10");
		$content.classList.remove("col-12");
	}
	
	document.querySelector(".custom-hider").addEventListener("click", hideSidebar);
	document.querySelector(".custom-shower").addEventListener("click", showSidebar);
});
