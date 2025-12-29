<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <Link
              href="/"
              class="text-gray-600 hover:text-gray-900 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
          </div>

          <!-- User Info -->
          <div v-if="$page.props.auth?.user" class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">{{ $page.props.auth.user.name }}</span>
            <Link
              href="/dashboard"
              class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm"
            >
              Dashboard
            </Link>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="text-red-600 text-lg font-medium mb-2">Failed to load cart</div>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button
          @click="fetchCart"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
        >
          Try Again
        </button>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="!cart || cart.items.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6H19M7 13v6a1 1 0 001 1h10a1 1 0 001-1v-6M7 13L5.4 5M7 13l1.5 6" />
        </svg>
        <h3 class="mt-2 text-lg font-semibold text-gray-900">Your cart is empty</h3>
        <p class="mt-1 text-sm text-gray-500">Start shopping to add items to your cart.</p>
        <div class="mt-6">
          <Link
            href="/"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
          >
            Continue Shopping
          </Link>
        </div>
      </div>

      <!-- Cart Content -->
      <div v-else class="lg:grid lg:grid-cols-12 lg:gap-x-8 lg:items-start">
        <!-- Cart Items -->
        <div class="lg:col-span-8">
          <div class="bg-white rounded-lg shadow-md">
            <div class="px-4 py-6 sm:px-6">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Cart Items</h2>
                <button
                  @click="clearCart"
                  class="text-sm text-red-600 hover:text-red-500 transition-colors"
                >
                  Clear Cart
                </button>
              </div>
              
              <div class="mt-6 flow-root">
                <ul role="list" class="divide-y divide-gray-200">
                  <li
                    v-for="item in cart.items"
                    :key="item.id"
                    class="flex py-6"
                  >
                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                      <img
                        v-if="item.product.imageUrl"
                        :src="item.product.imageUrl"
                        :alt="item.product.name"
                        class="h-full w-full object-cover object-center"
                        @error="handleImageError"
                      />
                      <div v-else class="flex items-center justify-center h-full text-gray-400 bg-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>

                    <div class="ml-4 flex flex-1 flex-col">
                      <div>
                        <div class="flex justify-between text-base font-medium text-gray-900">
                          <h3>{{ item.product.name }}</h3>
                          <p class="ml-4">{{ item.product.formattedPrice }}</p>
                        </div>
                        <p v-if="item.product.category" class="mt-1 text-sm text-gray-500">
                          {{ item.product.category.name }}
                        </p>
                      </div>
                      
                      <div class="flex flex-1 items-end justify-between text-sm">
                        <div class="flex items-center space-x-2">
                          <label class="text-gray-500">Qty:</label>
                          <select
                            :value="item.quantity"
                            @change="updateQuantity(item.id, $event.target.value)"
                            class="rounded-md border border-gray-300 text-base font-medium text-gray-700 text-left shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                          >
                            <option v-for="i in 10" :key="i" :value="i">{{ i }}</option>
                          </select>
                          <span class="text-gray-500">×</span>
                          <span class="font-medium">${{ item.subtotal.toFixed(2) }}</span>
                        </div>

                        <div class="flex">
                          <button
                            @click="removeItem(item.id)"
                            type="button"
                            class="font-medium text-red-600 hover:text-red-500 transition-colors"
                          >
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Continue Shopping -->
          <div class="mt-6">
            <Link
              href="/"
              class="inline-flex items-center text-blue-600 hover:text-blue-500 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Continue Shopping
            </Link>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-4 mt-16 lg:mt-0">
          <div class="bg-white rounded-lg shadow-md">
            <div class="px-4 py-6 sm:px-6">
              <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
              
              <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between">
                  <p class="text-sm text-gray-600">Total Items</p>
                  <p class="text-sm font-medium text-gray-900">{{ cart.totalItems }}</p>
                </div>
                
                <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                  <p class="text-base font-medium text-gray-900">Subtotal</p>
                  <p class="text-base font-medium text-gray-900">{{ cart.formattedTotalAmount }}</p>
                </div>
                
                <div class="text-sm text-gray-500">
                  <p>Shipping and taxes calculated at checkout.</p>
                </div>
              </div>

              <div class="mt-6">
                <button
                  class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  @click="proceedToCheckout"
                >
                  Proceed to Checkout
                </button>
              </div>

              <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">or</p>
                <Link
                  href="/"
                  class="text-sm text-blue-600 hover:text-blue-500 transition-colors"
                >
                  Continue Shopping
                </Link>
              </div>
            </div>
          </div>
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
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const loading = ref(false)
const error = ref(null)
const cart = ref(null)
const notifications = ref([])

// Get CSRF token for API requests
const getCSRFToken = () => {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta ? meta.getAttribute('content') : ''
}

// Fetch cart from API
const fetchCart = async () => {
  try {
    loading.value = true
    error.value = null

    const response = await fetch('/api/cart', {
      headers: {
        'Authorization': `Bearer ${window.Laravel?.auth?.token}`,
        'Accept': 'application/json',
      }
    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 401) {
        router.visit('/login')
        return
      }
      throw new Error(data.message || 'Failed to fetch cart')
    }

    if (data.success) {
      cart.value = data.data
    } else {
      throw new Error(data.message || 'Failed to fetch cart')
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

// Update item quantity
const updateQuantity = async (itemId, quantity) => {
  try {
    const response = await fetch(`/api/cart/${itemId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${window.Laravel?.auth?.token}`,
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCSRFToken(),
      },
      body: JSON.stringify({ quantity: parseInt(quantity) })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      showNotification('Cart updated successfully', 'success')
      await fetchCart() // Refresh cart
    } else {
      throw new Error(data.message || 'Failed to update cart')
    }
  } catch (err) {
    showNotification(err.message || 'Failed to update cart', 'error')
  }
}

// Remove item from cart
const removeItem = async (itemId) => {
  try {
    const response = await fetch(`/api/cart/${itemId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${window.Laravel?.auth?.token}`,
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCSRFToken(),
      }
    })

    const data = await response.json()

    if (response.ok && data.success) {
      showNotification('Item removed from cart', 'success')
      await fetchCart() // Refresh cart
    } else {
      throw new Error(data.message || 'Failed to remove item')
    }
  } catch (err) {
    showNotification(err.message || 'Failed to remove item', 'error')
  }
}

// Clear entire cart
const clearCart = async () => {
  if (!confirm('Are you sure you want to clear your entire cart?')) {
    return
  }

  try {
    const response = await fetch('/api/cart', {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${window.Laravel?.auth?.token}`,
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCSRFToken(),
      }
    })

    const data = await response.json()

    if (response.ok && data.success) {
      showNotification('Cart cleared successfully', 'success')
      await fetchCart() // Refresh cart
    } else {
      throw new Error(data.message || 'Failed to clear cart')
    }
  } catch (err) {
    showNotification(err.message || 'Failed to clear cart', 'error')
  }
}

// Proceed to checkout
const proceedToCheckout = () => {
  showNotification('Checkout feature coming soon!', 'info')
}

// Handle image errors
const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Notification system
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

// Check authentication and redirect if needed
onMounted(() => {
  if (!window.Laravel?.auth?.user) {
    router.visit('/login')
    return
  }
  
  fetchCart()
})
</script>