
$(document).ready(function () {
    let isShifted = false;

    $('#burgerButton').click(function () {
        const sidebar = document.getElementById("sidebar");

        if (!sidebar) {
            console.warn("Element with ID 'sidebar' not found.");
            return;
        }

        // Apply styles only once if needed
        sidebar.style.display = 'flex';
        sidebar.style.flexDirection = 'column';
        sidebar.style.transition = 'transform 0.45s cubic-bezier(.4,0,.2,1)';
        sidebar.style.willChange = 'transform';

        // Toggle visibility
        if (!isShifted) {
            // Bring into view
            sidebar.style.transform = 'translateX(0)';
        } else {
            // Move off screen to the left
            sidebar.style.transform = 'translateX(-50rem)';
        }

        isShifted = !isShifted;
    });
});
window.addEventListener('load', function () {
  const cards = document.querySelectorAll('.booksAvailable');
  
  cards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add('books-animate');
    }, index * 200); // stagger animations
  });
});

