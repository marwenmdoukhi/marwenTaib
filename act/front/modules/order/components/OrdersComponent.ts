import {
    AfterViewInit,
    animate,
    Component,
    ElementRef,
    Input,
    style,
    transition,
    trigger,
    ViewChild,
    ViewChildren,
    NgZone
} from '@angular/core';
import {Dropdown} from 'primeng/primeng';
import {Table} from 'primeng/table';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import * as moment from "moment";
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {User} from '../../../shared/entities/user';
import {Observable} from "rxjs";
import {debounceTime, distinctUntilChanged, map, startWith, tap} from "rxjs/operators";
import {ActeService} from "../../../shared/services/acte.services";


export const fadeInOut = (name = 'fadeInOut', duration = 0.1) =>
    trigger(name, [
        transition(':enter', [
            style({opacity: 0}),
            animate(`${duration}s ease-in-out`)
        ]),
        transition(':leave', [animate(`${duration}s ease-in-out`, style({ opacity: 0 }))])
    ])

declare const $: any;

@Component({
    selector: 'orders',
    templateUrl: './OrdersComponent.html',
    styleUrls: ['./OrdersComponent.css'],
    providers: [MessageService],
    animations: [
        fadeInOut('fadeInOut-3', 3)
    ]
})

export class OrdersComponent implements AfterViewInit {

    @ViewChild('tableOrders') tableOrders: Table;
    @ViewChild('validActs') validActs: ElementRef;
    @ViewChild('consultActs') consultActs: ElementRef;
    @ViewChildren('myVar') createdItems;

    @Input() vm: IOrdersViewModelMaster;
    noRecordMsgFlag: boolean;
    creationDate: string;
    signatureDate: string;
    filterDateCreation: boolean = false;
    filterDateSigning: boolean = false;
    filterChocie: string;
    sort: string;
    inputReasearchBar: string;
    displayDivForReasearchBar: boolean = false;
    envId :number = 29;
    readyOnlyForUser :boolean = false;
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
    disableBtnEmail:boolean = false;


    choices = [
        { label: 'Filtre par date', value: null },
        { label: 'Date de dernière modification', value: 1 },
        { label: 'Date de signature', value: 2 },
    ];
    choicesAvocat = [
        { label: 'Filtre par date', value: null },
        { label: 'Date de dernière modification', value: 1 },
        { label: 'Date de récéption', value: 2 },
    ];
    isMobileSize = false;
    constructor(private ngZone: NgZone,private orderService: ActeService)  {
    }
    test(event: any) {
        if (event == 1) {
            this.vm.filterDateCreation = true;
            this.vm.filterDateSigning = false;
        } else if (event == 2) {
            this.vm.filterDateCreation = false;
            this.vm.filterDateSigning = true;
        } else {
            this.vm.filterDateCreation = false;
            this.vm.filterDateSigning = false;
        }
    }

    get orderTableMesActes(): boolean {
        if (this.sort != '1' && this.sort != '2' && this.sort != '3' && this.sort != '4' && this.sort != '5' && this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_USER') {
            this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', "HH:mm:ss");
        }
        return true;
    }

    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }
    set userConnected(user :User){
         OrdersViewModelMaster.userConnected=user;
    }
    user() {
        let user = this.userConnected;
    }
    annuler(){
        let userQuery = this.orderService.getUserconnectedAsync();
        Promise.all([userQuery]).then(results => {
            this.userConnected=results[0];
        });
    }

    get activateFilterClass(): boolean {
        if (this.vm.activateFilterClass == true) {
            this.tableOrders.filter('trueorFalse', 'actValidated', 'equalsActValidated');
            this.consultActs.nativeElement.classList.remove('inactive-filter');
            this.consultActs.nativeElement.classList.add('active-filter');
            this.validActs.nativeElement.classList.add('inactive-filter');
            if (this.sort == '2') {
                this.vm.customSortDate(this.tableOrders.value, false, 'receptionDate', "HH:mm:ss");
            }
        }
        else {
            this.tableOrders.filter('null', 'actValidated', 'equalsActValidated');
            if (this.sort == '2' && this.tableOrders.value ) {
                this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, false, 'receptionDate', "HH:mm:ss");
            }
        }
        return this.vm.activateFilterClass;
    }

    filterActs(event: any) {
        if (event.target.id == 'valid') {
            this.vm.activateFilterClass = false;
            this.validActs.nativeElement.classList.add('active-filter');
            this.validActs.nativeElement.classList.remove('inactive-filter');
            this.consultActs.nativeElement.classList.add('inactive-filter');
        }

        else if (event.target.id == 'consult') {
            this.vm.activateFilterClass = true;
            this.validActs.nativeElement.classList.add('inactive-filter');
        }
    }

    get activFilterStatus(): boolean {
        return ((!this.userConnected || !this.userConnected.roles || !(this.userConnected.roles[0] === 'ROLE_COUNSEL')) || (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_COUNSEL' && this.vm.activateFilterClass == true))
    }
    ngAfterViewInit(): void {
        this.checkCnbMobile();
        this.checkEmailCnb();
        this.readyOnlyForUser = this.oneMonthResiliation();
        if (localStorage.getItem('array')) {
            let ids = JSON.parse(localStorage.getItem('array'));
            let newData = [];
            for (let i = 0; i < this.tableOrders.value.length; i++) {
                if (ids.includes(this.tableOrders.value[i].id.toString())) {
                    newData.push(this.tableOrders.value[i]);
                }
            }
            this.tableOrders.value = [...newData];
        }
        if (localStorage.getItem('status')) {
            if (localStorage.getItem('status') === 'Signature refusee') {
                let newData = [];
                for (let i = 0; i < this.tableOrders.value.length; i++) {
                    if (this.tableOrders.value[i].status === 'Signature refusee' || this.tableOrders.value[i].status === 'Validation refusee') {
                        newData.push(this.tableOrders.value[i]);
                    }
                }
                this.tableOrders.value = [...newData];
            } else {
                this.vm.choosenStatuts = localStorage.getItem('status');
                this.tableOrders.filter(this.vm.choosenStatuts, 'status', 'equals');
            }
        }

        if(localStorage.getItem('act')){
            let id=null;
            var actId = localStorage.getItem('act').substring(
                localStorage.getItem('act').indexOf("-") + 1,
                localStorage.getItem('act').lastIndexOf("-")
            );

            var actIdNumber: number = +actId;

            this.tableOrders.value.forEach((value,index)=>{
                if(value.id===actIdNumber){
                    id=index;
                }
            })
            this.vm.assignActeModel(this.tableOrders.value[id]);
        }
        if (localStorage.getItem('status')) {
            localStorage.removeItem('status');
        }
        if (localStorage.getItem('act')) {
            localStorage.removeItem('act');
        }
        if (localStorage.getItem('array')) {
            localStorage.removeItem('array');
        }
        if (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_COUNSEL') {
            this.sort = '2';
        } else if (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_USER') {
            this.sort = '0';
        }
        let env = this.vm.services.orderService.getEnvVariables().then(res => {
            if (res == 'dev') {
                this.envId = 31;
            } else {
                 this.envId = 30;
             }
         });
        OrdersViewModelMaster.reloadComplete = false;
            OrdersViewModelMaster.idActe = null;
        this.tableOrders.filterConstraints['equalsActValidated'] = (value, filter): boolean => {
            if (filter == 'trueorFalse') { return (value == 'true' || value == 'false') }
            else {
                return (value == filter)
            }
        };

        this.tableOrders.filterConstraints['requestDateFilter'] = (value, filter): boolean => {
            if (!value || !filter) {
                return false;
            }
            let v = moment(value, 'DD/MM/YYYY');
            let fmax = moment(filter[1], 'DD/MM/YYYY').format("DD/MM/YYYY");

            if (fmax != "Invalid date") {
                return v.isSameOrAfter(moment(filter[0], 'DD/MM/YYYY')) && v.isSameOrBefore(moment(filter[1], 'DD/MM/YYYY'));
            } else {
                return v.isSameOrAfter(moment(filter[0], 'DD/MM/YYYY'));
            }
        };
        this.tableOrders.filterConstraints['signingDateFilter'] = (value, filter): boolean => {
            if (!value || !filter) {
                return false;
            }
            let v = moment(value, 'DD/MM/YYYY');
            let fmax = moment(filter[1], 'DD/MM/YYYY').format("DD/MM/YYYY");

            if (fmax != "Invalid date") {
                return v.isSameOrAfter(moment(filter[0], 'DD/MM/YYYY')) && v.isSameOrBefore(moment(filter[1], 'DD/MM/YYYY'));
            } else {
                return v.isSameOrAfter(moment(filter[0], 'DD/MM/YYYY'));
            }
        }
        let resizeObservable = Observable.fromEvent(window, 'resize')
            .pipe(
                debounceTime(200),
                map(() => window.innerWidth), //Don't use mapTo!
                distinctUntilChanged(),
                startWith(window.innerWidth),
                tap(width => {
                    if(width <= 768 && !this.isMobileSize) {
                        this.isMobileSize = true;
                    }else {
                        this.isMobileSize = false;
                    }
                }),
            );

        resizeObservable.subscribe();
        this.checkEmailCnb();
        this.checkCnbMobile();
        window['angularComponentReference'] = { component: this, zone: this.ngZone, loadAngularFunctionforActs: () => this.showSettings(), };

    }

    showSettings(){
        this.settingsBtn = true;
        this.displayCX = true;

    }


    onChangeFiltre(filtre: any) {
        if (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] == 'ROLE_USER') {
            if (filtre == "1") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, true, 'requestDate', "HH:mm:ss");
                }
                else {
                    this.vm.listOrder = this.vm.customSortDate(this.vm.listOrder, true, 'requestDate', "HH:mm:ss");

                }
            }
            else if (filtre == "0") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', "HH:mm:ss");
                }
            }
            else if (filtre == "3") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, true, 'signingDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, true, 'signingDate', 'HH:mm:ss');
                }
            }
            else if (filtre == "2") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, false, 'signingDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, false, 'signingDate', 'HH:mm:ss');
                }
            }

            else if (filtre == "5") {
                this.tableOrders.lazy = false;
                this.tableOrders._sortOrder = -1;
                this.tableOrders.sortField = 'name';
            }
            else if (filtre == "4") {
                this.tableOrders.lazy = false;
                this.tableOrders._sortOrder = 1;
                this.tableOrders.sortField = 'name';
            }
        }
        else {
            if (filtre == "1") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, true, 'requestDate', "HH:mm:ss");
                }
                else {
                    this.vm.listOrder = this.vm.customSortDate(this.vm.listOrder, true, 'requestDate', "HH:mm:ss");
                }
            }
            else if (filtre == "0") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', "HH:mm:ss");
                }
            }

            else if (filtre == "3") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, true, 'receptionDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, true, 'receptionDate', 'HH:mm:ss');
                }
            }
            else if (filtre == "2") {
                if (this.tableOrders.value) {
                    this.tableOrders.value = this.vm.customSortDate(this.tableOrders.value, false, 'receptionDate', "HH:mm:ss");
                }
                else {
                    this.vm.customSortDate(this.tableOrders.value, false, 'receptionDate', 'HH:mm:ss');
                }
            }

            else if (filtre == "5") {
                this.tableOrders.lazy = false;
                this.tableOrders._sortOrder = -1;
                this.tableOrders.sortField = 'name';
            }
            else if (filtre == "4") {
                this.tableOrders.lazy = false;
                this.tableOrders._sortOrder = 1;
                this.tableOrders.sortField = 'name';
            }
        }

    }

    isEmpty(event: any) {
        this.noRecordMsgFlag = event.filteredValue.length == 0;
    }


    styleObject(col: any): Object {
        if (col.header == 'statut') {
            return { 'width': col.width, 'text-align': 'center' }
        }
        return { 'width': col.width }
    }

    create() {
        this.vm.modeCreateOrModify = false;
        OrdersViewModelMaster.currentActe = new Order();
        OrdersViewModelMaster.listDocument = [];
        OrdersViewModelMaster.listAvocat = [];
        OrdersViewModelMaster.listSignataire = [];
        OrdersViewModelMaster.idActe = null;
        this.activeIndex = 0;
        this.displayCreateOrder = true;
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
                if (this.userConnected.phoneNumber === null) {
                    this.checkPhone();
                }
            }
        } else {
            let inp = this.userConnected.emailApp;
            let patternEmail = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,10}$/;
            if (this.userConnected.emailApp !== null && this.userConnected.emailApp !=="") {
                if (patternEmail.test(inp) !== false) {
                    this.errorMsg = false;
                    this.hideMsg = false;
                    this.disableBtn = false;
                    if (this.userConnected.phoneNumber === null) {
                        this.checkPhone();
                    }
                } else {
                    this.errorMsg = true;
                    this.hideMsg = false;
                    this.disableBtn = true;
                }
            } else {
                this.errorMsg = false;
                this.hideMsg = false;
                this.disableBtn = false;
                if (this.userConnected.phoneNumber === null) {
                    this.checkPhone();
                }
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

    get activeIndex(): number {
        return OrdersViewModelMaster.activeIndex;
    }
    set activeIndex(activeIndex: number) {
        OrdersViewModelMaster.activeIndex = activeIndex;
    }

    set reloadComplete(reloadComplete: boolean) {
        OrdersViewModelMaster.reloadComplete = reloadComplete;
    }

    get reloadComplete(): boolean {
        return OrdersViewModelMaster.reloadComplete;
    }

    set displayCreateOrder(displayCreateOrder: boolean) {
        OrdersViewModelMaster.displayCreateOrder = displayCreateOrder;
    }

    get displayCreateOrder(): boolean {
        return OrdersViewModelMaster.displayCreateOrder;
    }

    set displayConsultOrder(displayConsultOrder: boolean) {
        OrdersViewModelMaster.displayConsultOrder = displayConsultOrder;
    }

    get displayConsultOrder(): boolean {
        return OrdersViewModelMaster.displayConsultOrder;
    }

    set displaySendTovalidation(displaySendTovalidation: boolean) {
        OrdersViewModelMaster.displaySendTovalidation = displaySendTovalidation;
    }
    get displaySendTovalidation(): boolean {
        return OrdersViewModelMaster.displaySendTovalidation;
    }
    get displayConsultSignedActComponent(): boolean {
        return OrdersViewModelMaster.displayConsultSignedActComponent
    }
    set displayConsultSignedActComponent(displayConsultSignedActComponent: boolean) {
        OrdersViewModelMaster.displayConsultSignedActComponent = displayConsultSignedActComponent;
    }


    initializeFilterAndSorting(dropdown: Dropdown) {
        this.vm.filterDateCreation = null;
        this.vm.filterDateSigning = null;
        this.vm.filterChocie = null;
        this.vm.creationDate = null;
        this.vm.signatureDate = null;
        this.vm.choosenStatuts = null;
        if (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_COUNSEL') {
            this.sort = '2';
        }
        else if (this.userConnected && this.userConnected.roles && this.userConnected.roles[0] === 'ROLE_USER') {
            this.sort = '0';
        }
        if (this.userConnected.roles[0] === 'ROLE_COUNSEL') {
            this.vm.customSortDate(this.tableOrders.value, false, 'receptionDate', 'HH:mm:ss');
        }
        else {
            this.vm.customSortDate(this.tableOrders.value, false, 'requestDate', 'HH:mm:ss');
        }
        this.tableOrders.reset();
        if (dropdown) {
            dropdown.resetFilter();
        }
        this.noRecordMsgFlag = false;
    }

    get displayMyAct(): boolean {
        return this.displayCreateOrder || this.displayConsultOrder || this.displaySendTovalidation || this.vm.displayValidate || this.vm.displaySentToSignature || this.vm.displayActeRefused || this.vm.displayAbandonedAct ||  this.vm.displayconsultActForAvocatComponent || this.vm.displayConsultSignedActComponent || this.vm.displayAllResult
    }
    oneMonthResiliation(){
       let dateDiff=moment().diff(moment(this.userConnected.resiliation,'DD/MM/YYYY'),'months',true);
       return dateDiff <=1 || this.userConnected.resiliation ===null;
    }
    showAbondonne(status,choosen){
        return !(status == 'Abandonne' && (choosen == undefined || false));
    }

    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }

}