import { Component, OnInit, EventEmitter, HostListener, ViewChild, ElementRef, ViewEncapsulation, trigger, transition, style, animate, Input, AfterViewInit, AfterViewChecked, ViewChildren, QueryList } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { Calendar, DataTable, Dropdown, MultiSelect } from 'primeng/primeng';
import { ActeService } from '../../../shared/services/acte.services';
import { Table } from 'primeng/table';
import { MenuItem, MessageService } from 'primeng/api';
import { SortEvent } from 'primeng/api';
import { Order } from '../../../shared/entities/order';
import { DocumentService } from '../../../shared/services/document.services';
import { SignataireService } from '../../../shared/services/signataire.services';
import { AvocatService } from '../../../shared/services/avocat.services';
import * as moment from "moment";
import { and } from "@angular/router/src/utils/collection";
import { equal, notDeepEqual, notEqual } from "assert";
import { User } from '../../../shared/entities/user';
import { debug } from 'util';
import { Column } from '../../../shared/models/Column';
import { Avocat } from '../../../shared/entities/avocat';
import { Signataire } from '../../../shared/entities/signataire';
import { CommonModel } from '../../../shared/entities/commonModel';
import { BaseViewModel } from './Static/BaseViewModel';
import { SearchBarUser } from '../../../shared/entities/searchBarUser';

export const fadeInOut = (name = 'fadeInOut', duration = 0.1) =>
    trigger(name, [
        transition(':enter', [
            style({ opacity: 0 }),
            animate(`${duration}s ease-in-out`)
        ]),
        transition(':leave', [animate(`${duration}s ease-in-out`, style({ opacity: 0 }))])
    ])
declare const $: any;

@Component({
    selector: 'allResult',
    templateUrl: './AllResultComponent.html',
    styleUrls: ['./ListContactComponent.css'],
    providers: [MessageService],
})

export class AllResultComponent implements OnInit {

    @ViewChildren('cmp') components: QueryList<MultiSelect>;
    @ViewChild('tableOrders') tableOrders: Table;
    selectedType = [];
    columns: Array<Column>;
    listeContact: any[] = [];
    allAct: Order[] = [];
    sort: string = "0";
    filterChocieType: string;
    filterChocieNature: string;
    noRecordMsgFlag: boolean = false;

    constructor(private messageService: MessageService, private avocatService: AvocatService, private signataireService: SignataireService, private orderService: ActeService
    ) {

        //  this.choicesNature.map((item) => this.selectedType.push(item));
    }

    ngOnInit(): void {
        this.sort = "0";
        this.filterChocieType = null;
        this.filterChocieNature = null;
        this.columns = [
            //isFrozen,isSortableDisabled, isReorderableDisabled, header, colkey,width,colspan.
            new Column(false, true, false, 'Nom', 'lastName', '50%', 3),
            new Column(false, true, false, 'Type', 'type', '50%', 3),
        ];
    }
    get choicesNature(): any[] {
        return [
            { label: 'Acte (' + this.AllData.filter(elem => elem.nameActe).length + ')', value: '0' },
            { label: 'Document (' + this.AllData.filter(elem => elem.documentName).length + ')', value: '1' },
            { label: 'Contact (' + this.AllData.filter(elem => elem.contactName).length + ')', value: '2' }
        ];
    }
    isEmpty(event: any) {
        if (event.filteredValue.length == 0) {
            this.noRecordMsgFlag = true;
        }
        else {
            this.noRecordMsgFlag = false;
        }
    }

    styleObject(col: any): Object {
        if (col.header == 'statut') {
            return { 'width': col.width, 'text-align': 'center' }
        }
        return { 'width': col.width }
    }


    filter(elm) {
        let array = Array.from(elm, elme => elme["value"]);
        this.tableOrders.filter(Array.from(elm, elme => elme["value"]), 'type', 'in')
    }
    onReset(event, tableOrders) {
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

    get displayAllResult() {
        return BaseViewModel.displayAllResult;
    }
    set displayAllResult(displayAllResult: boolean) {
        BaseViewModel.displayAllResult = displayAllResult;
    }

    get inputReasearchBar() {
        return BaseViewModel.inputReasearchBar;
    }
    set inputReasearchBar(inputReasearchBar: string) {
        BaseViewModel.inputReasearchBar = inputReasearchBar;
    }

    get displayDivForReasearchBar() {
        return BaseViewModel.displayDivForReasearchBar;
    }
    set displayDivForReasearchBar(displayDivForReasearchBar: boolean) {
        BaseViewModel.displayDivForReasearchBar = displayDivForReasearchBar;
    }
    get allData() {
        return BaseViewModel.allData;
    }
    set allData(allData: CommonModel[]) {
        BaseViewModel.allData = allData;
    }
    set modeModify(modeModify: boolean) {
        BaseViewModel.modeModify = modeModify;
    }

    get displayContactAdd(): boolean {
        return BaseViewModel.displayContactAdd;
    }
    set displayContactAdd(displayContactAdd: boolean) {
        BaseViewModel.displayContactAdd = displayContactAdd;
    }

    get allContact() {
        return BaseViewModel.allContact;
    }
    set allContact(allContact: Signataire[]) {
        BaseViewModel.allContact = allContact;
    }

    setContactTConsylt(contact: any): void {
        if (contact["type"] && (contact["type"] == 0 || contact["type"] == 1)) {
            this.modeModify = true;
            let reasearchUser = new SearchBarUser();
            reasearchUser.idUser = "" + BaseViewModel.userConnected.id;
            reasearchUser.type = contact["type"];
            reasearchUser.idEntity = contact["id"];
            let searchBarUser = this.orderService.postSearchBar(reasearchUser);
            Promise.all([searchBarUser]).then(results => {
            });
            window.location.href = '/myact';
        }
        else {
            contact = this.allContact.filter(item => item.id == contact.id)[0];
            if (contact.enterpriseName == 'null') {
                contact.enterpriseName = null;
            }
            BaseViewModel.contactToModify = contact;
            this.displayContactAdd = true;
        }
    }

    get AllData() {
        return this.allData.filter(it =>
            (it.nameActe && it.nameActe.toLowerCase().includes(this.inputReasearchBar.toLowerCase())) ||
            (it.folderNumber && (it.folderNumber.toLowerCase().includes(this.inputReasearchBar.toLowerCase()))) ||
            (it.folderName && (it.folderName.toLowerCase().includes(this.inputReasearchBar.toLowerCase()))) ||
            (it.internalNumber && (it.internalNumber.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.documentName && (it.documentName.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.contactName && (it.contactName.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.contactLastName && (it.contactLastName.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.contactPhoneNumber && (it.contactPhoneNumber.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.contactMil && (it.contactMil.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )) ||
            (it.contactMil && (it.nature.toLowerCase().includes(this.inputReasearchBar.toLowerCase()) )))
    }
}
