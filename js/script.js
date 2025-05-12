document.addEventListener('DOMContentLoaded', function() {
    // Navigation functionality
    const navLinks = document.querySelectorAll('.nav-link:not([href="login.php"]):not([href="logout.php"])');
    const contentSections = document.querySelectorAll('.content-section');
    
    // Function to switch sections
    function switchSection(sectionId) {
        // Update navigation
        navLinks.forEach(link => {
            if (link.dataset.section === sectionId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
        
        // Update content
        contentSections.forEach(section => {
            if (section.id === `${sectionId}-section`) {
                section.classList.add('active');
            } else {
                section.classList.remove('active');
            }
        });
    }
    
    // Add click event to nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.dataset.section;
            switchSection(sectionId);
        });
    });
    
    // Handle back/forward buttons
    window.addEventListener('popstate', function() {
        checkHash();
    });
    
    // Initialize
    checkHash();
    
    // Gallery modal functionality
    const modal = document.getElementById('gallery-modal');
    const modalImg = document.getElementById('modal-image');
    const galleryItems = document.querySelectorAll('.gallery-item img');
    const closeBtn = document.querySelector('.close');
    
    if (modal && modalImg && galleryItems.length > 0) {
        galleryItems.forEach(item => {
            item.addEventListener('click', function() {
                modal.style.display = 'block';
                modalImg.src = this.src;
            });
        });
        
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});