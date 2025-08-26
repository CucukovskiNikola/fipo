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

export function useLibreTranslate() {
  const isTranslating = ref(false);
  const cache: TranslationCache = reactive({});
  const stats = reactive<TranslationStats>({
    totalTranslations: 0,
    cacheHits: 0,
    cacheMisses: 0,
    lastUsed: Date.now(),
  });

  // Cache configuration
  const CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
  const LOCAL_STORAGE_KEY = "translation_cache";
  const STATS_STORAGE_KEY = "translation_stats";

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
   * Translate text using our backend API
   */
  const translateText = async (
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

    // Set translating state
    isTranslating.value = true;

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
    } finally {
      isTranslating.value = false;
    }
  };

  /**
   * Translate multiple texts at once with batching
   */
  const translateTexts = async (
    texts: string[],
    from = "de",
    to = "en",
    batchSize = 5
  ): Promise<string[]> => {
    const results: string[] = [];

    // Process in batches to avoid overwhelming the API
    for (let i = 0; i < texts.length; i += batchSize) {
      const batch = texts.slice(i, i + batchSize);
      const batchResults = await Promise.all(
        batch.map((text) => translateText(text, from, to))
      );
      results.push(...batchResults);

      // Small delay between batches
      if (i + batchSize < texts.length) {
        await new Promise((resolve) => setTimeout(resolve, 100));
      }
    }

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
  const autoTranslate = async (text: string): Promise<string> => {
    if (await isGerman(text)) {
      return await translateText(text);
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
    translateTexts,
    autoTranslate,
    isTranslating,
    cache,
    clearCache,
    cleanupExpiredCache,
    isGerman,
    getCacheStats,
    preloadCommonTranslations,
  };
}
