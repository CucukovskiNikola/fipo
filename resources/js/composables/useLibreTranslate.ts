import { ref, reactive, onMounted } from "vue";

interface TranslationCache {
  [key: string]: {
    text: string;
    timestamp: number;
    expiresAt: number;
  };
}

interface TranslationStats {
  totalTranslations: number;
  cacheHits: number;
  cacheMisses: number;
  lastUsed: number;
}

interface TranslationQueueItem {
  id: string;
  text: string;
  from: string;
  to: string;
  priority: 'high' | 'normal' | 'low';
  resolve: (value: string) => void;
  reject: (reason?: any) => void;
  timestamp: number;
}

export function useLibreTranslate() {
  const isTranslating = ref(false);
  const cache: TranslationCache = reactive({});
  const stats = reactive<TranslationStats>({
    totalTranslations: 0,
    cacheHits: 0,
    cacheMisses: 0,
    lastUsed: Date.now(),
  });

  // Queue management
  const translationQueue: TranslationQueueItem[] = [];
  const isProcessingQueue = ref(false);
  const activeTranslations = new Set<string>();
  
  // Debounce timers
  const debounceTimers = new Map<string, NodeJS.Timeout>();

  // Cache configuration
  const CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
  const LOCAL_STORAGE_KEY = "translation_cache";
  const STATS_STORAGE_KEY = "translation_stats";
  
  // Queue configuration
  const DEBOUNCE_DELAY = 300; // 300ms debounce
  const QUEUE_PROCESS_DELAY = 200; // 200ms between queue items
  const MAX_CONCURRENT_TRANSLATIONS = 1; // Process one at a time

  // Use our backend API to avoid CORS issues
  const API_URL = "/api/translate";

  // Get CSRF token from meta tag
  const getCSRFToken = (): string => {
    const metaTag = document.querySelector(
      'meta[name="csrf-token"]'
    ) as HTMLMetaElement;
    return metaTag?.content || "";
  };

  // Load cache from localStorage on mount
  onMounted(() => {
    loadCacheFromStorage();
    loadStatsFromStorage();
  });

  /**
   * Load cached translations from localStorage
   */
  const loadCacheFromStorage = (): void => {
    try {
      const stored = localStorage.getItem(LOCAL_STORAGE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        const now = Date.now();

        // Filter out expired entries
        Object.keys(parsed).forEach((key) => {
          if (parsed[key].expiresAt > now) {
            cache[key] = parsed[key];
          }
        });
      }
    } catch (error) {
      console.warn(
        "Failed to load translation cache from localStorage:",
        error
      );
    }
  };

  /**
   * Save cache to localStorage
   */
  const saveCacheToStorage = (): void => {
    try {
      localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(cache));
    } catch (error) {
      console.warn("Failed to save translation cache to localStorage:", error);
    }
  };

  /**
   * Load stats from localStorage
   */
  const loadStatsFromStorage = (): void => {
    try {
      const stored = localStorage.getItem(STATS_STORAGE_KEY);
      if (stored) {
        Object.assign(stats, JSON.parse(stored));
      }
    } catch (error) {
      console.warn(
        "Failed to load translation stats from localStorage:",
        error
      );
    }
  };

  /**
   * Save stats to localStorage
   */
  const saveStatsToStorage = (): void => {
    try {
      localStorage.setItem(STATS_STORAGE_KEY, JSON.stringify(stats));
    } catch (error) {
      console.warn("Failed to save translation stats to localStorage:", error);
    }
  };

  /**
   * Check if cache entry is valid
   */
  const isCacheValid = (cacheKey: string): boolean => {
    const entry = cache[cacheKey];
    return entry && entry.expiresAt > Date.now();
  };

  /**
   * Get cache entry
   */
  const getFromCache = (cacheKey: string): string | null => {
    if (isCacheValid(cacheKey)) {
      stats.cacheHits++;
      stats.lastUsed = Date.now();
      return cache[cacheKey].text;
    }
    return null;
  };

  /**
   * Set cache entry
   */
  const setCache = (cacheKey: string, text: string): void => {
    const now = Date.now();
    cache[cacheKey] = {
      text,
      timestamp: now,
      expiresAt: now + CACHE_DURATION,
    };

    // Save to localStorage after a short delay to avoid excessive writes
    setTimeout(saveCacheToStorage, 1000);
  };

  /**
   * Generate unique ID for translation requests
   */
  const generateTranslationId = (text: string, from: string, to: string): string => {
    return `${from}-${to}-${text.slice(0, 50)}-${Date.now()}`;
  };

  /**
   * Process the translation queue sequentially
   */
  const processQueue = async (): Promise<void> => {
    if (isProcessingQueue.value || translationQueue.length === 0) {
      return;
    }

    isProcessingQueue.value = true;
    isTranslating.value = true;

    while (translationQueue.length > 0) {
      // Sort by priority: high -> normal -> low, then by timestamp
      translationQueue.sort((a, b) => {
        const priorityOrder = { high: 3, normal: 2, low: 1 };
        if (priorityOrder[a.priority] !== priorityOrder[b.priority]) {
          return priorityOrder[b.priority] - priorityOrder[a.priority];
        }
        return a.timestamp - b.timestamp;
      });

      const item = translationQueue.shift();
      if (!item) break;

      try {
        const result = await translateTextDirect(item.text, item.from, item.to);
        item.resolve(result);
      } catch (error) {
        item.reject(error);
      }

      // Remove from active translations
      activeTranslations.delete(item.id);

      // Delay before processing next item
      if (translationQueue.length > 0) {
        await new Promise(resolve => setTimeout(resolve, QUEUE_PROCESS_DELAY));
      }
    }

    isProcessingQueue.value = false;
    isTranslating.value = false;
  };

  /**
   * Add translation to queue
   */
  const addToQueue = (
    text: string,
    from: string,
    to: string,
    priority: 'high' | 'normal' | 'low' = 'normal'
  ): Promise<string> => {
    const id = generateTranslationId(text, from, to);
    
    // Check if already in queue or being processed
    if (activeTranslations.has(id)) {
      return Promise.resolve(text); // Return original text if already processing
    }

    activeTranslations.add(id);

    return new Promise((resolve, reject) => {
      const queueItem: TranslationQueueItem = {
        id,
        text,
        from,
        to,
        priority,
        resolve,
        reject,
        timestamp: Date.now(),
      };

      translationQueue.push(queueItem);
      
      // Start processing if not already running
      if (!isProcessingQueue.value) {
        processQueue();
      }
    });
  };

  /**
   * Clear translation queue
   */
  const clearQueue = (): void => {
    translationQueue.length = 0;
    activeTranslations.clear();
    debounceTimers.forEach(timer => clearTimeout(timer));
    debounceTimers.clear();
  };

  /**
   * Direct translation without queue (for internal use)
   */
  const translateTextDirect = async (
    text: string,
    from = "de",
    to = "en"
  ): Promise<string> => {
    // Return original text if empty
    if (!text || text.trim() === "") {
      return text;
    }

    // Create cache key
    const cacheKey = `${from}-${to}-${text}`;

    // Check cache first
    const cached = getFromCache(cacheKey);
    if (cached) {
      return cached;
    }

    stats.cacheMisses++;
    stats.totalTranslations++;

    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": getCSRFToken(),
        },
        body: JSON.stringify({
          text: text,
          from: from,
          to: to,
        }),
      });

      if (!response.ok) {
        throw new Error(
          `Translation API failed with status: ${response.status}`
        );
      }

      const data = await response.json();
      const translatedText = data.translatedText || text;

      if (
        translatedText &&
        translatedText.trim() !== "" &&
        translatedText !== text
      ) {
        // Cache the translation
        setCache(cacheKey, translatedText);
        saveStatsToStorage();
        return translatedText;
      }

      console.warn("Backend returned empty or same text");
      return text;
    } catch (error) {
      console.error("Translation error:", error);
      // Return original text on error
      return text;
    }
  };

  /**
   * Optimized translation with caching and queueing
   */
  const translateText = async (
    text: string,
    from = "de",
    to = "en",
    priority: 'high' | 'normal' | 'low' = 'normal'
  ): Promise<string> => {
    // Return original text if empty
    if (!text || text.trim() === "") {
      return text;
    }

    // Create cache key
    const cacheKey = `${from}-${to}-${text}`;

    // Check cache first
    const cached = getFromCache(cacheKey);
    if (cached) {
      return cached;
    }

    // Add to queue for processing
    return addToQueue(text, from, to, priority);
  };

  /**
   * Debounced translation function
   */
  const translateTextDebounced = async (
    text: string,
    from = "de",
    to = "en",
    priority: 'high' | 'normal' | 'low' = 'normal',
    debounceKey = text
  ): Promise<string> => {
    // Return original text if empty
    if (!text || text.trim() === "") {
      return text;
    }

    // Check cache first for immediate response
    const cacheKey = `${from}-${to}-${text}`;
    const cached = getFromCache(cacheKey);
    if (cached) {
      return cached;
    }

    // Clear existing debounce timer for this key
    if (debounceTimers.has(debounceKey)) {
      clearTimeout(debounceTimers.get(debounceKey)!);
    }

    return new Promise((resolve) => {
      const timer = setTimeout(async () => {
        try {
          const result = await translateText(text, from, to, priority);
          resolve(result);
        } catch (error) {
          console.error('Debounced translation error:', error);
          resolve(text);
        } finally {
          debounceTimers.delete(debounceKey);
        }
      }, DEBOUNCE_DELAY);

      debounceTimers.set(debounceKey, timer);
    });
  };

  /**
   * Translate multiple texts at once with batching
   */
  const translateTexts = async (
    texts: string[],
    from = "de",
    to = "en",
    priority: 'high' | 'normal' | 'low' = 'normal'
  ): Promise<string[]> => {
    // Use the queue system for all translations
    const results = await Promise.all(
      texts.map((text) => translateText(text, from, to, priority))
    );
    return results;
  };

  /**
   * Clear translation cache
   */
  const clearCache = (): void => {
    Object.keys(cache).forEach((key) => {
      delete cache[key];
    });
    localStorage.removeItem(LOCAL_STORAGE_KEY);
    console.log("Translation cache cleared");
  };

  /**
   * Clear expired cache entries
   */
  const cleanupExpiredCache = (): void => {
    const now = Date.now();
    let cleanedCount = 0;

    Object.keys(cache).forEach((key) => {
      if (cache[key].expiresAt <= now) {
        delete cache[key];
        cleanedCount++;
      }
    });

    if (cleanedCount > 0) {
      saveCacheToStorage();
      console.log(`Cleaned up ${cleanedCount} expired cache entries`);
    }
  };

  /**
   * Get cache statistics
   */
  const getCacheStats = () => {
    const now = Date.now();
    const validEntries = Object.values(cache).filter(
      (entry) => entry.expiresAt > now
    ).length;
    const expiredEntries = Object.values(cache).filter(
      (entry) => entry.expiresAt <= now
    ).length;

    return {
      ...stats,
      validCacheEntries: validEntries,
      expiredCacheEntries: expiredEntries,
      totalCacheEntries: Object.keys(cache).length,
      cacheHitRate:
        stats.totalTranslations > 0
          ? ((stats.cacheHits / stats.totalTranslations) * 100).toFixed(2) + "%"
          : "0%",
    };
  };

  /**
   * Check if text is likely German using backend API
   */
  const isGerman = async (text: string): Promise<boolean> => {
    try {
      const response = await fetch("/api/is-german", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": getCSRFToken(),
        },
        body: JSON.stringify({ text }),
      });

      if (response.ok) {
        const data = await response.json();
        return data.isGerman || false;
      }
    } catch (error) {
      console.warn("German detection failed:", error);
    }

    // Fallback to client-side detection
    const germanWords = [
      "der",
      "die",
      "das",
      "und",
      "ist",
      "mit",
      "für",
      "auf",
      "zu",
      "von",
      "bei",
      "auch",
      "eine",
      "einen",
      "einer",
    ];
    const words = text.toLowerCase().split(/\s+/);
    const germanWordCount = words.filter((word) =>
      germanWords.includes(word)
    ).length;
    return germanWordCount > 0 || /[äöüß]/.test(text);
  };

  /**
   * Auto-translate text if it appears to be German
   */
  const autoTranslate = async (
    text: string,
    priority: 'high' | 'normal' | 'low' = 'normal'
  ): Promise<string> => {
    if (await isGerman(text)) {
      return await translateText(text, "de", "en", priority);
    }
    return text;
  };

  /**
   * Auto-translate with debouncing
   */
  const autoTranslateDebounced = async (
    text: string,
    priority: 'high' | 'normal' | 'low' = 'normal',
    debounceKey = text
  ): Promise<string> => {
    if (await isGerman(text)) {
      return await translateTextDebounced(text, "de", "en", priority, debounceKey);
    }
    return text;
  };

  /**
   * Preload common translations
   */
  const preloadCommonTranslations = async (
    commonTexts: string[],
    from = "de",
    to = "en"
  ): Promise<void> => {
    console.log("Preloading common translations...");
    await translateTexts(commonTexts, from, to);
    console.log("Common translations preloaded");
  };

  return {
    translateText,
    translateTextDebounced,
    translateTexts,
    autoTranslate,
    autoTranslateDebounced,
    isTranslating,
    isProcessingQueue,
    cache,
    clearCache,
    clearQueue,
    cleanupExpiredCache,
    isGerman,
    getCacheStats,
    preloadCommonTranslations,
  };
}
