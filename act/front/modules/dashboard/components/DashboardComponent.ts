import {animate, Component, ElementRef, Input, OnInit, style, transition, trigger, ViewChild,NgZone} from '@angular/core';
import {Table} from 'primeng/table';
import {MessageService} from 'primeng/api';
import {SignataireService} from '../../../shared/services/signataire.services';
import {AvocatService} from '../../../shared/services/avocat.services';
import * as moment from "moment";
import {Avocat} from '../../../shared/entities/avocat';
import {Cookies} from "../../../shared/entities/cookies";
import {ActeService} from "../../../shared/services/acte.services";
import {CookieService} from 'ngx-cookie-service';
import {IOrdersViewModelMaster} from "../../order/view-models/interfaces/IOrdersViewModelMaster";
import {BaseViewModel} from "./Static/BaseViewModel";
import {Signataire} from "../../../shared/entities/signataire";
import {CommonModel} from "../../../shared/entities/commonModel";
import {DocumentService} from "../../../shared/services/document.services";
import {NgxSpinnerService} from "ngx-spinner";

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
    selector: 'dashboard',
    templateUrl: './DashboardComponent.html',
    styleUrls: ['./DashboardComponent.css'],
    providers: [MessageService],
    animations: [
        fadeInOut('fadeInOut-3', 3)
    ]
})

export class DashboardComponent implements OnInit {
    @ViewChild('tableOrders') tableOrders: Table;
    @ViewChild('emailApp') emailApp: ElementRef;

    @Input() vm: IOrdersViewModelMaster;
    acctpePiwic: boolean = false;
    cookiesInofmration: Cookies;
    displayCookiesPrametres: boolean = false;
    displayCookiesModal: boolean = false;
    listeContact: any[] = [];
    choicesNature = [
        {label: 'Nature de contact', value: null},
        {label: 'Avocat', value: 'ROLE_COUNSEL'},
        {label: 'Signataire', value: 'ROLE_SIGNATORY'},
    ];
    choicesType = [
        {label: 'Type de contact', value: null},
        {label: 'Morale', value: 'NotNull'},
        {label: 'Physique', value: 'null'},
    ];
    sort: string = "0";
    filterChocieType: string;
    filterChocieNature: string;
    noRecordMsgFlag: boolean = false;
    displayCookies = false;
    displayResiliateModal = false;
    displayConfirmationModal = false;
    displayResiliateQuestion = false;
    userConnected: any;
    reasonInput: string = '';
    causes: { message: string, checked: boolean }[];
    counts: any;
    acts: any;
    signedActs: any = [];
    actsRefused: any = [];
    actsValidated: any = [];
    lastActs: any = [];
    env: any = {};
    day: string;
    month: string;
    displayCGU: boolean = false;
    displayRefuseCGU: boolean = false;
    displayPC: boolean = false;
    displayRefusePC: boolean = false;
    displayCX: boolean = true;
    isCnbEmail: boolean = false;
    hideMsg: boolean = false;
    errorMsg: boolean = false;
    disableBtn: boolean = false;
    disableBtnEmail : boolean = false;
    disableBtnPhone : boolean = false;
    hidePhoneMsg: boolean = false;
    errorPhoneMsg: boolean = false;
    txtValue: string = null;
    isCnbPhone: boolean = false;
    settingsBtn:boolean = false;
    documentsActs : any = [];


    constructor(private messageService: MessageService, private avocatService: AvocatService, private signataireService: SignataireService,
                private orderService: ActeService, private cookiesService: CookieService, private documentService: DocumentService, private spinner: NgxSpinnerService,private ngZone:NgZone) {

    }

    ngOnInit(): void {
        this.month = moment().locale("fr").format("MMMM");
        this.day = moment().locale("fr").format("D");
        if (localStorage.getItem('status')) {
            localStorage.removeItem('status');
        }
        if (localStorage.getItem('act')) {
            localStorage.removeItem('act');
        }
        let getCookies = this.orderService.getCookies();
        Promise.all([getCookies]).then(results => {
            if (this.cookiesService.get('assp-cookies')) {
                this.displayCookiesModal = results[0];
            } else {
                this.displayCookiesModal = true;
            }
        });
        this.getRessources();
        window['angularComponentReference'] = { component: this, zone: this.ngZone, loadAngularFunction: () => this.showSettings(), };

        this.acctpePiwic = !!(this.cookiesService.get('assp-cookies'));

        this.causes = [{
            message: "Je ne suis plus avocat",
            checked: false
        }, {
            message: "Je ne veux plus utiliser les services ASSP",
            checked: false
        }, {
            message: "Autre",
            checked: false
        }];
    }
    getRessources() {
        let allContactQuery = this.avocatService.getAllContactAsync();
        let userQuery = this.orderService.getUserconnectedAsync();
        let count = this.orderService.getCountAct();
        let orderQuery = this.orderService.getAllActesAsync();
        let envQuery = this.orderService.getEnvVariables();
        Promise.all([allContactQuery, userQuery, count, orderQuery, envQuery, this.spinner.show()]).then(results => {

            for (let contact of results[0]) {
                contact.roles = contact.roles[0];
                this.listeContact.push(contact);
                let commonModel = new CommonModel();
                commonModel.contactName = contact.name;
                commonModel.id = contact.id;
                commonModel.contactLastName = contact.lastName;
                commonModel.contactMil = contact.email;
                commonModel.contactPhoneNumber = contact.phoneNumber;
                commonModel.type = "2";
                if (contact.roles == "ROLE_COUNSEL") {
                    commonModel.nature = "avocat"
                } else {
                    commonModel.nature = "signataire"
                }
                this.allData.push(commonModel);
            }
            this.listeContact.sort(function (a, b) {
                if (a.name < b.name) {
                    return -1;
                }
                if (a.name > b.name) {
                    return 1;
                }
                return 0;
            })
            this.userConnected = results[1];
            this.checkEmailCnb();
            this.checkCnbMobile();
            this.counts = results[2][1];
            this.lastActs = results[2][0];
            this.acts = results[3];
            this.acts.forEach((value , index) => {
                this.documentsActs.push(value.id)
            })
            this.acts.forEach((value, index) => {
                if (value.status === 'Signee') {
                    this.signedActs.push(value);
                } else if (value.status === 'Signature refusee' || value.status === 'Validation refusee') {
                    this.actsRefused.push(value);
                } else if (value.status === 'En cours de signature') {
                    this.actsValidated.push(value);
                }
                let commonModel = new CommonModel();
                commonModel.nameActe = value.name;
                commonModel.status = value.status;
                commonModel.id = "" + value.id;
                commonModel.folderNumber = value.folderNumber;
                commonModel.folderName = value.folderName;
                commonModel.folderName = value.folderName;
                commonModel.type = "0";
                this.allData.push(commonModel);
            });
            this.env = results[4];
            if (this.displayCookiesModal == false) {
                this.consultShowModals('cgu');
                if(this.displayCGU==false){
                    this.consultShowModals('pc');
                }

            }
            let actsDocuments = this.documentService.getAllActsDocument(this.documentsActs);
            Promise.all([actsDocuments]).then(documentsResult => {
                for (let document of documentsResult[0]) {
                    if (document.actId && results[3].filter(act => act.id == document.actId).length == 1) {
                        let commonModel = new CommonModel();
                        commonModel.documentName = document.name;
                        commonModel.id = "" + document.actId;
                        commonModel.type = '1';
                        commonModel.status = results[3].filter(act => act.id == document.actId)[0].status;
                        this.allData.push(commonModel);
                    }
                }
            })
            this.spinner.hide();
        });


    }


    showSettings(){
        this.settingsBtn = true;
        this.displayCX = true;
    }


    checkBrowser(): string {
        switch (true) {
            case window.navigator.userAgent.toLowerCase().indexOf("edge") > -1:
                return "edge";
            case window.navigator.userAgent.toLowerCase().indexOf("edg") > -1:
                return "chromium based edge (dev or canary)";
            case window.navigator.userAgent.toLowerCase().indexOf("opr") > -1:
                return "opera";
            case window.navigator.userAgent.toLowerCase().indexOf("chrome") > -1:
                return "chrome";
            case window.navigator.userAgent.toLowerCase().indexOf("trident") > -1:
                return "ie";
            case window.navigator.userAgent.toLowerCase().indexOf("firefox") > -1:
                return "firefox";
            case window.navigator.userAgent.toLowerCase().indexOf("safari") > -1:
                return "safari";
            default:
                return "other";
        }
    }

    saveCookies() {
        if (this.acctpePiwic == false) {
            this.cookiesService.delete('assp-cookies');
            this.displayCookies = false;
            this.displayCookiesModal = false;
            this.displayCookiesPrametres = false;
        } else {

            this.cookiesInofmration = new Cookies();
            this.cookiesInofmration.date = new Date().toUTCString();
            this.cookiesInofmration.guid = this.createGuid();
            this.cookiesInofmration.navigateur = this.checkBrowser();
            this.cookiesInofmration.piwikIgnore = this.acctpePiwic;
            let saveCookies = this.orderService.postCookies(this.cookiesInofmration);
            Promise.all([saveCookies]).then(results => {
                this.cookiesService.set('assp-cookies', JSON.stringify(this.cookiesInofmration), 365, null, null, null);
                this.displayCookies = false;
                this.displayCookiesModal = false;
                this.displayCookiesPrametres = false;
            });
        }
    }

    consultShowModals(param: any) {
        if (param === "cgu") {
            this.displayCGU = this.env.showModalCgu;
        } else if (param === "pc") {
            this.displayPC = this.env.showModalPc;
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
    annuler(){
        let userQuery = this.orderService.getUserconnectedAsync();
        Promise.all([userQuery]).then(results => {
            this.userConnected=results[0];
        });
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

    getCookies() {
        this.cookiesService.get('');
    }


    createGuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    resiliate() {
        this.displayResiliateModal = false;
        this.displayConfirmationModal = false;
        this.displayResiliateQuestion = false;
        let reason = '';
        let comment = this.reasonInput;
        this.causes.forEach(function (value) {
            if (value.checked === true) {
                if (value.message === 'Autre') {
                    reason = comment;
                } else {
                    reason = value.message;
                }
            }
        });
        let resilate = this.avocatService.resiliate([this.userConnected, reason]);
        Promise.all([resilate]).then(results => {

        });
    }

    updateSelection(position, causes) {
        for (var _index = 0; _index < causes.length; _index++) {
            let cause = this.causes[_index];
            cause.checked = (position != _index) ? false : true
            this.causes[_index] = cause;
            if (_index == 2) {
                this.reasonInput = '';
            }


        }
    }

    toggleSidebar() {
        let wrapperElement = document.querySelector('.wrapper');
        if (wrapperElement !== undefined) {
            wrapperElement.classList.toggle('menu-open');
        }
    }

    filterSeeAll(status) {
        localStorage.setItem('status', status);
        window.location.href = location.origin + '/myact';
    }
    filterSeeByIds() {
        let arrayId=[];
        for(let i=0;i<this.lastActs.length;i++){
            arrayId.push(this.lastActs[i].id);
        }
        localStorage.setItem('array', JSON.stringify(arrayId));
        window.location.href = location.origin + '/myact';
    }

    goToSynthese(item) {
        localStorage.setItem('act', item.id);
        window.location.href = location.origin + '/myact';
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

    get displayContactAdd(): boolean {
        return BaseViewModel.displayContactAdd;
    }

    set displayContactAdd(displayContactAdd: boolean) {
        BaseViewModel.displayContactAdd = displayContactAdd;
    }

    assignActeModel(c) {
        localStorage.setItem('act', c.id);
        if (c.type === '2') {
            window.location.href = location.origin + '/contact';
        } else {
            window.location.href = location.origin + '/myact';
        }

    }
    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }


}