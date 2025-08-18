export function useInitials() {
  const getInitials = (name: string): string => {
    if (!name) return '';
    
    return name
      .split(' ')
      .map(part => part.charAt(0).toUpperCase())
      .slice(0, 2)
      .join('');
  };

  return {
    getInitials
  };
}