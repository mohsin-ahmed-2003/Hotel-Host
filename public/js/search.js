document.addEventListener('DOMContentLoaded', function() {
    // Show More Amenities Logic
    const showMoreBtn = document.getElementById('showMoreAmenities');
    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', function() {
            const hiddenAmenities = document.querySelectorAll('.hidden-amenity');
            const isHidden = hiddenAmenities[0].classList.contains('d-none');
            
            hiddenAmenities.forEach(el => {
                if (isHidden) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });
            
            if (isHidden) {
                this.innerHTML = 'Show less <i class="fas fa-chevron-up ms-1"></i>';
            } else {
                this.innerHTML = 'Show more <i class="fas fa-chevron-down ms-1"></i>';
            }
        });
    }

    // Auto-submit form when inputs change (optional, but good for UX)
    const searchForm = document.getElementById('searchFiltersForm');
    // Removed auto-submit logic because filters are now inside an offcanvas 
    // and should only submit when the user clicks 'Show properties'

    // Custom Offcanvas Logic
    const filterBtn = document.querySelector('.filters-modal-btn');
    const offcanvas = document.getElementById('searchFiltersOffcanvas');
    const closeBtn = document.querySelector('.btn-close-custom');
    
    if (offcanvas && filterBtn) {
        let backdrop = document.querySelector('.offcanvas-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'offcanvas-backdrop';
            document.body.appendChild(backdrop);
        }

        function openOffcanvas() {
            offcanvas.classList.add('show');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeOffcanvas() {
            offcanvas.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        filterBtn.addEventListener('click', openOffcanvas);
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeOffcanvas);
        }
        
        
        backdrop.addEventListener('click', closeOffcanvas);
    }
    
    // ==========================================
    // Room Card Slider & Wishlist Logic
    // ==========================================
    const wishlist = JSON.parse(localStorage.getItem('user_wishlist') || '{}');
    Object.keys(wishlist).forEach(roomId => {
        if (wishlist[roomId]) {
            const buttons = document.querySelectorAll(`.wishlist-btn-room-${roomId}`);
            buttons.forEach(btn => {
                btn.classList.add('active');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'fas fa-heart';
            });
        }
    });

    window.toggleWishlist = function(event, roomId) {
        event.preventDefault();
        event.stopPropagation();
        
        const btn = event.currentTarget;
        const icon = btn.querySelector('i');
        const isActive = btn.classList.contains('active');
        
        let wishlist = JSON.parse(localStorage.getItem('user_wishlist') || '{}');
        
        if (isActive) {
            btn.classList.remove('active');
            if (icon) icon.className = 'far fa-heart';
            wishlist[roomId] = false;
        } else {
            btn.classList.add('active');
            if (icon) icon.className = 'fas fa-heart';
            wishlist[roomId] = true;
        }
        
        localStorage.setItem('user_wishlist', JSON.stringify(wishlist));
    };

    window.nextSlide = function(event) {
        event.preventDefault();
        event.stopPropagation();
        const container = event.currentTarget.parentElement.querySelector('.slides-container');
        const slides = container.querySelectorAll('.slide-img');
        if (slides.length <= 1) return;
        
        let activeIndex = 0;
        slides.forEach((slide, index) => {
            if (slide.classList.contains('active')) {
                activeIndex = index;
                slide.classList.remove('active');
            }
        });
        
        let nextIndex = (activeIndex + 1) % slides.length;
        slides[nextIndex].classList.add('active');
    };

    window.prevSlide = function(event) {
        event.preventDefault();
        event.stopPropagation();
        const container = event.currentTarget.parentElement.querySelector('.slides-container');
        const slides = container.querySelectorAll('.slide-img');
        if (slides.length <= 1) return;
        
        let activeIndex = 0;
        slides.forEach((slide, index) => {
            if (slide.classList.contains('active')) {
                activeIndex = index;
                slide.classList.remove('active');
            }
        });
        
        let prevIndex = (activeIndex - 1 + slides.length) % slides.length;
        slides[prevIndex].classList.add('active');
    };
});
