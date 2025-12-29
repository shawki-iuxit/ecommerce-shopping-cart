<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-2xl font-bold text-gray-900">
            E-Commerce Store
          </h1>

          <!-- Auth Actions -->
          <div class="flex items-center space-x-4">
            <!-- Cart Icon (only show if authenticated) -->
            <div v-if="$page.props.auth?.user" class="relative">
              <button
                @click="goToCart"
                class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6H19M7 13v6a1 1 0 001 1h10a1 1 0 001-1v-6M7 13L5.4 5M7 13l1.5 6" />
                </svg>
                <!-- Cart Count Badge -->
                <span
                  v-if="cartCount > 0"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                >
                  {{ cartCount > 99 ? '99+' : cartCount }}
                </span>
              </button>
            </div>

            <!-- User Dropdown -->
            <div v-if="$page.props.auth?.user" class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                @blur="hideUserMenuDelayed"
                class="flex items-center space-x-2 px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors rounded-md"
              >
                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-medium text-gray-700">
                  {{ getInitials($page.props.auth.user.name) }}
                </div>
                <span class="text-sm">{{ $page.props.auth.user.name }}</span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showUserMenu"
                @mouseenter="clearHideTimeout"
                @mouseleave="hideUserMenuDelayed"
                class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
              >
                <Link
                  href="/dashboard"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7" />
                  </svg>
                  Dashboard
                </Link>
                <Link
                  href="/cart"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6H19M7 13v6a1 1 0 001 1h10a1 1 0 001-1v-6M7 13L5.4 5M7 13l1.5 6" />
                  </svg>
                  Shopping Cart
                  <span v-if="cartCount > 0" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                    {{ cartCount > 99 ? '99+' : cartCount }}
                  </span>
                </Link>
                <Link
                  href="/orders"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  My Orders
                </Link>
                <div class="border-t border-gray-100 my-1"></div>
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Logout
                </Link>
              </div>
            </div>
            <div v-else class="flex items-center space-x-2">
              <Link
                href="/login"
                class="px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors text-sm"
              >
                Login
              </Link>
              <Link
                href="/register"
                class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm"
              >
                Register
              </Link>
            </div>
          </div>
          
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Category Filter -->
      <div class="mb-8">
        <div class="flex flex-wrap gap-2">
          <button
            @click="selectedCategory = null"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-colors',
              selectedCategory === null 
                ? 'bg-blue-600 text-white' 
                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
            ]"
          >
            All Products
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            @click="selectedCategory = category.id"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-colors',
              selectedCategory === category.id 
                ? 'bg-blue-600 text-white' 
                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ category.name }}
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="n in 8"
          :key="n"
          class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse"
        >
          <div class="h-48 bg-gray-300"></div>
          <div class="p-4 space-y-3">
            <div class="h-4 bg-gray-300 rounded"></div>
            <div class="h-3 bg-gray-300 rounded w-2/3"></div>
            <div class="h-4 bg-gray-300 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="text-red-600 text-lg font-medium mb-2">Failed to load products</div>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button
          @click="fetchProducts"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Try Again
        </button>
      </div>

      <!-- Products Grid -->
      <div v-else-if="products.length > 0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
          <div
            v-for="product in products"
            :key="product.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
          >
            <div class="relative h-48 bg-gray-200">
              <img
                v-if="product.imageUrl"
                :src="product.imageUrl"
                :alt="product.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div v-else class="flex items-center justify-center h-full text-gray-400">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              
              <!-- Stock Badge -->
              <div class="absolute top-2 right-2">
                <span
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    product.isInStock 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ product.isInStock ? 'In Stock' : 'Out of Stock' }}
                </span>
              </div>
            </div>

            <div class="p-4">
              <div class="mb-2">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                  {{ product.name }}
                </h3>
                <p v-if="product.category" class="text-sm text-gray-500">
                  {{ product.category.name }}
                </p>
              </div>
              
              <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                {{ product.description }}
              </p>
              
              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-blue-600">
                  {{ product.formattedPrice }}
                </span>
                <button
                  :disabled="!product.isAvailable"
                  :class="[
                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                    product.isAvailable
                      ? 'bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                      : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  ]"
                  @click="addToCart(product)"
                >
                  {{ product.isAvailable ? 'Add to Cart' : 'Unavailable' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.lastPage > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg">
          <div class="flex flex-1 justify-between sm:hidden">
            <button
              @click="goToPage(pagination.currentPage - 1)"
              :disabled="pagination.currentPage === 1"
              :class="[
                'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700',
                pagination.currentPage === 1
                  ? 'cursor-not-allowed opacity-50'
                  : 'hover:bg-gray-50'
              ]"
            >
              Previous
            </button>
            <button
              @click="goToPage(pagination.currentPage + 1)"
              :disabled="pagination.currentPage === pagination.lastPage"
              :class="[
                'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700',
                pagination.currentPage === pagination.lastPage
                  ? 'cursor-not-allowed opacity-50'
                  : 'hover:bg-gray-50'
              ]"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ pagination.from }}</span>
                to
                <span class="font-medium">{{ pagination.to }}</span>
                of
                <span class="font-medium">{{ pagination.total }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <button
                  @click="goToPage(pagination.currentPage - 1)"
                  :disabled="pagination.currentPage === 1"
                  :class="[
                    'relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300',
                    pagination.currentPage === 1
                      ? 'cursor-not-allowed opacity-50'
                      : 'hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                  ]"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                  </svg>
                </button>

                <template v-for="page in getVisiblePages()" :key="page">
                  <button
                    v-if="page === '...'"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 cursor-default"
                  >
                    ...
                  </button>
                  <button
                    v-else
                    @click="goToPage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0',
                      page === pagination.currentPage
                        ? 'bg-blue-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                        : 'text-gray-900 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                    ]"
                  >
                    {{ page }}
                  </button>
                </template>

                <button
                  @click="goToPage(pagination.currentPage + 1)"
                  :disabled="pagination.currentPage === pagination.lastPage"
                  :class="[
                    'relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300',
                    pagination.currentPage === pagination.lastPage
                      ? 'cursor-not-allowed opacity-50'
                      : 'hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                  ]"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">No products found</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ selectedCategory || searchQuery ? 'Try adjusting your filters or search terms.' : 'No products are currently available.' }}
        </p>
        <div v-if="selectedCategory || searchQuery" class="mt-6">
          <button
            @click="clearFilters"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
          >
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.93 10.36a.75.75 0 00-1.06 1.061l2.375 2.375a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            Clear filters
          </button>
        </div>
      </div>
    </main>

    <!-- Notifications -->
    <div class="fixed top-4 right-4 space-y-2 z-50">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300',
          notification.type === 'success' ? 'bg-green-500 text-white' :
          notification.type === 'error' ? 'bg-red-500 text-white' :
          'bg-blue-500 text-white'
        ]"
      >
        <div class="flex items-center justify-between">
          <p class="text-sm font-medium">{{ notification.message }}</p>
          <button
            @click="removeNotification(notification.id)"
            class="ml-2 text-white hover:text-gray-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useApi } from '@/composables/useApi.js'

// Simple debounce function
const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

const loading = ref(false)
const error = ref(null)
const products = ref([])
const categories = ref([])
const selectedCategory = ref(null)
const searchQuery = ref('')
const currentPage = ref(1)
const cartCount = ref(0)

// User dropdown menu
const showUserMenu = ref(false)
let hideTimeout = null

// Computed properties
const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth?.user)

// API composable
const { apiRequest } = useApi()

const pagination = reactive({
  currentPage: 1,
  lastPage: 1,
  perPage: 12,
  total: 0,
  from: 0,
  to: 0,
})

// Fetch products from API
const fetchProducts = async (page = 1, categoryId = null, search = '') => {
  try {
    loading.value = true
    error.value = null

    const params = new URLSearchParams({
      page: page.toString(),
      per_page: pagination.perPage.toString(),
    })

    if (categoryId) params.append('category_id', categoryId.toString())
    if (search.trim()) params.append('search', search.trim())

    const response = await fetch(`/api/products?${params}`)
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to fetch products')
    }

    if (data.success) {
      products.value = data.data || []
      
      // Update pagination
      if (data.meta) {
        Object.assign(pagination, data.meta)
      }
    } else {
      throw new Error(data.message || 'Failed to fetch products')
    }
  } catch (err) {
    error.value = err.message
    products.value = []
  } finally {
    loading.value = false
  }
}

// Fetch categories (mock data for now)
const fetchCategories = () => {
  categories.value = [
    { id: 1, name: 'Electronics' },
    { id: 2, name: 'Clothing' },
    { id: 3, name: 'Books' },
    { id: 4, name: 'Home & Garden' },
    { id: 5, name: 'Sports' },
  ]
}

// Debounced search handler
const handleSearch = debounce(() => {
  currentPage.value = 1
  fetchProducts(1, selectedCategory.value, searchQuery.value)
}, 300)

// Watch for category changes
watch(selectedCategory, () => {
  currentPage.value = 1
  fetchProducts(1, selectedCategory.value, searchQuery.value)
})

// Page navigation
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.lastPage && page !== pagination.currentPage) {
    currentPage.value = page
    fetchProducts(page, selectedCategory.value, searchQuery.value)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Get visible page numbers for pagination
const getVisiblePages = () => {
  const pages = []
  const current = pagination.currentPage
  const last = pagination.lastPage
  
  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    pages.push(1)
    
    if (current > 4) {
      pages.push('...')
    }
    
    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)
    
    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
    
    if (current < last - 3) {
      pages.push('...')
    }
    
    pages.push(last)
  }
  
  return pages
}

// Clear all filters
const clearFilters = () => {
  selectedCategory.value = null
  searchQuery.value = ''
  currentPage.value = 1
  fetchProducts()
}

// Get CSRF token for API requests
const getCSRFToken = () => {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta ? meta.getAttribute('content') : ''
}

// Fetch cart count
const fetchCartCount = async () => {
  if (!isAuthenticated.value) return

  try {
    const response = await apiRequest('/api/cart/count')

    if (response.ok) {
      const data = await response.json()
      if (data.success) {
        cartCount.value = data.data.count
      }
    }
  } catch (err) {
    console.error('Failed to fetch cart count:', err)
  }
}

// Add to cart handler
const addToCart = async (product) => {
  if (!isAuthenticated.value) {
    // Redirect to login if not authenticated
    router.visit('/login')
    return
  }

  try {
    const response = await apiRequest('/api/cart', {
      method: 'POST',
      body: JSON.stringify({
        product_id: product.id,
        quantity: 1,
      })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      cartCount.value = data.cart_count || cartCount.value + 1
      
      // Show success message
      showNotification(`${product.name} added to cart!`, 'success')
    } else {
      throw new Error(data.message || 'Failed to add item to cart')
    }
  } catch (err) {
    showNotification(err.message || 'Failed to add item to cart', 'error')
  }
}

// Go to cart page
const goToCart = () => {
  router.visit('/cart')
}

// User dropdown menu functions
const hideUserMenuDelayed = () => {
  hideTimeout = setTimeout(() => {
    showUserMenu.value = false
  }, 150)
}

const clearHideTimeout = () => {
  if (hideTimeout) {
    clearTimeout(hideTimeout)
    hideTimeout = null
  }
}

// Get user initials
const getInitials = (name) => {
  if (!name) return 'U'
  return name.split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('')
}

// Notification system
const notifications = ref([])

const showNotification = (message, type = 'info') => {
  const notification = {
    id: Date.now(),
    message,
    type,
  }
  
  notifications.value.push(notification)
  
  // Auto remove after 3 seconds
  setTimeout(() => {
    removeNotification(notification.id)
  }, 3000)
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Handle image errors
const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Initialize
onMounted(() => {
  fetchCategories()
  fetchProducts()
  fetchCartCount()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>