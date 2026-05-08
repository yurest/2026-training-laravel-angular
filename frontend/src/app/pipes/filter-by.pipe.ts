import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'filterBy',
  standalone: true,
  pure: true,
})
export class FilterByPipe implements PipeTransform {
  public transform<T>(items: T[] | null | undefined, property: keyof T, value: unknown): T[] {
    if (!items) {
      return [];
    }

    if (value === null || value === undefined || value === '') {
      return items;
    }

    return items.filter((item) => item[property] === value);
  }
}
