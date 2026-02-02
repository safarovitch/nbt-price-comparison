<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useTrans } from '@/composables/useTrans';
import { Search } from 'lucide-vue-next';

const { __ } = useTrans();
const searchQuery = ref('');
const isFocused = ref(false);
const showResults = ref(false);

const results = ref([
    { title: __('Consumer Loans'), category: __('Credits'), icon: 'fa-solid fa-hand-holding-dollar' },
    { title: __('Car Loans'), category: __('Credits'), icon: 'fa-solid fa-car' },
    { title: __('Deposits'), category: __('Deposits'), icon: 'fa-solid fa-vault' },
    { title: __('Insurance'), category: __('Services'), icon: 'fa-solid fa-user-shield' },
]);

const handleSearch = () => {
    if (searchQuery.value.length > 0) {
        showResults.value = true;
    } else {
        showResults.value = false;
    }
};

const handleFocus = () => {
    isFocused.value = true;
    if (searchQuery.value.length > 0) {
        showResults.value = true;
    }
};

const handleBlur = () => {
    isFocused.value = false;
    // Delay hiding results to allow clicking on them
    setTimeout(() => {
        showResults.value = false;
    }, 200);
};
</script>

<template>
    <div class="global-search-container" :class="{ 'focused': isFocused }">
        <div class="search-hero-content text-center mb-4">
            <!-- <h1 class="search-title fw-bold">{{ __('What are you looking for?') }}</h1> -->
            <!-- <p class="search-description text-muted mb-0">
                {{ __('Find the best financial solutions from top Tajikistan banks in seconds.') }}
            </p> -->
        </div>

        <div class="search-input-wrapper">
            <Search class="search-icon" :size="20" />
            <input 
                type="text" 
                v-model="searchQuery" 
                @input="handleSearch"
                @focus="handleFocus"
                @blur="handleBlur"
                :placeholder="__('Search for services, products or organizations...')"
                class="search-input"
            />
            <button class="search-button">
                {{ __('Search') }}
            </button>
        </div>
        <!-- ... (dropdown content unchanged) ... -->

        <!-- Dropdown Results -->
        <Transition name="fade-slide">
            <div v-if="showResults" class="search-results-dropdown">
                <div class="results-header">
                    <span>{{ __('Popular searches') }}</span>
                </div>
                <div class="results-list">
                    <div v-for="(result, index) in results" :key="index" class="result-item">
                        <div class="result-icon">
                            <i :class="result.icon"></i>
                        </div>
                        <div class="result-info">
                            <span class="result-title">{{ result.title }}</span>
                            <span class="result-category">{{ result.category }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-right ms-auto opacity-50 small"></i>
                    </div>
                </div>
                <div class="results-footer">
                    <a href="#">{{ __('View all results for') }} "{{ searchQuery }}"</a>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.global-search-container {
    position: relative;
    max-width: 800px;
    margin: 0 auto 4rem auto;
    z-index: 90;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.search-title {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    color: #0f172a;
}

.search-description {
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.global-search-container.focused {
    /* Transform removed */
}

.search-input-wrapper {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    padding: 8px 8px 8px 24px;
    border: 1.5px solid #e2e8f0;
    transition: all 0.3s ease;
}

.focused .search-input-wrapper {
    border-color: var(--bs-primary);
}

.search-icon {
    color: #94a3b8;
    margin-right: 16px;
    transition: color 0.3s ease;
}

.focused .search-icon {
    color: var(--bs-primary);
}

.search-input {
    flex-grow: 1;
    border: none;
    outline: none;
    font-size: 1rem;
    padding: 12px 0;
    background: transparent;
    color: #1e293b;
}

.search-input::placeholder {
    color: #94a3b8;
}

.search-button {
    background: var(--bs-primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 32px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-left: 8px;
}

.search-button:hover {
    background: #008cd4;
    transform: scale(1.02);
}

.search-button:active {
    transform: scale(0.98);
}

/* Results Dropdown */
.search-results-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    left: 0;
    right: 0;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
    z-index: 100;
}

.results-header {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.8rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.result-item {
    display: flex;
    align-items: center;
    padding: 12px 24px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.result-item:hover {
    background: #f1f5f9;
}

.result-icon {
    width: 40px;
    height: 40px;
    background: #eff6ff;
    color: var(--bs-primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    font-size: 1.1rem;
}

.result-info {
    display: flex;
    flex-direction: column;
}

.result-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.95rem;
}

.result-category {
    font-size: 0.75rem;
    color: #64748b;
}

.results-footer {
    padding: 16px 24px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
    text-align: center;
}

.results-footer a {
    color: var(--bs-primary);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
}

.results-footer a:hover {
    text-decoration: underline;
}

/* Animations */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}

.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

@media (max-width: 768px) {
    .search-input-wrapper {
        padding: 6px 6px 6px 16px;
    }
    
    .search-button {
        padding: 10px 20px;
        font-size: 0.85rem;
    }
    
    .search-input {
        font-size: 0.9rem;
    }
}
</style>
