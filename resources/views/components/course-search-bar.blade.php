<style>
.course-search-bar .input-group-text {
    border: 1px solid #dee2e6;
}

.course-search-bar .form-control {
    border: 1px solid #dee2e6;
}

.course-search-bar .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

#courseSearchResults {
    list-style: none;
    padding: 0;
    margin: 0;
    /* Responsive width: 90% on mobile, 60% on desktop */
    width: 90% !important;
    max-width: 90vw !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 50 !important;
}

@media (min-width: 768px) {
    #courseSearchResults {
        width: 75% !important;
        max-width: 75vw !important;
    }
}

@media (min-width: 992px) {
    #courseSearchResults {
        width: 60% !important;
        max-width: 60vw !important;
    }
}

.search-result-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: block;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background-color: #f8f9fa;
    padding-left: 20px;
}

.search-result-item .course-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.search-result-item .course-subtitle {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-bottom: 4px;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.search-result-item .course-category {
    font-size: 0.75rem;
    display: inline-block;
    background-color: #e8f4f8;
    color: #0c5460;
    padding: 2px 8px;
    border-radius: 4px;
    margin-right: 8px;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 100%;
}

.search-result-item .course-price {
    font-weight: 700;
    color: #27ae60;
    font-size: 0.9rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.search-no-results {
    padding: 24px 16px;
    text-align: center;
    color: #7f8c8d;
    word-wrap: break-word;
}

.search-loading {
    padding: 12px 16px;
    text-align: center;
    color: #7f8c8d;
}
</style>
<!-- Course Search Bar Component -->
<div class="course-search-bar mb-4" data-aos="fade-up" style="overflow: visible !important;">
    <div class="position-relative" style="z-index: auto; overflow: visible;">
        <div class="input-group input-group-lg shadow-sm" style="overflow: visible;">
            <span class="input-group-text bg-white border-end-0">
                <i class="fa fa-search text-primary"></i>
            </span>
            <input 
                type="text" 
                class="form-control border-start-0 rounded-end" 
                id="courseSearchInput"
                placeholder="Search courses by title, topic, or keyword..."
                autocomplete="off"
                data-category-id="{{ $categoryId ?? '' }}"
            >
        </div>

        <!-- Search Results Dropdown -->
        <div 
            class="position-absolute mt-2 bg-white rounded-3 shadow-lg d-none" 
            id="courseSearchResults"
            style="top: 100%; left: 0; z-index: 50; max-height: none; overflow: visible; width: 100%;"
        >
            <!-- Results will be populated here -->
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('courseSearchInput');
    const resultsContainer = document.getElementById('courseSearchResults');
    const categoryId = searchInput?.getAttribute('data-category-id');
    let searchTimeout;

    if (!searchInput) return;

    // Handle input event with debouncing
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsContainer.classList.add('d-none');
            return;
        }

        // Show loading state
        resultsContainer.innerHTML = '<div class="search-loading"><i class="spinner-border spinner-border-sm"></i> Searching...</div>';
        resultsContainer.classList.remove('d-none');

        searchTimeout = setTimeout(function() {
            let searchUrl = `{{ route('courses.search') }}?q=${encodeURIComponent(query)}`;
            if (categoryId) {
                searchUrl += `&category_id=${categoryId}`;
            }

            fetch(searchUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        resultsContainer.innerHTML = data.data.map(course => `
                            <a href="${course.url}" class="search-result-item">
                                <div class="d-flex gap-3">
                                    ${course.featured_image ? `
                                        <img src="${course.featured_image}" alt="${course.title}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                    ` : `
                                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">📚</div>
                                    `}
                                    <div style="flex: 1;">
                                        <div class="course-title">${course.title}</div>
                                        ${course.subtitle ? `<div class="course-subtitle">${course.subtitle}</div>` : ''}
                                        <div>
                                            ${course.category ? `<span class="course-category">${course.category}</span>` : ''}
                                            <span class="course-price">₦${new Intl.NumberFormat('en-NG').format(course.fee)}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        `).join('');
                    } else {
                        resultsContainer.innerHTML = '<div class="search-no-results">No courses found matching your search</div>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    resultsContainer.innerHTML = '<div class="search-no-results">Error searching courses</div>';
                });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.course-search-bar')) {
            resultsContainer.classList.add('d-none');
        }
    });

    // Show results on focus
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && !resultsContainer.classList.contains('d-none')) {
            resultsContainer.classList.remove('d-none');
        }
    });
});
</script>
