import {
    animate,
    Component,
    Input,
    OnInit,
    QueryList,
    style,
    transition,
    trigger,
    ViewChild,
    ViewChildren
} from '@angular/core';
import {MultiSelect} from 'primeng/primeng';
import {Table} from 'primeng/table';
import {MessageService} from 'primeng/api';
import {Order} from '../../../shared/entities/order';
import {Column} from '../../../shared/models/Column';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';

export const fadeInOut = (name = 'fadeInOut', duration = 0.1) =>
    trigger(name, [
        transition(':enter', [
            style({opacity: 0}),
            animate(`${duration}s ease-in-out`)
        ]),
        transition(':leave', [animate(`${duration}s ease-in-out`, style({opacity: 0}))])
    ])
declare const $: any;

@Component({
    selector: 'allResult',
    templateUrl: './AllResultComponent.html',
    styleUrls: ['./OrdersComponent.css'],
    providers: [MessageService],
})

export class AllResultComponent implements OnInit {


    @ViewChildren('cmp') components: QueryList<MultiSelect>;


    @ViewChild('tableOrders') tableOrders: Table;
    @Input() vm: IOrdersViewModelMaster;
    selectedType = [];
    columns: Array<Column>;
    listeContact: any[] = [];
    allAct: Order[] = [];
    sort: string = "0";
    filterChocieType: string;
    filterChocieNature: string;
    noRecordMsgFlag: boolean = false;

    constructor() {
    }

    get choicesNature(): any[]
    {
        return [
            { label: 'Acte (' + this.AllData.filter(elem => elem.nameActe).length + ')', value: '0' },
            { label: 'Document (' + this.AllData.filter(elem => elem.documentName).length + ')', value: '1' },
            { label: 'Contact (' + this.AllData.filter(elem => elem.contactName).length + ')', value: '2' }
        ];
    }
    ngOnInit(): void {
        console.log(this.vm);
        this.sort = "0";
        this.filterChocieType = null;
        this.filterChocieNature = null;
        this.columns = [
            new Column(false, true, false, 'Nom', 'lastName', '50%', 3),
            new Column(false, true, false, 'Type', 'type', '50%', 3),
        ];
    }

    text3(): string {

        return decodeURIComponent('Aucun élément ne correspond à votre recherche');
    }

    text2(): string {
        
        return decodeURIComponent('Voir tous les résultats pour «'+ this.vm.inputReasearchBar+'»');
    }

    get text1():string {
        return decodeURIComponent('résultat');
    }

    styleObject(col: any): Object {
        if (col.header == 'statut') {
            return { 'width': col.width, 'text-align': 'center' }
        }
        return { 'width': col.width }
    }

    isEmpty(event: any) {
        if (event.filteredValue.length == 0) {
            this.noRecordMsgFlag = true;
        }
        else {
            this.noRecordMsgFlag = false;
        }
    }

    get AllData() {
        return this.vm.allData.filter(it =>
            (it.nameActe && (it.nameActe.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.folderNumber && (it.folderNumber.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.folderName && (it.folderName.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.internalNumber && (it.internalNumber.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.documentName && (it.documentName.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.contactName && (it.contactName.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.contactLastName && (it.contactLastName.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.contactPhoneNumber && (it.contactPhoneNumber.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.contactMil && (it.contactMil.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase())  )) ||
            (it.contactMil && (it.nature.toLowerCase().includes(this.vm.inputReasearchBar.toLowerCase()) )))
    }



    filter(elm) {
        this.tableOrders.filter(Array.from(elm, elme => elme["value"]), 'type', 'in')
    }
    onReset(event) {
        this.selectedType = this.selectedType.filter(elm => elm.value != event);
        this.tableOrders.filter(Array.from(this.selectedType, elme => elme["value"]), 'type', 'in');
    }
    onResetAll(event, dt) {
        this.components['_results'].forEach(ds => {
            ds.value = null;
            ds.updateLabel();
        });
        this.selectedType = [];
        this.tableOrders.reset();
        this.noRecordMsgFlag = false;
    }

    set displayCreateOrder(displayCreateOrder: boolean) {
        OrdersViewModelMaster.displayCreateOrder = displayCreateOrder;
    }

    get displayCreateOrder(): boolean {
        return OrdersViewModelMaster.displayCreateOrder;
    }
    set displaySendTovalidation(displaySendTovalidation: boolean) {
        OrdersViewModelMaster.displaySendTovalidation = displaySendTovalidation;
    }
    get displaySendTovalidation(): boolean {
        return OrdersViewModelMaster.displaySendTovalidation;
    }
    set displayConsultOrder(displayConsultOrder: boolean) {
        OrdersViewModelMaster.displayConsultOrder = displayConsultOrder;
    }

    get displayConsultOrder(): boolean {
        return OrdersViewModelMaster.displayConsultOrder;
    }
}
