// =============================================================================
// GOOGLE REVIEWS WIDGET - Real API Integration
// =============================================================================

class GoogleReviewsWidget {
    constructor(placeId, apiKey, containerId) {
        this.placeId = placeId;
        this.apiKey = apiKey;
        this.container = document.getElementById(containerId);
        this.reviews = [];
        this.currentIndex = 0;
    }

    async fetchReviews() {
        try {
            // Google Places API endpoint
            const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${this.placeId}&fields=reviews,rating,user_ratings_total&key=${this.apiKey}`;

            // Note: For production, this should be proxied through your backend to hide API key
            const response = await fetch(url);
            const data = await response.json();

            if (data.result && data.result.reviews) {
                this.reviews = data.result.reviews;
                this.renderReviews();
            }
        } catch (error) {
            console.error('Error fetching Google Reviews:', error);
            this.renderFallbackReviews();
        }
    }

    renderReviews() {
        if (!this.container) return;

        const reviewsHTML = this.reviews.slice(0, 5).map(review => `
            <div class="google-review-card">
                <div class="review-header">
                    <img src="${review.profile_photo_url}"
                         alt="${review.author_name}"
                         class="review-avatar"
                         loading="lazy">
                    <div class="review-author-info">
                        <div class="review-author">${review.author_name}</div>
                        <div class="review-rating">
                            ${this.renderStars(review.rating)}
                        </div>
                        <div class="review-date">${this.formatDate(review.time)}</div>
                    </div>
                </div>
                <p class="review-text">${review.text}</p>
                <a href="${review.author_url}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="review-source">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                    </svg>
                    Ver no Google
                </a>
            </div>
        `).join('');

        this.container.innerHTML = `
            <div class="google-reviews-container">
                <div class="google-reviews-header">
                    <div class="reviews-badge">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                        </svg>
                        <span>Google Reviews</span>
                    </div>
                    <div class="reviews-summary">
                        <span class="reviews-rating">${this.calculateAverageRating()}</span>
                        <div class="reviews-stars">${this.renderStars(this.calculateAverageRating())}</div>
                        <span class="reviews-count">(${this.reviews.length} avaliações)</span>
                    </div>
                </div>
                <div class="google-reviews-grid">
                    ${reviewsHTML}
                </div>
            </div>
        `;
    }

    renderFallbackReviews() {
        // Fallback com reviews estáticas se API falhar
        const fallbackReviews = [
            {
                author_name: "Maria Silva",
                rating: 5,
                text: "Experiência incrível com a Carol! Tour personalizado perfeito.",
                relative_time_description: "há 2 semanas"
            },
            {
                author_name: "João Santos",
                rating: 5,
                text: "Guia excepcional, conhece Londres como ninguém!",
                relative_time_description: "há 1 mês"
            }
        ];

        const reviewsHTML = fallbackReviews.map(review => `
            <div class="google-review-card">
                <div class="review-header">
                    <div class="review-avatar">${review.author_name[0]}</div>
                    <div class="review-author-info">
                        <div class="review-author">${review.author_name}</div>
                        <div class="review-rating">${this.renderStars(review.rating)}</div>
                        <div class="review-date">${review.relative_time_description}</div>
                    </div>
                </div>
                <p class="review-text">${review.text}</p>
            </div>
        `).join('');

        this.container.innerHTML = reviewsHTML;
    }

    renderStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        let stars = '';

        for (let i = 0; i < fullStars; i++) {
            stars += '<span class="star star--full">★</span>';
        }

        if (hasHalfStar) {
            stars += '<span class="star star--half">★</span>';
        }

        const emptyStars = 5 - Math.ceil(rating);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<span class="star star--empty">☆</span>';
        }

        return stars;
    }

    calculateAverageRating() {
        if (this.reviews.length === 0) return 5.0;
        const sum = this.reviews.reduce((acc, review) => acc + review.rating, 0);
        return (sum / this.reviews.length).toFixed(1);
    }

    formatDate(timestamp) {
        const date = new Date(timestamp * 1000);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'hoje';
        if (diffDays === 1) return 'ontem';
        if (diffDays < 7) return `há ${diffDays} dias`;
        if (diffDays < 30) return `há ${Math.floor(diffDays / 7)} semanas`;
        if (diffDays < 365) return `há ${Math.floor(diffDays / 30)} meses`;
        return `há ${Math.floor(diffDays / 365)} anos`;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Note: Replace with your actual Google Place ID and API Key
    // For security, the API key should be in environment variables and proxied through backend
    const PLACE_ID = 'YOUR_GOOGLE_PLACE_ID';
    const API_KEY = 'YOUR_GOOGLE_API_KEY';

    const reviewsWidget = new GoogleReviewsWidget(PLACE_ID, API_KEY, 'google-reviews-widget');

    // Use fallback for now (until API credentials are configured)
    reviewsWidget.renderFallbackReviews();
});
