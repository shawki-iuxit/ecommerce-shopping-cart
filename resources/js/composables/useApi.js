import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useApi() {
  const page = usePage()
  
  // Get CSRF token
  const getCSRFToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]')
    return meta ? meta.getAttribute('content') : ''
  }

  // Get API token from Inertia props
  const apiToken = computed(() => page.props.apiToken)

  const getApiToken = () => {
    if (!page.props.auth?.user) {
      console.log('No authenticated user found')
      return null
    }
    
    if (!apiToken.value) {
      console.error('No API token available - this should be set by middleware')
      return null
    }

    console.log('Using API token from Inertia props')
    return apiToken.value
  }

  // Make authenticated API request
  const apiRequest = async (url, options = {}) => {
    console.log('Making API request to:', url)
    const token = getApiToken()
    
    if (!token) {
      console.error('No API token available for request')
      throw new Error('Authentication required')
    }
    
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Authorization': `Bearer ${token}`,
      ...options.headers,
    }

    if (!options.method || options.method.toUpperCase() !== 'GET') {
      headers['X-CSRF-TOKEN'] = getCSRFToken()
    }

    console.log('Request headers:', headers)

    const response = await fetch(url, {
      ...options,
      headers,
      credentials: 'same-origin',
    })

    console.log('Response status:', response.status)
    return response
  }

  return {
    apiToken,
    getApiToken,
    apiRequest,
    getCSRFToken,
  }
}