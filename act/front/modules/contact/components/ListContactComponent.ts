import {animate, Component, NgZone, OnInit, style, transition, trigger, ViewChild} from '@angular/core';
import {ActeService} from '../../../shared/services/acte.services';
import {Table} from 'primeng/table';
import {MessageService} from 'primeng/api';
import {Order} from '../../../shared/entities/order';
import {DocumentService} from '../../../shared/services/document.services';
import {SignataireService} from '../../../shared/services/signataire.services';
import {AvocatService} from '../../../shared/services/avocat.services';
import * as moment from "moment";
import {Column} from '../../../shared/models/Column';
import {Avocat} from '../../../shared/entities/avocat';
import {BaseViewModel} from './Static/BaseViewModel';
import {Signataire} from '../../../shared/entities/signataire';
import {CommonModel} from '../../../shared/entities/commonModel';
import {SearchBarUser} from '../../../shared/entities/searchBarUser';
import {NgxSpinnerService} from "ngx-spinner";
import {log} from "util";


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
    selector: 'listeContact',
    templateUrl: './ListContactComponent.html',
    styleUrls: ['./ListContactComponent.css'],
    providers: [MessageService],
    animations: [
        fadeInOut('fadeInOut-3', 3)
    ]
})

export class ListContactComponent implements OnInit {

    @ViewChild('contactTable') tableOrders: Table;
    columns: Array<Column>;
    listeContact: any[] = [];
    allAct: Order[] = [];

    choicesNature = [
        { label: 'Nature de contact', value: null },
        { label: 'Avocat', value: 'ROLE_COUNSEL' },
        { label: 'Signataire', value: 'ROLE_SIGNATORY' },
    ];
    choicesType = [
        { label: 'Type de contact', value: null },
        { label: 'Morale', value: 'null' },
        { label: 'Physique', value: 'NotNull' },
    ];
    sort: string = "0";
    filterChocieType: string;
    filterChocieNature: string;
    noRecordMsgFlag: boolean = false;
    displayPopuForDelete: boolean = false;
    currentConatct: any;
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
    userConnected: any;
    disableBtnEmail : boolean = false;
    documentsActs : any = [];
    acts: any = [];
    actUsers : any = [];




    constructor(private messageService: MessageService, private documentService: DocumentService, private avocatService: AvocatService, private signataireService: SignataireService, private orderService: ActeService , private spinner: NgxSpinnerService,private ngZone : NgZone
    ) {
    }


    ngOnInit() {
        this.getRessources();
        window['angularComponentReference'] = {
            component: this,
            zone: this.ngZone,
            loadAngularFunctionforContacts: () => this.showSettings(),
        };
        this.sort = "0";
        this.filterChocieType = null;
        this.filterChocieNature = null;
        this.columns = [
            new Column(false, true, false, 'Nom et prénom', 'lastName', '250px', 3),
            new Column(false, true, false, 'Tél (annuaire)', 'phoneNumberApp', '170px', 3),
            new Column(false, true, false, 'Adresse de messagerie (annuaire)', 'emailApp', '170px', 3),
            new Column(false, true, false, 'N° mobile', 'phoneNumber', '170px', 3),
            new Column(false, true, false, 'Adresse de messagerie', 'email', '170px', 3),
            new Column(false, true, false, 'Action', '', '90px', 3),
        ];
        let env = this.orderService.getEnvVariables().then(res => {
            if (res == 'dev'){
                this.envId = 31;
            }else{
                this.envId = 30;
            }
        });

    }

    showSettings(){
        this.settingsBtn = true;
        this.displayCX = true;

    }
    checkinput() {
        let inp = this.userConnected.emailApp;
        console.log(inp)
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
        console.log(this.errorMsg);
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
    annuler(){
        let userQuery = this.orderService.getUserconnectedAsync();
        Promise.all([userQuery]).then(results => {
            this.userConnected=results[0];
        });
    }
    filterNatureContact() {
            this.tableOrders.filterConstraints['equalsSignataire'] = (value, filter): boolean => {
                if (filter == 'trueorFalse') { return (value == 'ROLE_ENTERPRISE' || value == 'ROLE_SIGNATORY') }
                else {
                    return (value == filter)
                }
            };
        if (this.filterChocieNature == "ROLE_SIGNATORY") {
            this.tableOrders.filter('trueorFalse', 'roles', 'equalsSignataire');
        }
        else {
            this.tableOrders.filter('ROLE_COUNSEL', 'roles', 'equalsSignataire');
        }
    }

    filterTypeContact() {
        if (this.filterChocieNature) {
            this.tableOrders.filter(this.filterChocieNature, 'roles', 'equals');
        }
        if (this.filterChocieType == 'null') {
            this.tableOrders.filter('ROLE_ENTERPRISE', 'roles', 'equals');
        }
        else {
            this.tableOrders.filter('ROLE_ENTERPRISE', 'roles', 'notEquals');
            this.tableOrders.filter('ROLE_SIGNATORY', 'roles', 'equals');
        }
    }


    getLetter(contact: Signataire): string {
        if (this.tableOrders && this.sort == '0' && !this.filterChocieNature && !this.filterChocieType) {
            this.tableOrders.lazy = false
            this.tableOrders._sortOrder = 1;
            this.tableOrders.sortField = 'name';
        }
        if (this.allContact.indexOf(contact) == 0 || (this.allContact.indexOf(contact) != 0 && contact.name.charAt(0).toLocaleUpperCase() != this.allContact[this.allContact.indexOf(contact) - 1].name.charAt(0).toLocaleUpperCase())) {
            return contact.name.charAt(0).toLocaleUpperCase();
        }
        else {
            return '';
        }
    }

    deleteContact(contact) {
        const contactClicked = BaseViewModel.allSignataire.find(signataire => signataire.id === contact.id);
        const actFound = this.actUsers.filter(act => act.user == contactClicked.id && act.status !== 'Abandonne' ).length;
        if (contact.cnbId) {
            this.messageService.add({ severity: 'error', summary: 'Contact', detail: 'Le contact est issu de l\'annuaire, il ne peut pas être supprimé', life: 4000 });
        }
        else if (BaseViewModel.allAvocat.some(avocat => avocat.actId && avocat.id == contact.id) || (BaseViewModel.allSignataire.some(signataire => signataire.actId && signataire.id == contact.id) && actFound > 0 )) {
            console.log('it works')
            this.messageService.add({ severity: 'error', summary: 'Contact', detail: 'Le contact est rattaché à un acte, il ne peut pas être supprimé', life: 4000 });
        }
        else {
            this.displayPopuForDelete = true;
            this.currentConatct = contact;
        }
    }

    deletContactFromDataBase() {
        let deleteQuery = this.avocatService.deleteContact(this.currentConatct);
        Promise.all([deleteQuery]).then(results => {
            this.allContact = this.allContact.filter(cont => cont.name != this.currentConatct.name);
            this.displayPopuForDelete = false;
            this.messageService.add({ severity: 'success', summary: 'Contact', detail: 'Le contact est supprimé avec succès', life: 4000 });
        });
    }
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
    consultContactMethod(contact: any) {
        BaseViewModel.contactToModify = contact;
        this.displayContactAdd = true; this.modeModify = true; this.consultContact = true;
    }
    setContactToModify(contact: any): void {
        if (contact.enterpriseName == 'null') {
            contact.enterpriseName = null;
        }
        if (contact.cnbId) {
            this.messageService.add({ severity: 'error', summary: 'Contact', detail: 'Le contact est issu de l\'annuaire, il ne peut pas être modifié' });
        }
       else if (BaseViewModel.allAvocat.some(avocat => avocat.actId && avocat.id == contact.id) || BaseViewModel.allSignataire.some(signataire => signataire.actId && signataire.id == contact.id)) {
            this.messageService.add({ severity: 'info', summary: 'Contact', detail: 'Votre contact a des actes en cours', life: 4000 });
            BaseViewModel.contactToModify = contact;
            this.displayContactAdd = true; this.modeModify = true; this.consultContact = false;
        }
        else {
            BaseViewModel.contactToModify = contact;
            this.displayContactAdd = true; this.modeModify = true; this.consultContact = false;
        }
    }

    setContactTConsylt(contact: any): void {
        if (contact["type"] && (contact["type"] == 0 || contact["type"] == 1)) {
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
            let contactType;
            if (contact["type"] && contact["type"] == 2) {
                contactType = contact["type"];
            }
            contact = this.allContact.filter(item => item.id == contact.id)[0];
            contact["type"] = contactType;
            if (contact.enterpriseName == 'null') {
                contact.enterpriseName = null;
            }
            if (contactType == "2") {
                this.consultContact = true;
            }
            this.displayContactAdd = true; this.modeModify = true;
        }
        BaseViewModel.contactToModify = contact;
    }

    onChangeFiltre(filtre: any) {
        console.log(filtre);
        debugger;
        if (filtre == "1") {
            this.tableOrders.lazy = false;
            this.tableOrders._sortOrder = -1;
            this.tableOrders.sortField = 'name';
        }
        else if (filtre == "0") {
            this.tableOrders.lazy = false;
            this.tableOrders._sortOrder = 1;
            this.tableOrders.sortField = 'name';

        }
    }

    isEmpty(event: any) {
        this.noRecordMsgFlag = event.filteredValue.length == 0;
    }
    styleObject(col: any): Object {
        return { 'width': col.width }
    }

    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        return arr
    }

    initializeFilterAndSorting() {
        this.sort = '0';
        this.tableOrders.reset();
        this.allContact.sort(function (a, b) {
            if (a.name < b.name) { return -1; }
            if (a.name > b.name) { return 1; }
            return 0;
        })
        this.filterChocieType = null;
        this.filterChocieNature = null;
        this.noRecordMsgFlag = false;

    }


    get displayContactAdd(): boolean {
        return BaseViewModel.displayContactAdd;
    }
    set displayContactAdd(displayContactAdd: boolean) {
        BaseViewModel.displayContactAdd = displayContactAdd;
    }

    getRessources() {
        let allContactQuery = this.avocatService.getAllContactAsync();
        let searchBar = this.orderService.getAllActesAsyncSearchBar();
        let userQuery = this.orderService.getUserconnectedAsync();

        Promise.all([allContactQuery, searchBar, userQuery, this.spinner.show()]).then(results1 => {
            this.spinner.hide();
            for (let contact of results1[0]) {
                contact.roles = contact.roles[0];
                if (!contact.roles) {
                    contact.roles = 'ROLE_COUNSEL'
                }
                if (contact.roles == 'ROLE_ENTERPRISE') {
                    contact["role"] = 'enterprise';
                }
                else if (contact.roles == 'ROLE_COUNSEL') {
                    contact["role"] = 'counsel';
                }
                else if (contact.roles == 'ROLE_SIGNATORY') {
                    contact["role"] = 'signatory';
                }
                if (!contact.enterpriseName) {
                    contact.enterpriseName = 'null';
                }
                this.allContact.push(contact);
                let commonModel = new CommonModel();
                commonModel.contactName = contact.name;
                commonModel.id = contact.id;
                commonModel.contactLastName = contact.lastName;
                commonModel.contactMil = contact.email;
                commonModel.contactPhoneNumber = contact.phoneNumber;
                commonModel.type = "2";
                if (contact.roles == "ROLE_COUNSEL") {
                    commonModel.nature = "avocat"
                }
                else {
                    commonModel.nature = "signataire"
                }
                this.allData.push(commonModel);
            }
            if(localStorage.getItem('act')){
                let contact = this.allContact.find(item => item.id == parseInt(localStorage.getItem('act')));
                contact["type"]='2';
                this.setContactTConsylt(contact);
            }
            if(localStorage.getItem('status')){
                localStorage.removeItem('status');
            }
            if(localStorage.getItem('act')){
                localStorage.removeItem('act');
            }
            this.allContact.sort(function (a, b) {
                if (a.name < b.name) { return -1; }
                if (a.name > b.name) { return 1; }
                return 0;
            });
            BaseViewModel.userConnected = results1[2];
            this.readyOnlyForUser=this.oneMonthResiliation();
            if (results1[1].filter(elm => elm.idUser == "" + BaseViewModel.userConnected.id).length > 0) {
                this.consultContact = true;
                this.setContactTConsylt(this.allContact.filter(contact => contact.id == results1[1].filter(elm => elm.idUser == "" + results1[2].id)[0].idEntity)[0]);
                let deletQuery = this.orderService.deleteSearch(results1[1][0]);
                Promise.all([deletQuery]).then(results => {
                });
            }

            let sigataireQuery = this.signataireService.getAllSignataireAsync();
            let avocatQuery = this.avocatService.getAllAvocatsAsync();
            let orderQuery = this.orderService.getAllActesAsync();
            let searchBar = this.orderService.getAllActesAsyncSearchBar();
            let userQuery = this.orderService.getUserconnectedAsync();

            Promise.all([sigataireQuery, avocatQuery, orderQuery, searchBar, userQuery]).then(results => {
                for (let act of results[2]) {
                    let commonModel = new CommonModel();
                    commonModel.nameActe = act.name;
                    commonModel.status = act.status;
                    commonModel.id = "" + act.id;
                    commonModel.folderNumber = act.folderNumber;
                    commonModel.folderName = act.folderName;
                    commonModel.folderName = act.folderName;
                    commonModel.type = "0";
                    this.allData.push(commonModel);
                    this.documentsActs.push(act.id)
                    this.acts.push({id: act.id , status : act.status})

                }
                let actsDocuments = this.documentService.getAllActsDocument(this.documentsActs);
                Promise.all([actsDocuments]).then(documentsResult => {
                    for (let document of documentsResult[0]) {
                        if (document.actId && results[2].filter(act => act.id == document.actId).length == 1) {
                            let commonModel = new CommonModel();
                            commonModel.documentName = document.name;
                            commonModel.id = "" + document.actId;
                            commonModel.type = '1';
                            commonModel.status = results[2].filter(act => act.id == document.actId)[0].status;
                            this.allData.push(commonModel);
                        }
                    }
                })
                BaseViewModel.allSignataire = results[0];
                BaseViewModel.allAvocat = results[1];
                this.userConnected = results[5];
                console.log(BaseViewModel.userConnected)
                let actUser = this.orderService.getAllActesUsersAsync(BaseViewModel.userConnected)
                Promise.all([actUser]).then(res => {
                    this.actUsers = res[0];
                })
                this.checkEmailCnb();
                this.checkCnbMobile();



            });
        });

        
    }

    toggleSidebar() {
        let wrapperElement = document.querySelector('.wrapper');
        if (wrapperElement !== undefined) {
            wrapperElement.classList.toggle('menu-open');
        }
    }

    get allContact() {
        return BaseViewModel.allContact;
    }
    set allContact(allContact: Signataire[]) {
        BaseViewModel.allContact = allContact;
    }

    set modeModify(modeModify: boolean) {
        BaseViewModel.modeModify = modeModify;
    }
    get consultContact() {
        return BaseViewModel.consultContact;
    }
    set consultContact(consultContact: boolean) {
        BaseViewModel.consultContact = consultContact;
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
    oneMonthResiliation(){
        let dateDiff=moment().diff(moment(BaseViewModel.userConnected.resiliation,'DD/MM/YYYY'),'months',true);
        return dateDiff <=1 || BaseViewModel.userConnected.resiliation ===null;
    }
    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }
}