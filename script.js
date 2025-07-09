
// Automatically highlight current page (add this to every page)
document.addEventListener('DOMContentLoaded', function() {
  const currentPage = window.location.pathname.split('/').pop();
  const links = document.querySelectorAll('.nav-links a');
  
  links.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('current-page');
    }
  });
});

