document.addEventListener("DOMContentLoaded", () => {
  const addThesisBtn = document.getElementById("addThesisBtn");
  const cancelBtn = document.getElementById("cancelFormBtn");
  const thesisOverview = document.getElementById("thesisOverview");
  const addThesisFormSection = document.getElementById("addThesisFormSection");

  // When clicking "Add New Thesis"
addThesisBtn.addEventListener("click", () => {
  thesisOverview.classList.add("fade-out");
  setTimeout(() => {
    thesisOverview.classList.add("hidden");
    addThesisFormSection.classList.remove("hidden", "fade-out"); // <-- remove fade-out
    addThesisFormSection.classList.add("fade-in");
  }, 300);
});


  // When clicking "Cancel"
cancelBtn.addEventListener("click", () => {
  addThesisFormSection.classList.add("fade-out");
  setTimeout(() => {
    addThesisFormSection.classList.add("hidden");
    thesisOverview.classList.remove("hidden", "fade-out");
    thesisOverview.classList.add("fade-in");
  }, 300);
});



});
document.addEventListener("DOMContentLoaded", () => {
  const dropdownBtn = document.getElementById("filterDropdownBtn");
  const dropdownMenu = document.getElementById("filterMenu");

  // Toggle dropdown visibility
  dropdownBtn.addEventListener("click", () => {
    dropdownMenu.classList.toggle("hidden");
  });

  // Change button text when selecting an item
  dropdownMenu.querySelectorAll("li").forEach(item => {
    item.addEventListener("click", () => {
      dropdownBtn.innerHTML = `${item.textContent} <span class="arrow">▼</span>`;
      dropdownMenu.classList.add("hidden");
    });
  });

  // Click outside to close
  document.addEventListener("click", (e) => {
    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.add("hidden");
    }
  });
});
document.addEventListener("DOMContentLoaded", () => {
  const currentPage = window.location.pathname.split("/").pop(); // get filename (e.g. "manage-thesis.html")
  const navLinks = document.querySelectorAll(".sidebar nav a");

  navLinks.forEach(link => {
    const linkPage = link.getAttribute("href");

    if (linkPage === currentPage) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
});