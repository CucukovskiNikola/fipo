import { type ClassValue, clsx } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

/**
 * Generate a localized partner URL based on the current locale
 */
export function getLocalizedPartnerUrl(partnerId: number, partnerTitle: string, locale: string = 'de'): string {
  const slug = createSlug(partnerTitle);
  
  if (locale === 'en') {
    return `/en/partners/${partnerId}`;
  }
  
  return `/partners/${partnerId}`;
}

/**
 * Create a URL-friendly slug from a title
 */
export function createSlug(title: string): string {
  return title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");
}
