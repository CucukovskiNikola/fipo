export const useImageCompression = () => {
  /**
   * Convert and compress image to WebP format
   * @param {File} file - Original image file
   * @param {Object} options - Compression options
   * @param {Function} onProgress - Progress callback (0-100)
   * @returns {Promise<File>} - Compressed WebP file
   */
  const convertToWebP = (file, options = {}, onProgress = null) => {
    return new Promise((resolve, reject) => {
      const {
        quality = 0.8, // 80% quality
        maxWidth = 1920,
        maxHeight = 1080,
        filename = null
      } = options;

      // Create canvas element
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();

      img.onload = () => {
        if (onProgress) onProgress(20); // Image loaded
        
        // Calculate new dimensions while maintaining aspect ratio
        let { width, height } = img;
        
        if (width > maxWidth || height > maxHeight) {
          const aspectRatio = width / height;
          
          if (width > height) {
            width = maxWidth;
            height = width / aspectRatio;
          } else {
            height = maxHeight;
            width = height * aspectRatio;
          }
        }

        if (onProgress) onProgress(40); // Dimensions calculated

        // Set canvas dimensions
        canvas.width = width;
        canvas.height = height;

        if (onProgress) onProgress(60); // Canvas prepared

        // Draw and compress image
        ctx.drawImage(img, 0, 0, width, height);
        
        if (onProgress) onProgress(80); // Image drawn

        // Convert to WebP blob
        canvas.toBlob(
          (blob) => {
            if (blob) {
              if (onProgress) onProgress(95); // Conversion complete
              
              const webpFilename = filename || file.name.replace(/\.[^/.]+$/, '.webp');
              const webpFile = new File([blob], webpFilename, {
                type: 'image/webp',
                lastModified: Date.now()
              });
              
              const compressionRatio = ((1 - webpFile.size / file.size) * 100).toFixed(1);
              console.log(`Image compressed: ${(file.size / 1024).toFixed(1)}KB → ${(webpFile.size / 1024).toFixed(1)}KB (${compressionRatio}% reduction)`);
              
              if (onProgress) onProgress(100); // Complete
              resolve(webpFile);
            } else {
              reject(new Error('Failed to convert image to WebP'));
            }
          },
          'image/webp',
          quality
        );
      };

      img.onerror = () => {
        reject(new Error('Failed to load image'));
      };

      if (onProgress) onProgress(10); // Starting
      img.src = URL.createObjectURL(file);
    });
  };

  /**
   * Process multiple images
   * @param {FileList|File[]} files - Array of image files
   * @param {Object} options - Compression options
   * @param {Function} onProgress - Progress callback for overall progress
   * @returns {Promise<File[]>} - Array of compressed WebP files
   */
  const processImages = async (files, options = {}, onProgress = null) => {
    const fileArray = Array.from(files);
    const processedImages = [];
    const totalFiles = fileArray.length;

    for (let i = 0; i < fileArray.length; i++) {
      const file = fileArray[i];
      
      try {
        // Check if file is an image
        if (!file.type.startsWith('image/')) {
          console.warn(`Skipping non-image file: ${file.name}`);
          if (onProgress) {
            const overallProgress = ((i + 1) / totalFiles) * 100;
            onProgress(overallProgress, i + 1, totalFiles, 'skipped');
          }
          continue;
        }

        // Skip if already WebP and small enough
        if (file.type === 'image/webp' && file.size <= (options.maxSize || 500000)) {
          processedImages.push(file);
          if (onProgress) {
            const overallProgress = ((i + 1) / totalFiles) * 100;
            onProgress(overallProgress, i + 1, totalFiles, 'skipped_optimized');
          }
          continue;
        }

        // Progress callback for individual file
        const fileProgress = (progress) => {
          if (onProgress) {
            const baseProgress = (i / totalFiles) * 100;
            const fileContribution = (progress / 100) * (100 / totalFiles);
            const overallProgress = baseProgress + fileContribution;
            onProgress(overallProgress, i + 1, totalFiles, 'processing', file.name);
          }
        };

        const compressedImage = await convertToWebP(file, options, fileProgress);
        processedImages.push(compressedImage);
        
        if (onProgress) {
          const overallProgress = ((i + 1) / totalFiles) * 100;
          onProgress(overallProgress, i + 1, totalFiles, 'completed', file.name);
        }
      } catch (error) {
        console.error(`Failed to process image ${file.name}:`, error);
        if (onProgress) {
          const overallProgress = ((i + 1) / totalFiles) * 100;
          onProgress(overallProgress, i + 1, totalFiles, 'error', file.name);
        }
        // Optionally include original file as fallback
        // processedImages.push(file);
      }
    }

    return processedImages;
  };

  /**
   * Get image dimensions
   * @param {File} file - Image file
   * @returns {Promise<{width: number, height: number}>}
   */
  const getImageDimensions = (file) => {
    return new Promise((resolve) => {
      const img = new Image();
      img.onload = () => {
        resolve({ width: img.width, height: img.height });
      };
      img.src = URL.createObjectURL(file);
    });
  };

  /**
   * Get default compression settings with descriptions
   */
  const getCompressionPresets = () => {
    return {
      // High quality for important images
      high_quality: {
        quality: 0.9,
        maxWidth: 2560,
        maxHeight: 1440,
        description: 'High Quality - Best for hero images, portfolios'
      },
      
      // Standard quality for general use
      standard: {
        quality: 0.8,
        maxWidth: 1920,
        maxHeight: 1080,
        description: 'Standard - Good balance of quality and size'
      },
      
      // Optimized for web performance
      web_optimized: {
        quality: 0.75,
        maxWidth: 1200,
        maxHeight: 800,
        description: 'Web Optimized - Fast loading, good quality'
      },
      
      // Small file sizes
      compact: {
        quality: 0.65,
        maxWidth: 800,
        maxHeight: 600,
        description: 'Compact - Smallest files, acceptable quality'
      },
      
      // Thumbnails
      thumbnail: {
        quality: 0.7,
        maxWidth: 400,
        maxHeight: 300,
        description: 'Thumbnail - Perfect for previews and thumbnails'
      }
    };
  };

  /**
   * Custom compression settings builder
   */
  const createCustomSettings = ({
    quality = 0.8,
    maxWidth = 1920,
    maxHeight = 1080,
    maxSize = null,
    maintainAspectRatio = true,
    filename = null
  } = {}) => {
    return {
      quality: Math.max(0.1, Math.min(1.0, quality)), // Ensure 0.1-1.0 range
      maxWidth: Math.max(100, maxWidth), // Minimum 100px width
      maxHeight: Math.max(100, maxHeight), // Minimum 100px height
      maxSize,
      maintainAspectRatio,
      filename
    };
  };

  return {
    convertToWebP,
    processImages,
    getImageDimensions,
    getCompressionPresets,
    createCustomSettings
  };
};