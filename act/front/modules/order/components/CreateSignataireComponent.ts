import {animate, Component, ElementRef, Input, OnInit, style, transition, trigger, ViewChild} from '@angular/core';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {Signataire} from '../../../shared/entities/signataire';
import * as moment from "moment";

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
    selector: 'CreateSignataireComponent',
    templateUrl: './CreateSignataireComponent.html',
    providers: [MessageService],
    styleUrls: ['./OrdersComponent.css'],
    animations: [
        fadeInOut('fadeInOut-3', 2)
    ]
})

export class CreateSignataireComponent implements OnInit {

    @ViewChild('yourInput') yourInput: ElementRef;
    @ViewChild('name') name: ElementRef;
    listeSignataire: Signataire[] = [];
    array = Array;
    @Input() vm: IOrdersViewModelMaster;
    displayDivForAutoComplete: boolean;
    conditionAccepted: boolean = false;
    modifiedSignataire: Signataire;
    errorValidation: boolean = false;
    displayDivForAutoCompleteLastName: boolean;
    fr: any;
    duplicant: boolean = false;
    validatedFormName: boolean = false;
    validatedFormLastName: boolean = false;
    validatedFormEmail: boolean = false;
    validatedFormPhone: boolean = false;
    roleChangedToSignatory : boolean = false;


    constructor() {
        this.fr = {
            firstDayOfWeek: 1,
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthNamesShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc"],
            today: "Aujourd'hui",
            clear: 'Effacer'
        };
    }

    ngOnInit() {
        this.listeSignataire = new Array<Signataire>(0).fill({});
        let sig = new Signataire();
        sig.role = "signatory";
        OrdersViewModelMaster.signataireToModify && OrdersViewModelMaster.signataireToModify.name ? this.listeSignataire.push({ ...OrdersViewModelMaster.signataireToModify }) : this.listeSignataire.push(sig);;
        window.scrollTo(0, 0);

    }
    ngDoCheck() {
        for (let sig of this.listeSignataire) {
            let existItem = this.listeSignataire.filter(item => item.name === sig.name && item.email == sig.email && item.lastName === sig.lastName);
            this.duplicant = existItem.length > 1;
        }
    }

    emptySignataireToModifiy() {
        OrdersViewModelMaster.signataireToModify = new Signataire();
    }
    createNewSignataire() {
        let sig = new Signataire();
        sig.role = "signatory";
        this.listeSignataire.push(sig);
    }

    assignSignatory(value: any, index: any) {
        this.listeSignataire[index] = { ...value };
        this.displayDivForAutoComplete = false;
    }

    testchamp(champ):boolean{
        if (champ){
            return champ.length>51
        }
    }

    testDate(champ): boolean {
        if (moment().diff(moment(champ, 'DD/MM/YYYY'), 'years') < 18) {
            return champ
        }
    }

    testPhone(champ):boolean{
        if (champ){
            return champ.length>25
        }
    }

    onKey(event: any){
        event.target.value = event.target.value.toUpperCase();
        console.log(event.target.value);
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

    testPhoneCase(champ):boolean{
        if (champ){
            return champ.length == 3;
        }
    }

    inputValidation():boolean{
        return this.errorValidation = true;

    }

    currentYear(){
        return  (new Date()).getFullYear();
    }

    saveSignataire() {
        for (let sig of this.listeSignataire) {
            console.log(this.roleChangedToSignatory)
            console.log(sig.enterpriseName)
            if (this.roleChangedToSignatory == true){
                sig.enterpriseName = "";
            }
            if (!this.listSignataire.some((s => s.name == sig.name && s.email == sig.email && s.enterpriseName == sig.enterpriseName))) {
                sig.mailSent = null;
                sig.validator = null;
                sig.comment = null;
                sig.validatedAt = null;
                sig.signedAt = null;
                sig.signatureComment = null;
                if (!sig.role) {
                    sig["role"] = "signatory";
                }
                sig["name"] = sig.name.toUpperCase();
                sig.actId = OrdersViewModelMaster.idActe.toString();
                let signatoryQuery = this.vm.services.signataireService.postSignataire(sig);
                Promise.all([signatoryQuery.catch(err => {
                    OrdersViewModelMaster.displayModalSignataire = true;
                    this.vm.services.messageService.add({
                        severity: 'error',
                        summary: 'Ajout de signataire(s)',
                        detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' existe déjà dans la liste des contacts, vous ne pouvez pas le modifier',
                        life: 4000
                    });
                    console.log('err', err)
                    throw err
                })]).then(results => {
                    sig.id = results[0];
                    console.log(sig.id);
                    
                    this.listSignataire.push(sig);
                    if (!this.vm.allSignataire.some(s => s.name == sig.name && s.email == sig.email && s.actId == sig.actId)) {
                        sig.phoneNumber = (sig.phoneNumber[0] == '0') ? sig.phoneNumber.substring(1) : sig.phoneNumber;
                        if (sig.codeCountry == undefined) {
                            sig.codeCountry = '+33';
                        }
                        this.vm.allSignataire.push(sig);
                    }
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Ajout de signataire(s)', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + '  a été rajouté avec succès', life: 4000 });
                    OrdersViewModelMaster.signataireToModify = new Signataire();
                    OrdersViewModelMaster.displayModalSignataire = false;
                })
            }
            else {
                this.vm.services.messageService.add({ severity: 'error', summary: 'Ajout de signataire(s)', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' a déjà été rajouté à l\'acte', life: 4000 });
                OrdersViewModelMaster.displayModalSignataire = true;
            }
        }
    }
    modifySignataire() {
        if (this.modifiedSignataire == undefined) {
            this.displayModalSignataire = false
        }
        if (this.modifiedSignataire.birthDate == 'Invalid date') {
            this.modifiedSignataire.birthDate = null;
        }
        for (let sig of this.listeSignataire) {
            console.log(this.listeSignataire);
            console.log(this.listSignataire);
            debugger;

            if (this.listSignataire.some(s => s.name == sig.name || s.enterpriseName == sig.enterpriseName)) {
                if (!sig.role) {
                    sig["role"] = "signatory";
                }
                sig.actId = OrdersViewModelMaster.idActe.toString();
                let signatoryQuery = this.vm.services.signataireService.modifySignataire(sig);
                Promise.all([signatoryQuery.catch(err => {
                    OrdersViewModelMaster.displayModalSignataire = true;
                    this.vm.services.messageService.add({
                        severity: 'error',
                        summary: 'Modification des informations',
                        detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' existe déjà dans la liste des contacts, vous ne pouvez pas le modifier',
                        life: 4000
                    });
                    console.log('err', err)
                    throw err
                })]).then(results => {
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Modification des informations', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' modifié avec succès', life: 4000 });
                    for (let s of this.listSignataire) {
                        if (s.name == this.signataireToModify.name && s.enterpriseName == this.signataireToModify.enterpriseName && s.actId ) {
                            let index = this.listSignataire.indexOf(s);
                            if (~index) {
                                this.modifiedSignataire.phoneNumber = (this.modifiedSignataire.phoneNumber[0] == '0') ? this.modifiedSignataire.phoneNumber.substring(1) : this.modifiedSignataire.phoneNumber;
                                if (this.modifiedSignataire.codeCountry == undefined) {
                                    this.modifiedSignataire.codeCountry = '+33';
                                }
                                this.modifiedSignataire.actId = s.actId;
                                this.modifiedSignataire.validator = s.validator;
                                this.modifiedSignataire.mailSent = s.mailSent;
                                this.modifiedSignataire.birthDate = moment(this.modifiedSignataire.birthDate).format("DD/MM/YYYY");
                                this.listSignataire[index] = {...this.modifiedSignataire};
                            }
                        }
                    }

                    for (let s of this.vm.allSignataire) {
                        if (s.name == this.signataireToModify.name && s.actId && s.enterpriseName == this.signataireToModify.enterpriseName) {
                            let index = this.vm.allSignataire.indexOf(s);
                            if (~index) {
                                this.modifiedSignataire.phoneNumber = (this.modifiedSignataire.phoneNumber[0] == '0') ? this.modifiedSignataire.phoneNumber.substring(1) : this.modifiedSignataire.phoneNumber;
                                if (this.modifiedSignataire.codeCountry == undefined) {
                                    this.modifiedSignataire.codeCountry = '+33';
                                }
                                this.modifiedSignataire.actId = s.actId;
                                this.modifiedSignataire.validator = s.validator;
                                this.modifiedSignataire.mailSent = s.mailSent;
                                this.modifiedSignataire.birthDate = moment(this.modifiedSignataire.birthDate).format("DD/MM/YYYY");
                                this.vm.allSignataire[index] = {...this.modifiedSignataire};
                            }
                        }
                    }
                    OrdersViewModelMaster.signataireToModify = new Signataire();
                    this.displayModalSignataire = false
                });
            }
            else {
                this.vm.services.messageService.add({ severity: 'error', summary: 'Modification des informations', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' a déjà été rajouté à l\'acte', life: 4000 });
            }
        }
    }

    deleteSignataire(i: any) {
        if (this.listeSignataire.length == 1) {
            this.listeSignataire.splice(i, 1);
            this.listeSignataire = [];
            this.listeSignataire.splice(i, 1);
            OrdersViewModelMaster.displayModalSignataire = false;
        }
        else {
            this.listeSignataire.splice(i, 1);
        }
        OrdersViewModelMaster.signataireToModify = new Signataire();

    }
    focus(item): void {
        this.yourInput.nativeElement.focus();

    }
    focusOnchange(){
        setTimeout(() =>  this.yourInput.nativeElement.focus(), 300);

    }
    getCountry() {
        return 'fr';
    }

    allowedCountries() {
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }

    removeBlankSpace(event:any){
        event = event.replace(/\s/g, "");
        return event;
    }

    phoneNumberRestrict(event: any) {
        const pattern = /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
        let inputChar = String.fromCharCode(event.keyCode);
        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }


    get listSignataire(): Signataire[] {
        return OrdersViewModelMaster.listSignataire;
    }
    set listSignataire(listSignataire: Signataire[]) {
        OrdersViewModelMaster.listSignataire = listSignataire;
    }
    get allSignataire(): Signataire[] {
        return OrdersViewModelMaster.allSignataire;
    }

    get signataireToModify(): Signataire {
        return OrdersViewModelMaster.signataireToModify;
    }
    get displayModalSignataire(): boolean {
        return OrdersViewModelMaster.displayModalSignataire;
    }
    set displayModalSignataire(value: boolean) {
        OrdersViewModelMaster.displayModalSignataire = value;
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
    get allSignatairesLastName(): Signataire[] {
        return this.vm.allSignataire.filter((object, index, self) =>
            index === self.findIndex((t) => (
                t.lastName === object.lastName && t.email === object.email
            ))
        );
    }
    get allSignatairesName(): Signataire[] {
        return this.vm.allSignataire.filter((object, index, self) =>
            index === self.findIndex((t) => ( 
                t.name === object.name && t.email === object.email && t.enterpriseName === object.enterpriseName
            ))
        );
    }

    testSignatoryRoleSelected(): boolean {
        return this.listeSignataire.length == 0 || this.listeSignataire.some(s => !s.role);
    }

    disabledAddAndModifyButton(): boolean {
        let test: boolean = false;
        for (let signtaire of this.listeSignataire) {
            if (signtaire.role == 'enterprise' && (!signtaire.name || !signtaire.email || !signtaire.lastName || !signtaire.phoneNumber || !signtaire.enterpriseName || !signtaire.siren)) {
                test= false;
            }
            else if (signtaire.role =='signatory' && (!signtaire.name || !signtaire.email || !signtaire.lastName || !signtaire.phoneNumber) ){
                test= false
            }
            else
                test= true;
        }
        return test;
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
        this.displayModalSignataire = false;
    }
}
