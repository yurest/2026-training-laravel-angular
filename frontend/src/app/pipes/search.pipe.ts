import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'search',
  standalone: true,
  pure: true,
})
export class SearchPipe implements PipeTransform {
  public transform<T>(items: T[] | null | undefined, query: string, properties: (keyof T)[]): T[] {
    if (!items) {
      return [];
    }

    if (!query || !query.trim()) {
      return items;
    }

    const lowerQuery = query.toLowerCase().trim();

    return items.filter((item) =>
      properties.some((property) => {
        const value = item[property];

        if (value === null || value === undefined) {
          return false;
        }

        return String(value).toLowerCase().includes(lowerQuery);
      }),
    );
  }
}
