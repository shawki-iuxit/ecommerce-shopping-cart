<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Checkout</h1>

        <div v-if="!cart || cart.items.length === 0" class="text-center py-12">
          <div class="text-gray-500 dark:text-gray-400 mb-4">Your cart is empty</div>
          <Link href="/products" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Continue Shopping
          </Link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Order Summary -->
          <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
              
              <div class="space-y-4">
                <div v-for="item in cart.items" :key="item.id" class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                  <img 
                    :src="getProductImage(item.product)" 
                    :alt="item.product.name" 
                    class="w-16 h-16 object-cover rounded-md"
                    @error="handleImageError"
                  >
                  <div class="flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ item.product.name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
                  </div>
                  <div class="text-right">
                    <p class="font-medium text-gray-900 dark:text-white">${{ (item.total_price || item.subtotal || 0).toFixed(2) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">${{ (item.unit_price || item.product.price || 0).toFixed(2) }} each</p>
                  </div>
                </div>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="text-gray-900 dark:text-white">${{ subtotal.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tax (10%)</span>
                    <span class="text-gray-900 dark:text-white">${{ taxAmount.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                    <span class="text-gray-900 dark:text-white">${{ shippingAmount.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-gray-900 dark:text-white">Total</span>
                    <span class="text-gray-900 dark:text-white">${{ totalAmount.toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Shipping Address</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                  <input 
                    type="text" 
                    id="shipping_first_name"
                    v-model="form.shipping_address.first_name"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                  <input 
                    type="text" 
                    id="shipping_last_name"
                    v-model="form.shipping_address.last_name"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div class="md:col-span-2">
                  <label for="shipping_address_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 1</label>
                  <input 
                    type="text" 
                    id="shipping_address_1"
                    v-model="form.shipping_address.address_line_1"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div class="md:col-span-2">
                  <label for="shipping_address_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 2 (Optional)</label>
                  <input 
                    type="text" 
                    id="shipping_address_2"
                    v-model="form.shipping_address.address_line_2"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                  >
                </div>
                <div>
                  <label for="shipping_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                  <input 
                    type="text" 
                    id="shipping_city"
                    v-model="form.shipping_address.city"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="shipping_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                  <input 
                    type="text" 
                    id="shipping_state"
                    v-model="form.shipping_address.state"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="shipping_zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP Code</label>
                  <input 
                    type="text" 
                    id="shipping_zip"
                    v-model="form.shipping_address.zip_code"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="shipping_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                  <input 
                    type="text" 
                    id="shipping_country"
                    v-model="form.shipping_address.country"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
              </div>
            </div>

            <!-- Billing Address -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Billing Address</h2>
                <label class="flex items-center">
                  <input 
                    type="checkbox" 
                    v-model="sameAsShipping"
                    @change="toggleBillingAddress"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Same as shipping</span>
                </label>
              </div>
              
              <div v-if="!sameAsShipping" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="billing_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                  <input 
                    type="text" 
                    id="billing_first_name"
                    v-model="form.billing_address.first_name"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="billing_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                  <input 
                    type="text" 
                    id="billing_last_name"
                    v-model="form.billing_address.last_name"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div class="md:col-span-2">
                  <label for="billing_address_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 1</label>
                  <input 
                    type="text" 
                    id="billing_address_1"
                    v-model="form.billing_address.address_line_1"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div class="md:col-span-2">
                  <label for="billing_address_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 2 (Optional)</label>
                  <input 
                    type="text" 
                    id="billing_address_2"
                    v-model="form.billing_address.address_line_2"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                  >
                </div>
                <div>
                  <label for="billing_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                  <input 
                    type="text" 
                    id="billing_city"
                    v-model="form.billing_address.city"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="billing_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                  <input 
                    type="text" 
                    id="billing_state"
                    v-model="form.billing_address.state"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="billing_zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP Code</label>
                  <input 
                    type="text" 
                    id="billing_zip"
                    v-model="form.billing_address.zip_code"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
                <div>
                  <label for="billing_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                  <input 
                    type="text" 
                    id="billing_country"
                    v-model="form.billing_address.country"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white"
                    required
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Section -->
          <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 sticky top-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment</h3>
              
              <div class="space-y-4 mb-6">
                <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Payment Method</p>
                  <p class="text-gray-900 dark:text-white">Cash on Delivery</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pay when your order arrives</p>
                </div>
              </div>

              <button 
                @click="placeOrder"
                :disabled="loading || !isFormValid"
                :class="[
                  'w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-white font-medium',
                  (loading || !isFormValid) 
                    ? 'bg-gray-400 cursor-not-allowed' 
                    : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
                ]"
              >
                <span v-if="loading" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Placing Order...
                </span>
                <span v-else>Place Order - ${{ totalAmount.toFixed(2) }}</span>
              </button>

              <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useApi } from '@/composables/useApi'

const { apiRequest } = useApi()

const cart = ref(null)
const loading = ref(false)
const errorMessage = ref('')
const sameAsShipping = ref(true)

const form = reactive({
  shipping_address: {
    first_name: '',
    last_name: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    zip_code: '',
    country: 'USA'
  },
  billing_address: {
    first_name: '',
    last_name: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    zip_code: '',
    country: 'USA'
  }
})

const subtotal = computed(() => {
  if (!cart.value?.items || !Array.isArray(cart.value.items)) return 0
  return cart.value.items.reduce((sum, item) => {
    // Check both possible field names for compatibility
    const itemTotal = item.total_price || item.subtotal || 0
    const numericTotal = parseFloat(itemTotal) || 0
    return sum + numericTotal
  }, 0)
})

const taxAmount = computed(() => subtotal.value * 0.1)
const shippingAmount = computed(() => 10.00)
const totalAmount = computed(() => subtotal.value + taxAmount.value + shippingAmount.value)

const isFormValid = computed(() => {
  const shipping = form.shipping_address
  const billing = sameAsShipping.value ? shipping : form.billing_address
  
  return shipping.first_name && shipping.last_name && shipping.address_line_1 && 
         shipping.city && shipping.state && shipping.zip_code && shipping.country &&
         billing.first_name && billing.last_name && billing.address_line_1 && 
         billing.city && billing.state && billing.zip_code && billing.country
})

const toggleBillingAddress = () => {
  if (sameAsShipping.value) {
    form.billing_address = { ...form.shipping_address }
  }
}

const fetchCart = async () => {
  try {
    const response = await apiRequest('/api/cart')
    if (response.ok) {
      const data = await response.json()
      cart.value = data.data
    } else {
      errorMessage.value = 'Failed to load cart data'
    }
  } catch (error) {
    console.error('Failed to fetch cart:', error)
    errorMessage.value = 'Failed to load cart data'
  }
}

const placeOrder = async () => {
  loading.value = true
  errorMessage.value = ''
  
  try {
    const orderData = {
      shipping_address: form.shipping_address,
      billing_address: sameAsShipping.value ? form.shipping_address : form.billing_address
    }
    
    const response = await apiRequest('/api/orders', {
      method: 'POST',
      body: JSON.stringify(orderData)
    })
    
    if (response.ok) {
      const data = await response.json()
      router.visit(`/orders/${data.order.id}`, {
        only: ['order'],
        onSuccess: () => {
          // Order placed successfully, redirect handled by Inertia
        }
      })
    } else {
      const errorData = await response.json()
      errorMessage.value = errorData.message || 'Failed to place order'
    }
  } catch (error) {
    console.error('Failed to place order:', error)
    errorMessage.value = 'Failed to place order. Please try again.'
  } finally {
    loading.value = false
  }
}

const getProductImage = (product) => {
  if (!product) {
    console.log('No product data available')
    return createPlaceholderImage()
  }
  
  // Try different possible image property names
  const imageUrl = product.imageUrl || product.image_url || product.image
  
  if (imageUrl) {
    console.log('Product image URL found:', imageUrl)
    return imageUrl
  }
  
  console.log('No image URL found for product:', product)
  return createPlaceholderImage()
}

const createPlaceholderImage = () => {
  const canvas = document.createElement('canvas')
  canvas.width = 64
  canvas.height = 64
  const ctx = canvas.getContext('2d')
  
  // Gray background
  ctx.fillStyle = '#f3f4f6'
  ctx.fillRect(0, 0, 64, 64)
  
  // Add a simple box icon
  ctx.fillStyle = '#9ca3af'
  ctx.fillRect(16, 16, 32, 32)
  ctx.strokeStyle = '#6b7280'
  ctx.lineWidth = 2
  ctx.strokeRect(16, 16, 32, 32)
  
  return canvas.toDataURL()
}

const handleImageError = (event) => {
  console.log('Image failed to load:', event.target.src)
  event.target.src = createPlaceholderImage()
}

onMounted(() => {
  fetchCart()
})
</script>