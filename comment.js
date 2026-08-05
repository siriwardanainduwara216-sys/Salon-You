function initCommentSlider() {
    const track = document.getElementById("commentTrack");
    const cards = document.querySelectorAll(".comment-card");
    const wrapper = document.querySelector(".comment-slider-wrapper");

    if (!track || cards.length === 0) {
        return;
    }

    let currentIndex = 0;

    function shiftNext() {
        currentIndex++;
        if (currentIndex >= cards.length) {
            currentIndex = 0; // Loop back to start
        }

        const cardWidth = cards[0].getBoundingClientRect().width;
        const gap = 24; // 1.5rem gap between cards
        const moveAmount = (cardWidth + gap) * currentIndex;

        track.style.transform = `translateX(-${moveAmount}px)`;
    }

    // Auto shift every 3 seconds
    let autoSlide = setInterval(shiftNext, 3000);

    // Pause on mouse hover
    if (wrapper) {
        wrapper.addEventListener("mouseenter", () => clearInterval(autoSlide));
        wrapper.addEventListener("mouseleave", () => {
            clearInterval(autoSlide);
            autoSlide = setInterval(shiftNext, 3000);
        });
    }
}

// Ensure execution regardless of script load timing
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCommentSlider);
} else {
    initCommentSlider();
}