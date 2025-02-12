function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active'); // Toggles the display of the nav-links
}

document.addEventListener("DOMContentLoaded", function() {
    const yearSpan = document.getElementById("current-year");
    yearSpan.textContent = new Date().getFullYear();
});