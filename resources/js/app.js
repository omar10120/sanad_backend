import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Modern Scrollbar Enhancement for Tailwind Layouts
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced scrollbar functionality for Tailwind layouts
    function enhanceTailwindScrollbars() {
        // Add smooth scroll behavior to all scrollable elements
        const scrollableElements = document.querySelectorAll('*');
        
        scrollableElements.forEach(element => {
            // Check if element is scrollable
            if (element.scrollHeight > element.clientHeight || 
                element.scrollWidth > element.clientWidth) {
                
                // Add smooth scrolling
                element.style.scrollBehavior = 'smooth';
                
                // Add custom class for enhanced styling
                element.classList.add('enhanced-scrollbar');
                
                // Add scroll event listener for dynamic styling
                element.addEventListener('scroll', function() {
                    this.classList.add('scrolling');
                    clearTimeout(this.scrollTimeout);
                    this.scrollTimeout = setTimeout(() => {
                        this.classList.remove('scrolling');
                    }, 150);
                });
            }
        });
        
        // Enhanced scrollbar for Tailwind specific containers
        const tailwindContainers = [
            '.min-h-screen',
            '.overflow-y-auto',
            '.overflow-x-auto',
            '.overflow-auto',
            '.max-h-screen',
            '.h-screen'
        ];
        
        tailwindContainers.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                if (element) {
                    element.classList.add('custom-scrollbar');
                    
                    // Add scroll indicator for long content
                    if (element.scrollHeight > element.clientHeight + 50) {
                        element.classList.add('has-scroll');
                    }
                }
            });
        });
    }
    
    // Initialize on page load
    enhanceTailwindScrollbars();
    
    // Re-initialize on dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(enhanceTailwindScrollbars, 100);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Add scroll-to-top functionality for Tailwind layouts
    let scrollToTopButton = document.getElementById('back-to-top');
    if (!scrollToTopButton) {
        scrollToTopButton = document.createElement('button');
        scrollToTopButton.id = 'back-to-top';
        scrollToTopButton.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        `;
        scrollToTopButton.className = 'fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 opacity-0 invisible -translate-y-4 z-50 flex items-center justify-center';
        document.body.appendChild(scrollToTopButton);
    }
    
    // Show/hide scroll-to-top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopButton.classList.remove('opacity-0', 'invisible', '-translate-y-4');
            scrollToTopButton.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            scrollToTopButton.classList.add('opacity-0', 'invisible', '-translate-y-4');
            scrollToTopButton.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });
    
    // Smooth scroll to top
    scrollToTopButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
