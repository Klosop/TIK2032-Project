document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link');
    const headerTitle = document.getElementById('header-title');
    const headerSubtitle = document.getElementById('header-subtitle');
    const pages = document.querySelectorAll('.page');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Remove active class from all links and pages
            navLinks.forEach(link => link.classList.remove('active'));
            pages.forEach(page => page.classList.remove('active'));

            // Add active class to the clicked link and corresponding page
            this.classList.add('active');
            const targetPage = document.getElementById(this.getAttribute('data-page'));
            targetPage.classList.add('active');

            // Update the header title and subtitle
            const sectionHeader = targetPage.querySelector('.section-header');
            if (sectionHeader) {
                const sectionTitle = sectionHeader.querySelector('.section-title').textContent;
                const sectionSubtitle = sectionHeader.querySelector('.section-subtitle').textContent;
                headerTitle.textContent = sectionTitle;
                headerSubtitle.textContent = sectionSubtitle;
            }
        });
    });

    // Gallery Popup Functionality
    const galleryItems = document.querySelectorAll('.gallery-item');
    const popup = document.getElementById('image-popup');
    const popupImage = document.getElementById('popup-image');
    const closeBtn = document.querySelector('.close');
    const overlay = document.getElementById('overlay'); // Get the overlay

    galleryItems.forEach(item => {
        item.addEventListener('click', function () {
            popup.style.display = 'block';
            overlay.style.display = 'block'; // Show the overlay
            popupImage.src = this.querySelector('img').src;
        });
    });

    closeBtn.addEventListener('click', function () {
        popup.style.display = 'none';
        overlay.style.display = 'none'; // Hide the overlay
    });

    // Close the popup if the user clicks outside the image
    window.addEventListener('click', function (event) {
        if (event.target === popup) {
            popup.style.display = 'none';
            overlay.style.display = 'none'; // Hide the overlay
        }
    });
});