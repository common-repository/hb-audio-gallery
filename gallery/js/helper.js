const openMenu = (event) => {
  closeDropdowns();
  event.nextElementSibling.classList.toggle("show");
};

// Close the dropdown menu if the user clicks outside of it
window.onclick = function (event) {
  if (!event.target.matches('.optionbtn')) {
    closeDropdowns();
  }
};

const closeDropdowns = () => {
  var dropdowns = document.getElementsByClassName("option-content");
  var i;
  for (i = 0; i < dropdowns.length; i++) {
    var openDropdown = dropdowns[i];
    if (openDropdown.classList.contains("show")) {
      openDropdown.classList.remove("show");
    }
  }
};

const openLink = (e) => {
  window.location.assign(e)
}