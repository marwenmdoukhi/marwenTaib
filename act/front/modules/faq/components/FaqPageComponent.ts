import { Component, OnInit, EventEmitter, HostListener, ViewChild, ElementRef, ViewEncapsulation, trigger, transition, style, animate, Input, AfterViewInit, AfterViewChecked,NgZone } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { Calendar, DataTable, Dropdown } from 'primeng/primeng';
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
import {CookieService} from "ngx-cookie-service";
import {NgxSpinnerService} from "ngx-spinner";


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
    selector: 'faq',
    templateUrl: './FaqComponent.html',
    styleUrls: ['./FaqPageComponent.css'],
    providers: [MessageService],
    animations: [
        fadeInOut('fadeInOut-3', 3)
    ]
})

export class FaqPageComponent implements OnInit {

    @ViewChild('tableOrders') tableOrders: Table;
    columns: Array<Column>;
    listeContact: any[] = [];
    choicesNature = [
        { label: 'Nature de contact', value: null },
        { label: 'Avocat', value: 'ROLE_COUNSEL' },
        { label: 'Signataire', value: 'ROLE_SIGNATORY' },
    ];
    choicesType = [
        { label: 'Type de contact', value: null },
        { label: 'Morale', value: 'NotNull' },
        { label: 'Physique', value: 'null' },
    ];
    sort: string = "0";
    filterChocieType: string;
    filterChocieNature: string;
    noRecordMsgFlag: boolean = false;
    faq :string;
    manual : string;
    showSpinner : boolean = false;
    isCnbPhone: boolean = false;
    isCnbEmail: boolean = false;
    hideMsg: boolean = false;
    errorMsg: boolean = false;
    disableBtn: boolean = false;
    disableBtnPhone : boolean = false;
    hidePhoneMsg: boolean = false;
    errorPhoneMsg: boolean = false;
    displayCX: boolean = true;
    settingsBtn:boolean = false;
    userConnected : any;
    disableBtnEmail:boolean = false;
    constructor(private messageService: MessageService, private avocatService: AvocatService, private signataireService: SignataireService,private acteService : ActeService,private spinner: NgxSpinnerService,private ngZone : NgZone
    ) {

    }

    ngOnInit(): void {
        this.manuals();
        window['angularComponentReference'] = { component: this, zone: this.ngZone, loadAngularFunctionforFaq: () => this.showSettings(), };

        this.columns = [
            //isFrozen,isSortableDisabled, isReorderableDisabled, header, colkey,width,colspan.
            new Column(false, true, false, 'Nom', 'lastName', '20px', 3),
            new Column(false, true, false, 'N portable annuaire', 'phoneNumberApp', '15px', 3),
            new Column(false, true, false, 'adresse mail annuaire', 'emailApp', '15px', 3),
            new Column(false, true, false, 'N portable', 'phoneNumber', '15px', 3),
            new Column(false, true, false, 'adresse mail', 'email', '15px', 3),
            new Column(false, true, false, 'svg', 'svg', '15px', 3),
        ];
    }
    getLetter(contact: Avocat): string {
        if (this.listeContact.indexOf(contact) == 0 || (this.listeContact.indexOf(contact) != 0 && contact.name.charAt(0).toLocaleUpperCase() != this.listeContact[this.listeContact.indexOf(contact) - 1].name.charAt(0).toLocaleUpperCase())) {
            return contact.name.charAt(0).toLocaleUpperCase();
        }
        else {
            return '';
        }
    }

    //Fonction customis�e pour trier les dates des actes.
    customSortDate(source: any, order: boolean, field: string) {
        source.sort((a, b) => {
            if (b[field] && a[field]) {
                let result = moment(b[field], 'DD/MM/YYYY').diff(moment(a[field], 'DD/MM/YYYY'));
                return order ? -result : result;
            }
            else if (!b[field]) {
                let result = moment('01/01/1900', 'DD/MM/YYYY').diff(moment(a[field], 'DD/MM/YYYY'));
                return order ? -result : result;
            }
            else {
                let result = moment(b[field], 'DD/MM/YYYY').diff(moment('01/01/1900', 'DD/MM/YYYY'));
                return order ? -result : result;
            }
        });
    }
    //ngAfterViewChecked() {
    //    if (this.tableOrders) {
    //        this.tableOrders.filterConstraints['equalAvocat'] = (value, filter): boolean => {
    //            return value == filter;
    //        }
    //    }
    //}

    onChangeFiltre(filtre: any) {
        if (filtre == "1") {
            this.tableOrders.lazy = false;
            //this.tableOrders.reset();
            this.tableOrders._sortOrder = -1;
            this.tableOrders.sortField = 'name';
            //this.listeContact.sort(function (a, b) {
            //    if (a.name < b.name) { return 1; }
            //    if (a.name > b.name) { return -1; }
            //    return 0;
            //})
        }
        else if (filtre == "0") {
            this.tableOrders.lazy = false;
            //this.tableOrders.reset();
            this.tableOrders._sortOrder = 1;
            this.tableOrders.sortField = 'name';
            //this.listeContact.sort(function (a, b) {
            //    if (a.name < b.name) { return -1; }
            //    if (a.name > b.name) { return 1; }
            //    return 0;
            //})

        }
    }
    showSettings(){
        this.settingsBtn = true;
        this.displayCX = true;

    }
    annuler(){
        let userQuery = this.acteService.getUserconnectedAsync();
        Promise.all([userQuery]).then(results => {
            this.userConnected=results[0];
        });
    }
    checkinput() {
        let inp = this.userConnected.emailApp;
        let patternEmail = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,10}$/;
        if (inp === "" || inp === null) {
            this.hideMsg = true;
            this.errorMsg = false;
            this.disableBtnEmail = true;
        } else if (patternEmail.test(inp) === false) {
            this.errorMsg = true;
            this.hideMsg = false;
            this.disableBtnEmail = true;
        } else {
            this.errorMsg = false;
            this.hideMsg = false;
            this.disableBtnEmail = false;
        }
    }

    checkPhone() {
        let phone = this.userConnected.phoneNumberApp;
        let phoneLength = (this.userConnected.phoneNumberApp + '').length;
        if (phoneLength <= 1 || phone == null) {
            this.hidePhoneMsg = true;
            this.disableBtn = true;
        } else if (phoneLength > 23) {
            this.errorPhoneMsg = true;
            this.disableBtn = true;
        } else {
            this.disableBtn = false;
            this.hidePhoneMsg = false;

        }

    }

    checkEmailCnb() {
        if (this.userConnected.email.replace(/^0+/, '') == this.userConnected.cnbId + 'cnb@cnb.fr' || this.userConnected.email.replace(/^0+/, '') == this.userConnected.cnbId + '@cnb.fr') {
            this.isCnbEmail = true;
            if (this.isCnbEmail === true && this.userConnected.emailApp === null) {
                this.disableBtn = true;
            } else if (this.isCnbEmail === true) {
                this.checkinput();
                this.checkPhone();
            }
        } else {
            let inp = this.userConnected.emailApp;
            let patternEmail = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,10}$/;
            if (this.userConnected.emailApp !== null && this.userConnected.emailApp !=="") {
                if (patternEmail.test(inp) !== false) {
                    this.errorMsg = false;
                    this.hideMsg = false;
                    this.disableBtn = false;
                    this.checkPhone();
                } else {
                    this.errorMsg = true;
                    this.hideMsg = false;
                    this.disableBtn = true;
                }
            } else {
                this.errorMsg = false;
                this.hideMsg = false;
                this.disableBtn = false;
                this.checkPhone();
            }
        }
    }
    checkCnbMobile() {
        if (this.userConnected.phoneNumber === null) {
            this.isCnbPhone = true;
            if (this.isCnbPhone === true && this.userConnected.phoneNumberApp === null) {
                this.disableBtn = true;
            } else if (this.isCnbPhone === true && this.userConnected.phoneNumberApp !== null) {
                this.checkPhone();
                this.checkEmailCnb();
            }
        }
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

    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        return arr
    }

    initializeFilterAndSorting(dropdown: Dropdown) {
        this.sort = '0';
        this.tableOrders.reset();
        this.listeContact.sort(function (a, b) {
            if (a.name < b.name) { return -1; }
            if (a.name > b.name) { return 1; }
            return 0;
        })
        this.filterChocieType = null;
        this.filterChocieNature = null;
        this.noRecordMsgFlag = false;
        if (dropdown) {
            dropdown.resetFilter();
        }
    }
    filterTypeContact() {
        if (this.filterChocieType == 'null') {
            this.tableOrders.filter('null', 'enterpriseName', 'notEquals')
        }
        else {
            this.tableOrders.filter('null', 'enterpriseName', 'equals')
        }
    }

    manuals(){
        let faq = this.acteService.getManuals();
        let userQuery = this.acteService.getUserconnectedAsync();

        Promise.all([faq,userQuery, this.spinner.show()]).then(results => {
             this.faq = results[0][0].content;
            this.manual = results[0][1].content;
            this.userConnected = results[1];
            this.spinner.hide();
            this.checkCnbMobile();
            this.checkEmailCnb();
        });
    }

    viewPdf() {
        let pdfWindow = window.open("");
        pdfWindow.document.write("<iframe  width='100%' height='100%' src='/logo/manuel.pdf'></iframe>")
    }

    downloadPDF() {
        return '/logo/manuel.pdf';
    }

    toggleSidebar() {
        let wrapperElement = document.querySelector('.wrapper');
        if (wrapperElement !== undefined) {
            wrapperElement.classList.toggle('menu-open');
        }
    }


    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }




}