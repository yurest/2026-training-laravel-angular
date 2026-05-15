export function extractArrayFromResponse<T = any>(
  response: any,
  key: string,
): T[] {
  if (Array.isArray(response)) return response;
  if (Array.isArray(response?.data)) return response.data;
  if (Array.isArray(response?.[key])) return response[key];

  return [];
}