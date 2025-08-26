const CACHE_NAME = 'translations-v1'
const API_CACHE_NAME = 'translation-api-v1'
const OFFLINE_CACHE_NAME = 'translation-offline-v1'

// Install event - cache essential resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll([
        '/',
        '/offline.html'
      ])
    })
  )
  self.skipWaiting()
})

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME && 
              cacheName !== API_CACHE_NAME && 
              cacheName !== OFFLINE_CACHE_NAME) {
            return caches.delete(cacheName)
          }
        })
      )
    })
  )
  self.clients.claim()
})

// Fetch event - intercept translation requests
self.addEventListener('fetch', (event) => {
  const { request } = event
  
  // Handle translation API requests
  if (request.url.includes('/api/translate') && request.method === 'POST') {
    event.respondWith(handleTranslationRequest(request))
    return
  }
  
  // Handle other requests
  event.respondWith(
    caches.match(request).then((response) => {
      return response || fetch(request)
    })
  )
})

// Handle translation requests with caching
async function handleTranslationRequest(request) {
  try {
    // Try to get from cache first
    const cache = await caches.open(API_CACHE_NAME)
    const cachedResponse = await cache.match(request)
    
    if (cachedResponse) {
      const cachedData = await cachedResponse.json()
      // Check if cache is still valid (24 hours)
      if (Date.now() - cachedData.timestamp < 24 * 60 * 60 * 1000) {
        return new Response(JSON.stringify(cachedData), {
          headers: { 'Content-Type': 'application/json' }
        })
      }
    }
    
    // If not cached or expired, fetch from network
    const response = await fetch(request)
    
    if (response.ok) {
      const data = await response.json()
      
      // Cache the successful response
      const cacheData = {
        ...data,
        timestamp: Date.now()
      }
      
      const cacheResponse = new Response(JSON.stringify(cacheData), {
        headers: { 'Content-Type': 'application/json' }
      })
      
      await cache.put(request, cacheResponse.clone())
      
      return response
    }
    
    // If network fails, try to return cached version (even if expired)
    if (cachedResponse) {
      return cachedResponse
    }
    
    return response
    
  } catch (error) {
    console.error('Translation request failed:', error)
    
    // Try to return cached version as fallback
    const cache = await caches.open(API_CACHE_NAME)
    const cachedResponse = await cache.match(request)
    
    if (cachedResponse) {
      return cachedResponse
    }
    
    // Return offline response
    return new Response(JSON.stringify({
      translatedText: 'Translation temporarily unavailable',
      service: 'offline',
      error: true
    }), {
      headers: { 'Content-Type': 'application/json' }
    })
  }
}

// Background sync for failed translations
self.addEventListener('sync', (event) => {
  if (event.tag === 'background-translation-sync') {
    event.waitUntil(backgroundTranslationSync())
  }
})

// Background sync function
async function backgroundTranslationSync() {
  try {
    // Get failed translation requests from IndexedDB
    const failedRequests = await getFailedRequests()
    
    for (const request of failedRequests) {
      try {
        const response = await fetch(request.url, {
          method: request.method,
          headers: request.headers,
          body: request.body
        })
        
        if (response.ok) {
          // Remove from failed requests
          await removeFailedRequest(request.id)
          
          // Cache the successful response
          const cache = await caches.open(API_CACHE_NAME)
          await cache.put(request.url, response.clone())
        }
      } catch (error) {
        console.error('Background sync failed for request:', request.id, error)
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error)
  }
}

// IndexedDB operations for failed requests
async function getFailedRequests() {
  // Implementation would depend on your IndexedDB setup
  return []
}

async function removeFailedRequest(id) {
  // Implementation would depend on your IndexedDB setup
}

// Message event for communication with main thread
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting()
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(clearAllCaches())
  }
})

// Clear all caches
async function clearAllCaches() {
  const cacheNames = await caches.keys()
  await Promise.all(
    cacheNames.map(cacheName => caches.delete(cacheName))
  )
  
  // Notify clients that cache was cleared
  const clients = await self.clients.matchAll()
  clients.forEach(client => {
    client.postMessage({
      type: 'CACHE_CLEARED'
    })
  })
}
