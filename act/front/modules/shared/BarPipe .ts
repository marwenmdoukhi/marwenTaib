import { Pipe, PipeTransform } from '@angular/core';
@Pipe({
    name: 'barPipe'
})
export class BarPipe implements PipeTransform {
    transform(items: any[], searchText: string): any[] {
        if (!items) return [];
        if (!searchText) return [];
        searchText = searchText.toLowerCase();
        return items.filter(it => {
            return (it.nameActe && (it.nameActe.toLowerCase().includes(searchText)) ) ||
                (it.folderNumber && (it.folderNumber.toLowerCase().includes(searchText)  )) ||
                (it.folderName && (it.folderName.toLowerCase().includes(searchText)  )) ||
                (it.internalNumber && (it.internalNumber.toLowerCase().includes(searchText) )) ||
                (it.documentName && (it.documentName.toLowerCase().includes(searchText)  )) ||
                (it.contactName && (it.contactName.toLowerCase().includes(searchText)  )) ||
                (it.contactLastName && (it.contactLastName.toLowerCase().includes(searchText)  )) ||
                (it.contactPhoneNumber && (it.contactPhoneNumber.toLowerCase().includes(searchText)  )) ||
                (it.contactMil && (it.contactMil.toLowerCase().includes(searchText)  )) ||
                (it.contactMil && (it.nature.toLowerCase().includes(searchText)  ))
        });
    }
}