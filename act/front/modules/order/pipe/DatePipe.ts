import { Pipe, PipeTransform } from '@angular/core';
import * as moment from "moment";
@Pipe({
    name: 'datePipe'
})
export class DatePipe implements PipeTransform {
    transform(date): string {
        return moment(date, 'DD/MM/YYYY').format().substring(0, 10);
    }
}