<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="max-w-4xl mx-auto">
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Loading order details...</p>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 dark:text-red-400 mb-4">{{ error }}</div>
          <Link href="/dashboard" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Back to Dashboard
          </Link>
        </div>

        <div v-else-if="order" class="space-y-6">
          <!-- Order Header -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order Confirmation</h1>
              <span :class="[
                'px-3 py-1 rounded-full text-sm font-medium',
                order.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                order.status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                order.status === 'shipped' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400' :
                order.status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
              ]">
                {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
              </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-gray-600 dark:text-gray-400">Order Number</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ order.order_number }}</p>
              </div>
              <div>
                <p class="text-gray-600 dark:text-gray-400">Order Date</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(order.placed_at) }}</p>
              </div>
              <div>
                <p class="text-gray-600 dark:text-gray-400">Total Amount</p>
                <p class="font-medium text-gray-900 dark:text-white">${{ order.total_amount }}</p>
              </div>
            </div>

            <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    Order placed successfully!
                  </p>
                  <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                    Your order has been received and is being processed. You will receive updates via email.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Items -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Items</h2>
            
            <div class="space-y-4">
              <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <img 
                  :src="getProductImage(item.product)" 
                  :alt="item.product.name" 
                  class="w-16 h-16 object-cover rounded-md"
                  @error="handleImageError"
                >
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900 dark:text-white">{{ item.product.name }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">${{ item.unit_price }} each</p>
                </div>
                <div class="text-right">
                  <p class="font-medium text-gray-900 dark:text-white">${{ item.total_price }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
            
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                <span class="text-gray-900 dark:text-white">${{ (order.total_amount - order.tax_amount - order.shipping_amount).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                <span class="text-gray-900 dark:text-white">${{ order.tax_amount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                <span class="text-gray-900 dark:text-white">${{ order.shipping_amount }}</span>
              </div>
              <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200 dark:border-gray-700">
                <span class="text-gray-900 dark:text-white">Total</span>
                <span class="text-gray-900 dark:text-white">${{ order.total_amount }}</span>
              </div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Shipping Address</h2>
            
            <div class="text-gray-900 dark:text-white">
              <p>{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</p>
              <p>{{ order.shipping_address.address_line_1 }}</p>
              <p v-if="order.shipping_address.address_line_2">{{ order.shipping_address.address_line_2 }}</p>
              <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.zip_code }}</p>
              <p>{{ order.shipping_address.country }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-center space-x-4">
            <Link 
              href="/products" 
              class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Continue Shopping
            </Link>
            <Link 
              href="/orders" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm"
            >
              View All Orders
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useApi } from '@/composables/useApi'

const props = defineProps({
  orderId: {
    type: [String, Number],
    required: true
  }
})

const { apiRequest } = useApi()

const order = ref(null)
const loading = ref(true)
const error = ref('')

const fetchOrder = async () => {
  try {
    const response = await apiRequest(`/api/orders/${props.orderId}`)
    
    if (response.ok) {
      const data = await response.json()
      console.log('Order data received:', data)
      order.value = data.order
      if (order.value?.items) {
        console.log('Order items:', order.value.items)
        console.log('First item product:', order.value.items[0]?.product)
      }
    } else {
      error.value = 'Order not found'
    }
  } catch (err) {
    console.error('Failed to fetch order:', err)
    error.value = 'Failed to load order details'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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
  fetchOrder()
})
</script>