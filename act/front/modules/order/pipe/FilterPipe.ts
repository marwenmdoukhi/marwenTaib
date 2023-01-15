import { Pipe, PipeTransform } from '@angular/core';
@Pipe({
    name: 'filter'
})
export class FilterPipe implements PipeTransform {
    transform(items: any[], argument: string, searchText: string): any[] {
        if (!items) return [];
        if (!searchText) return [];
        if (!argument) return [];
        searchText = searchText.toLowerCase();
        return items.filter(it => {
            return it[argument + ""].toLowerCase().includes(searchText);
        });
    }
}