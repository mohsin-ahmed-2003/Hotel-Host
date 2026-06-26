document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation for dynamic AJAX-loaded slider buttons
    document.body.addEventListener('click', function(e) {
        const prevBtn = e.target.closest('.prev-rooms-btn');
        const nextBtn = e.target.closest('.next-rooms-btn');
        
        if (prevBtn || nextBtn) {
            e.preventDefault();
            
            // Find the closest section container, then the grid inside it
            const section = (prevBtn || nextBtn).closest('section');
            if (!section) return;
            
            const grid = section.querySelector('.rooms-grid');
            if (!grid) return;
            
            // Calculate scroll amount based on card width + gap
            // Using a generic 300px approximation since it's dynamic calc() now, 
            // but we can just use the actual clientWidth of the first card + gap
            const firstCard = grid.querySelector('.room-card');
            if (!firstCard) return;
            
            const scrollAmount = firstCard.clientWidth + 24; // Card width + gap
            
            if (prevBtn) {
                grid.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else if (nextBtn) {
                grid.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }
    });
});
