<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Orders</h1>
          <Link 
            href="/products" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Continue Shopping
          </Link>
        </div>

        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Loading your orders...</p>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 dark:text-red-400 mb-4">{{ error }}</div>
          <button 
            @click="fetchOrders"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Try Again
          </button>
        </div>

        <div v-else-if="!orders || orders.length === 0" class="text-center py-12">
          <div class="text-gray-500 dark:text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders yet</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start shopping to see your orders here.</p>
          <div class="mt-6">
            <Link 
              href="/products" 
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
            >
              Start Shopping
            </Link>
          </div>
        </div>

        <div v-else class="space-y-6">
          <div 
            v-for="order in orders" 
            :key="order.id" 
            class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden"
          >
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Order #{{ order.order_number }}
                  </h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Placed on {{ formatDate(order.placed_at) }}
                  </p>
                </div>
                <div class="text-right">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    getStatusClasses(order.status)
                  ]">
                    {{ formatStatus(order.status) }}
                  </span>
                  <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    ${{ order.total_amount }}
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>{{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }}</span>
                <Link 
                  :href="`/orders/${order.id}`"
                  class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  View Details
                </Link>
              </div>

              <!-- Order Progress -->
              <div class="mt-6">
                <div class="flex items-center">
                  <div class="flex items-center text-sm">
                    <div :class="[
                      'flex items-center justify-center w-6 h-6 rounded-full border-2',
                      isStatusActive(order.status, 'pending') ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-white'
                    ]">
                      <svg v-if="isStatusActive(order.status, 'pending')" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <span :class="[
                      'ml-2',
                      isStatusActive(order.status, 'pending') ? 'text-indigo-600 font-medium' : 'text-gray-500'
                    ]">
                      Order Placed
                    </span>
                  </div>

                  <div class="flex-1 mx-4">
                    <div :class="[
                      'h-0.5',
                      isStatusCompleted(order.status, 'pending') ? 'bg-indigo-600' : 'bg-gray-300'
                    ]"></div>
                  </div>

                  <div class="flex items-center text-sm">
                    <div :class="[
                      'flex items-center justify-center w-6 h-6 rounded-full border-2',
                      isStatusActive(order.status, 'processing') ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-white'
                    ]">
                      <svg v-if="isStatusActive(order.status, 'processing')" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <span :class="[
                      'ml-2',
                      isStatusActive(order.status, 'processing') ? 'text-indigo-600 font-medium' : 'text-gray-500'
                    ]">
                      Processing
                    </span>
                  </div>

                  <div class="flex-1 mx-4">
                    <div :class="[
                      'h-0.5',
                      isStatusCompleted(order.status, 'processing') ? 'bg-indigo-600' : 'bg-gray-300'
                    ]"></div>
                  </div>

                  <div class="flex items-center text-sm">
                    <div :class="[
                      'flex items-center justify-center w-6 h-6 rounded-full border-2',
                      isStatusActive(order.status, 'shipped') ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-white'
                    ]">
                      <svg v-if="isStatusActive(order.status, 'shipped')" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <span :class="[
                      'ml-2',
                      isStatusActive(order.status, 'shipped') ? 'text-indigo-600 font-medium' : 'text-gray-500'
                    ]">
                      Shipped
                    </span>
                  </div>

                  <div class="flex-1 mx-4">
                    <div :class="[
                      'h-0.5',
                      isStatusCompleted(order.status, 'shipped') ? 'bg-indigo-600' : 'bg-gray-300'
                    ]"></div>
                  </div>

                  <div class="flex items-center text-sm">
                    <div :class="[
                      'flex items-center justify-center w-6 h-6 rounded-full border-2',
                      isStatusActive(order.status, 'delivered') ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-white'
                    ]">
                      <svg v-if="isStatusActive(order.status, 'delivered')" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <span :class="[
                      'ml-2',
                      isStatusActive(order.status, 'delivered') ? 'text-indigo-600 font-medium' : 'text-gray-500'
                    ]">
                      Delivered
                    </span>
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="mt-6 flex flex-wrap gap-3">
                <Link 
                  :href="`/orders/${order.id}`"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  View Details
                </Link>
                <button 
                  v-if="order.status === 'delivered'"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  Reorder
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination would go here if needed -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useApi } from '@/composables/useApi'

const { apiRequest } = useApi()

const orders = ref([])
const loading = ref(true)
const error = ref('')

const fetchOrders = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await apiRequest('/api/orders')
    
    if (response.ok) {
      const data = await response.json()
      orders.value = data.orders
    } else {
      error.value = 'Failed to load orders'
    }
  } catch (err) {
    console.error('Failed to fetch orders:', err)
    error.value = 'Failed to load orders'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const getStatusClasses = (status) => {
  const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    shipped: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

const statusOrder = ['pending', 'processing', 'shipped', 'delivered']

const isStatusActive = (currentStatus, targetStatus) => {
  if (currentStatus === 'cancelled') return false
  const currentIndex = statusOrder.indexOf(currentStatus)
  const targetIndex = statusOrder.indexOf(targetStatus)
  return currentIndex >= targetIndex
}

const isStatusCompleted = (currentStatus, targetStatus) => {
  if (currentStatus === 'cancelled') return false
  const currentIndex = statusOrder.indexOf(currentStatus)
  const targetIndex = statusOrder.indexOf(targetStatus)
  return currentIndex > targetIndex
}

onMounted(() => {
  fetchOrders()
})
</script>