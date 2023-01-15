import {
    animate,
    Component,
    ElementRef,
    Input,
    OnChanges,
    OnInit, SimpleChanges,
    style,
    transition,
    trigger,
    ViewChild
} from '@angular/core';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {Avocat} from '../../../shared/entities/avocat';
import {Order} from "../../../shared/entities/order";
import {Accordion} from "primeng/accordion";


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
    selector: 'searchAvocat',
    templateUrl: './SearchAvocatComponent.html',
    styleUrls: ['./OrdersComponent.css'],
    animations: [
        fadeInOut('fadeInOut-3', 2)
    ]
})

export class SearchAvocatComponent implements OnInit, OnChanges {
    @Input() vm: IOrdersViewModelMaster;
    @ViewChild('yourInput') yourInput: ElementRef;
    listeAvocat: Avocat[] = [];
    listeAvocats: Avocat[] = [];
    addedAvocats: Avocat[] = [];
    array = Array;
    count: string;
    displayDivForAutoComplete: boolean;
    active: any = '';
    conditionAccepted: boolean;
    modifiedAvocat: Avocat;
    selectedIndex: any[] = [];
    emailList: any[] = [];
    phoneList: any[] = [];
    displayListEmailAuto: boolean = false;
    displayListPhoneAuto: boolean = false;
    duplicant: boolean = false;
    codeCountryList: any[] = [];
    validatedFormName: boolean = false;
    validatedFormLastName: boolean = false;
    validatedFormEmail: boolean = false;
    validatedFormPhone: boolean = false;
    showSearchField: boolean = true;

    firstName: string = '';
    lastNameValue: string = '';
    searchResult: any[] = null;
    displayPopupForDeleteCouncel: boolean = false;
    @ViewChild('accordion') accordion: Accordion;
    showCreateAvocat: boolean = false;

    constructor() {
    }

    ngOnChanges(changes: SimpleChanges) {
    }

    closeAll() {
        if(this.accordion && this.accordion.tabs){
            for(let tab of this.accordion.tabs) {
                if(tab.selected) tab.selected = false;
            }
        }
    }

    deleteAddedAvocat(i) {
        if (this.addedAvocats.length == 1) {
            this.addedAvocats.splice(i, 1);
            this.addedAvocats = [];
            this.addedAvocats.splice(i, 1);
        }
        else {
            this.addedAvocats.splice(i, 1);
        }
        this.showCreateAvocat=false;
        this.createNewAvocat();
    }

    showDropdown(index) {
        if (document.getElementById(index)) {
            document.getElementById(index).classList.toggle("show");
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show') && openDropdown.id != index) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }


    ngOnInit() {
        this.listeAvocat = new Array<Avocat>(0).fill({});
        this.addedAvocats.push(new Avocat());
        OrdersViewModelMaster.avocatToModify && OrdersViewModelMaster.avocatToModify.name ? this.listeAvocat.push({...OrdersViewModelMaster.avocatToModify}) : this.listeAvocat.push(new Avocat());
        window.scrollTo(0, 0);
        if (OrdersViewModelMaster.avocatToModify){
            this.fillEmailList(OrdersViewModelMaster.avocatToModify);
            this.fillPhoneList(OrdersViewModelMaster.avocatToModify);
        }
    }

    ngDoCheck() {
        for (let avocat of this.addedAvocats) {
            let existItem = this.addedAvocats.filter(item => item.name === avocat.name && item.email == avocat.email && item.lastName === avocat.lastName);
            this.duplicant = existItem.length > 1;
        }
    }

    autocomplete(lastNameValue,firstName) {
        let AvocatQuery = this.vm.services.avocatService.autocomplete(lastNameValue,firstName);
        Promise.all([AvocatQuery]).then(result => {
            for(let i=0;i<result[0].length;i++){
                if(result[0][i].email){
                    if(result[0][i].email.replace(/^0+/, '') == result[0][i].cnbId + 'cnb@cnb.fr' || result[0][i].email.replace(/^0+/, '') == result[0][i].cnbId + '@cnb.fr') {
                        result[0][i].email = null;
                    }

                }
                result[0][i].phoneNumberTest=result[0][i].phoneNumber;
            }
            this.searchResult = result[0];
        });
    }

    searchAvocat() {
        this.showCreateAvocat = false;
        this.searchResult = null;
        this.showSearchField=true;
        this.autocomplete(this.lastNameValue,this.firstName);
    }


    emptyAvocatToModifiy() {
        OrdersViewModelMaster.avocatToModify = new Avocat();
    }

    testchamp(champ): boolean {
        if (champ) {
            return champ.length > 26
        }
    }

    testPhone(champ): boolean {
        if (champ) {
            return champ.length > 25
        }
    }

    testPhoneCase(champ): boolean {
        if (champ) {
            return champ.length == 3;
        }
    }
    removeBlankSpace(event:any){
        event = event.replace(/\s/g, "");
        return event;
    }



    createNewAvocat() {
        this.addedAvocats.push(new Avocat());
    }

    assignAvocat(value: any, index: any) {

        if (value.email.replace(/^0+/, '') == value.cnbId + 'cnb@cnb.fr' || value.email.replace(/^0+/, '') == value.cnbId + '@cnb.fr') {
            value.email = null;
        }
        this.listeAvocat[index] = {...value};
        this.fillEmailList(this.listeAvocat[index]);
        this.fillPhoneList(this.listeAvocat[index]);
        this.displayDivForAutoComplete = false;
    }

    displayListEmail(value: any) {
        this.displayListEmailAuto = value == true;

    }
    displayListPhone(value: any) {
        this.displayListPhoneAuto = value == true;

    }

    fillEmailList(value: any) {

        if (value.cnbId != null) {
            this.emailList = [value.email, value.emailEd,value.counselEmail];
            this.emailList = _.union(this.emailList);
            this.emailList = this.emailList.filter(function (el) {
                return el != null && el != 'null' && el.replace(/^0+/, '') != value.cnbId + 'cnb@cnb.fr' && el.replace(/^0+/, '') != value.cnbId + '@cnb.fr';
            });
            if (this.emailList.length < 3) {
                this.emailList = [value.email, value.emailApp, value.emailEd,value.counselEmail];
                this.emailList = _.union(this.emailList);
                this.emailList = this.emailList.filter(function (el) {
                    return el != null && el != 'null' && el.replace(/^0+/, '') != value.cnbId + 'cnb@cnb.fr' && el.replace(/^0+/, '') != value.cnbId + '@cnb.fr';
                });
            }
        }
    }

    getCountry() {
        return 'fr';
    }

    allowedCountries(){
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }

    phoneNumberRestrict(event: any) {
        const pattern = /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
        let inputChar = String.fromCharCode(event.keyCode);
        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }

    fillPhoneList(value: any) {
        if (value.cnbId != null) {
            this.phoneList = [{num:value.phoneNumber,code:value.codeCountry}, {num:value.phoneNumberEd,code:value.codeCountryEd},{num:value.counselPhone,code:value.counselCodeCountryApp}];
            let uniqIds = {}
            this.phoneList = this.phoneList.filter(obj => !uniqIds[obj.num] && (uniqIds[obj.num] = true));
            this.phoneList = this.phoneList.filter(function (el) {
                return el.num != null && el.num != 'null'
            });
            if (this.phoneList.length < 3) {
                this.phoneList = [{num:value.phoneNumber,code:value.codeCountry}, {num:value.phoneNumberApp,code:value.codeCountryApp}, {num:value.phoneNumberEd,code:value.codeCountryEd},{num:value.counselPhone,code:value.counselCodeCountryApp}];
                let uniqIds = {}
                this.phoneList = this.phoneList.filter(obj => !uniqIds[obj.num] && (uniqIds[obj.num] = true));
                this.phoneList = this.phoneList.filter(function (el) {
                    return el.num != null && el.num != 'null'
                });
            }
        }
    }

    focus(): void {
        this.yourInput.nativeElement.focus();

    }
    focusOnchange(){
        setTimeout(() =>  this.yourInput.nativeElement.focus(), 300);

    }

    onKey(event: any) {
        event.target.value = event.target.value.toUpperCase();
    }

    onkeyLastName(event : any){
        event.target.value = event.target.value.toLowerCase()
            .split(" ")
            .map(function (e) {
                return e.charAt(0).toUpperCase() + e.substring(1);
            })
            .join(" ")
            .split("-")
            .map(function (e) {
                return e.charAt(0).toUpperCase() + e.substring(1);
            })
            .join("-")
        console.log(event.target.value);
    }


    onKeyLastName(event: any){
        event.target.value = event.target.value.charAt(0).toLocaleUpperCase() + event.target.value.slice(1);
    }

    deleteAvocatPopup(){
        this.displayPopupForDeleteCouncel = true;
    }

    get displayModalSignataire(): boolean {
        return OrdersViewModelMaster.displayModalSignataire;
    }

    set displayModalSignataire(value: boolean) {
        OrdersViewModelMaster.displayModalSignataire = value;
    }

    get orderDetails(): Order {
        return OrdersViewModelMaster.currentActe;
    }
    set orderDetails(orderDetails: Order) {
        OrdersViewModelMaster.currentActe = orderDetails;
    }

    set listselectedAvocats(value: Avocat[]) {
        OrdersViewModelMaster.listAvocat = value;
    }
    get listselectedAvocats(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }

    get displayModalAvocat(): boolean {
        return OrdersViewModelMaster.displayModalAvocat;
    }

    set displayModalAvocat(value: boolean) {
        OrdersViewModelMaster.displayModalAvocat = value;
    }

    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }

    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }

    isAvocatSelected(avocat) {
        return this.listAvocat.findIndex(s => s.name == avocat.name && s.email == avocat.email) !== -1;
    }

    saveAvocat(avocat) {

            if (!this.listAvocat.some((s => s.name == avocat.name && s.email == avocat.email))) {
                avocat.mailSent = null;
                avocat.validator = null;
                avocat.validatedAt = null;
                avocat.comment = null;
                avocat.birthDate = null;
                avocat.birthPlace = null;
                avocat.role = "counsel";
                avocat.enterpriseName = null;
                avocat.actId = OrdersViewModelMaster.idActe.toString();
                console.log(avocat.codeCountry);
                let avocatyQuery = this.vm.services.avocatService.postSignataire(avocat);
                Promise.all([avocatyQuery.catch(err => {
                    if (err._body === '"exist"') {
                        this.vm.services.messageService.add({
                            severity: 'error',
                            summary: 'Ajout de l\'avocat au dossier',
                            detail: avocat.name[0].toUpperCase() + avocat.name.slice(1) + ' a déjà été rajouté à l\'acte', life: 4000
                        });
                        OrdersViewModelMaster.displayModalAvocat = true;
                    } else {
                        OrdersViewModelMaster.displayModalAvocat = true;
                        this.vm.services.messageService.add({
                            severity: 'error',
                            summary: 'Ajout de l\'avocat au dossier',
                            detail: avocat.name[0].toUpperCase() + avocat.name.slice(1) + ' existe déjà dans la liste des contacts, vous ne pouvez pas le modifier',
                            life: 4000
                        });
                    }
                    throw err
                })]).then(results => {
                    this.closeAll();
                    this.listeAvocats.push(avocat);
                    this.vm.services.messageService.add({
                        severity: 'success',
                        summary: 'Ajout de l\'avocat au dossier',
                        detail: 'L\'avocat ' + avocat.name + ' a été rajouté avec succès', life: 4000
                    });
                    avocat.id = results[0];
                    OrdersViewModelMaster.listAvocat.push(avocat);
                    OrdersViewModelMaster.avocatToModify = new Avocat();
                    OrdersViewModelMaster.displayModalAvocat = false;
                    if (!this.vm.allAvocat.some(s => s.id == avocat.id)) {
                        avocat.phoneNumber=(avocat.phoneNumber[0]=='0')?avocat.phoneNumber.substring(1):avocat.phoneNumber;
                        if(avocat.codeCountry==undefined){
                            avocat.codeCountry='+33';
                        }
                        if(avocat.email!=avocat.emailEd && avocat.email!=avocat.emailApp ){
                            avocat.counselEmail = avocat.email;
                        }
                        if(avocat.phoneNumber!=avocat.phoneNumberEd && avocat.phoneNumber!=avocat.phoneNumberApp ){
                            avocat.counselPhone = avocat.phoneNumber;
                        }

                        this.vm.allAvocat.push(avocat);
                    }

                    this.showSearchField=false;
                    this.displayDivForAutoComplete=false;
                    this.showCreateAvocat=false;
                    this.vm.getData();
                })
            }
            else {
                this.vm.services.messageService.add({
                    severity: 'error',
                    summary: 'Ajout de l\'avocat au dossier',
                    detail: avocat.name[0].toUpperCase() + avocat.name.slice(1) + ' a déjà été rajouté à l\'acte', life: 4000
                });
            }

    }
    deleteAvocat(avocat) {
        this.vm.services.avocatService.deleteAvocat(avocat).then(() => {
            let indexToDelete = this.listAvocat.findIndex(a => a.name === avocat.nae && a.email === avocat.email);
            this.listeAvocats.splice(indexToDelete, 1);

            this.vm.services.messageService.add({ severity: 'success', summary: 'Supression avocat', detail: avocat.name[0].toUpperCase() + avocat.name.slice(1) + ' supprimé avec succès', life: 4000 });
            for (let av of this.listAvocat) {
                if (av.name == avocat.name && av.email == avocat.email && av.actId == avocat.actId) {
                    this.listAvocat = this.listAvocat.filter(a => a != av);
                }
            }
        });
    }


    addAvocats() {
        OrdersViewModelMaster.displayModalAvocat = false;
        OrdersViewModelMaster.displaySearchAvocat = false;
        this.vm.getData();
    }


    avocatToModify(avocat: any): void {
        OrdersViewModelMaster.avocatToModify = avocat;
    }

    get listAvocat(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }

    set listAvocat(value: Avocat[]) {
        OrdersViewModelMaster.listAvocat = value;
    }

    disabledAddAndModifyButton(): boolean {
        return this.addedAvocats.some(avocat => !avocat.name || !avocat.phoneNumber || !avocat.lastName || !avocat.email);

    }

    get allAvocat(): Avocat[] {
        return this.vm.allAvocat.filter((object, index, self) =>
            index === self.findIndex((t) => (
                t.name === object.name && t.email === object.email
            ))
        );
    }

    changeComponent() {
        if (this.displayCreateOrder) {
            this.displayCreateOrder = false;
        }
        else if (this.displayConsultOrder || this.displaySendTovalidation) {
            this.displayConsultOrder = false;
            this.displaySendTovalidation = false;
        }
        else if (this.vm.displayActeRefused) {
            this.vm.displayActeRefused = false;
        }
        this.displayModalAvocat = false;
    }

    get displayCreateOrder(): boolean {
        return OrdersViewModelMaster.displayCreateOrder;
    }

    set displayCreateOrder(value: boolean) {
        OrdersViewModelMaster.displayCreateOrder = value;
    }

    get displayConsultOrder(): boolean {
        return OrdersViewModelMaster.displayConsultOrder;
    }

    set displayConsultOrder(value: boolean) {
        OrdersViewModelMaster.displayConsultOrder = value;
    }

    get displaySendTovalidation(): boolean {
        return OrdersViewModelMaster.displaySendTovalidation;
    }

    set displaySendTovalidation(value: boolean) {
        OrdersViewModelMaster.displaySendTovalidation = value;
    }
    get debugdata() {
        return {duplicant: this.duplicant,  validatedFormName: this.validatedFormName, validatedFormPhone: this.validatedFormPhone, validatedFormLastName: this.validatedFormLastName, validatedFormEmail: this.validatedFormEmail}
    }
}