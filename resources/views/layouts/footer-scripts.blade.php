<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/plugins/jquery/jquery.min.js')}}"></script>
{{--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
{{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>--}}

<!-- Ionicons js -->
<script src="{{URL::asset('assets/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/plugins/rating/jquery.barrating.js')}}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/p-scroll.js')}}"></script>
<!--Internal Sparkline js -->
<script src="{{URL::asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
{{-- <!-- right-sidebar js -->
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-rtl.js')}}"></script>
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-custom.js')}}"></script> --}}
<!-- Eva-icons js -->
<script src="{{URL::asset('assets/js/eva-icons.min.js')}}"></script>
@yield('js')
<!-- Sticky js -->
<script src="{{URL::asset('assets/js/sticky.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/js/custom.js')}}"></script>
<!-- Left-menu js-->
<script src="{{URL::asset('assets/plugins/side-menu/sidemenu.js')}}"></script>

<!-- Modern Scrollbar Enhancement Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced scrollbar functionality
    function enhanceScrollbars() {
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
        
        // Enhanced scrollbar for specific containers
        const containers = [
            '.main-content',
            '.app-content', 
            '.content',
            '.sidebar',
            '.main-sidebar',
            '.app-sidebar',
            '.modal-body',
            '.dropdown-menu'
        ];
        
        containers.forEach(selector => {
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
    enhanceScrollbars();
    
    // Re-initialize on dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(enhanceScrollbars, 100);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Add scroll-to-top functionality
    let scrollToTopButton = document.getElementById('back-to-top');
    if (!scrollToTopButton) {
        scrollToTopButton = document.createElement('a');
        scrollToTopButton.href = '#top';
        scrollToTopButton.id = 'back-to-top';
        scrollToTopButton.innerHTML = '<i class="las la-angle-double-up"></i>';
        scrollToTopButton.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
        `;
        document.body.appendChild(scrollToTopButton);
    }
    
    // Show/hide scroll-to-top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopButton.style.opacity = '1';
            scrollToTopButton.style.visibility = 'visible';
            scrollToTopButton.style.transform = 'translateY(0)';
        } else {
            scrollToTopButton.style.opacity = '0';
            scrollToTopButton.style.visibility = 'hidden';
            scrollToTopButton.style.transform = 'translateY(20px)';
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
    
    // Add hover effects
    scrollToTopButton.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.1)';
        this.style.boxShadow = '0 6px 20px rgba(102, 126, 234, 0.5)';
    });
    
    scrollToTopButton.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
        this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.3)';
    });
});
</script>

<!-- Additional CSS for enhanced scrollbars -->
<style>
.enhanced-scrollbar {
    transition: all 0.3s ease;
}

.enhanced-scrollbar.scrolling {
    transform: translateZ(0);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: content-box;
    transition: all 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: scale(1.1);
}

.has-scroll::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 20px;
    background: linear-gradient(transparent, rgba(0,0,0,0.1));
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.has-scroll:hover::after {
    opacity: 1;
}
</style>
