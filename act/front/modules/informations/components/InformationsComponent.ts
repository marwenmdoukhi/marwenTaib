import {animate, Component, NgZone, OnInit, style, transition, trigger, ViewChild} from '@angular/core';
import {Table} from 'primeng/table';
import {MessageService} from 'primeng/api';
import {SignataireService} from '../../../shared/services/signataire.services';
import {AvocatService} from '../../../shared/services/avocat.services';
import {Column} from '../../../shared/models/Column';
import {Cookies} from "../../../shared/entities/cookies";
import {ActeService} from "../../../shared/services/acte.services";
import {CookieService} from 'ngx-cookie-service';
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
    selector: 'informations',
    templateUrl: './InformationsComponent.html',
    styleUrls: ['./InformationsComponent.css'],
    providers: [MessageService],
    animations: [
        fadeInOut('fadeInOut-3', 3)
    ]
})

export class InformationsComponent implements OnInit {

    @ViewChild('tableOrders') tableOrders: Table;
    acctpePiwic: boolean = false;
    cookiesInofmration: Cookies;
    displayCookiesPrametres: boolean = false;
    displayCookiesModal: boolean = false;
    columns: Array<Column>;
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


    constructor(private messageService: MessageService, private avocatService: AvocatService, private signataireService: SignataireService,
                private orderService: ActeService, private cookiesService: CookieService,private spinner: NgxSpinnerService,private ngZone : NgZone) {

    }

    ngOnInit(): void {
        this.getRessources();
        window['angularComponentReference'] = { component: this, zone: this.ngZone, loadAngularFunctionforInformation: () => this.showSettings(), };

        this.columns = [
            new Column(false, true, false, 'Nom', 'lastName', '20px', 3),
            new Column(false, true, false, 'N portable annuaire', 'phoneNumberApp', '15px', 3),
            new Column(false, true, false, 'adresse mail annuaire', 'emailApp', '15px', 3),
            new Column(false, true, false, 'N portable', 'phoneNumber', '15px', 3),
            new Column(false, true, false, 'adresse mail', 'email', '15px', 3),
            new Column(false, true, false, 'svg', 'svg', '15px', 3),
        ];
        if ((this.cookiesService.get('assp-cookies'))) {
            this.acctpePiwic = true;
        } else {
            this.acctpePiwic = false;
        }

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
    showSettings(){
        this.settingsBtn = true;
        this.displayCX = true;

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
    toggleSidebar() {
        let wrapperElement = document.querySelector('.wrapper');
        if (wrapperElement !== undefined) {
            wrapperElement.classList.toggle('menu-open');
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

    getRessources() {
        let userQuery = this.orderService.getUserconnectedAsync();
        Promise.all([userQuery,this.spinner.show()]).then(results => {
            this.userConnected = results[0];
            this.spinner.hide();
            this.checkEmailCnb();
            this.checkCnbMobile();
        });

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

    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }


}