import { Component, OnInit, ViewChild, ElementRef, trigger, transition, style, animate } from '@angular/core';
import { MessageService } from 'primeng/api';
import { Signataire } from '../../../shared/entities/signataire';
import { AvocatService } from '../../../shared/services/avocat.services';
import * as moment from "moment";
import { Avocat } from '../../../shared/entities/avocat';
import { BaseViewModel } from './Static/BaseViewModel';


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
    selector: 'CreateContactComponent',
    templateUrl: './CreateContactComponent.html',
    providers: [MessageService],
    styleUrls: ['./ListContactComponent.css'],
    animations: [
        fadeInOut('fadeInOut-3', 2)
    ]
})

export class CreateContactComponent implements OnInit {
    @ViewChild('yourInput') yourInput: ElementRef;
    listeSignataire: any[] = [];
    array = Array;
    displayDivForAutoComplete: boolean;
    conditionAccepted: boolean = false;
    modifiedSignataire: Signataire;
    fr: any;
    validatedFormName: boolean = false;
    validatedFormLastName: boolean = false;
    validatedFormEmail: boolean = false;
    validatedFormPhone: boolean = false;
    roleChangedToSignatory : boolean = false;

    constructor(private messageService: MessageService, private avocatService: AvocatService) {
    }
    filterChocieNature: string;
    choicesNature = [
        { label: 'Nature de contact', value: null },
        { label: 'Avocat', value: 'ROLE_COUNSEL' },
        { label: 'Signataire', value: 'ROLE_SIGNATORY' },
    ];

    getRoles(item) {
        if (item.roles == 'ROLE_ENTERPRISE') {
            return 'ROLE_SIGNATORY';
        }
        else
            return item.roles;
    }
    get consultContact() {
        return BaseViewModel.consultContact;
    }
    set consultContact(consultContact: boolean) {
        BaseViewModel.consultContact = consultContact;
    }
    getRole(item) {
        if (item.roles == 'ROLE_COUNSEL') {
            return 'counsel';
        }
        else
            return item.role;
    }
    ngOnInit() {

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
        window.scrollTo(0, 0);
        this.listeSignataire = new Array<Signataire>(0).fill({});
        let sig = new Signataire();
        sig.role = null;
        BaseViewModel.contactToModify && BaseViewModel.contactToModify.name ? this.listeSignataire.push({ ...BaseViewModel.contactToModify }) : this.listeSignataire.push(sig);;
        window.scrollTo(0, 0);
    }
    get contactToModify(): Signataire {
        return BaseViewModel.contactToModify;
    }

    emptyContactToModify() {
        BaseViewModel.contactToModify = new Signataire();
        this.consultContact = false;
    }
    createNewSignataire() {
        let sig = new Signataire();
        this.listeSignataire.push(sig);
    }
    assignSignatory(value: any, index: any) {
        this.listeSignataire[index] = { ...value };
        this.displayDivForAutoComplete = false;
    }

    testchamp(champ): boolean {
        if (champ) {
            return champ.length > 50
        }
    }

    testDate(champ): boolean {
        if (moment().diff(moment(champ, 'DD/MM/YYYY'), 'years') < 18) {
            return champ
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
    currentYear() {
        return (new Date()).getFullYear();
    }

    removeBlankSpace(event:any){
        event = event.replace(/\s/g, "");
        return event;
    }

    saveContact() {
        for (let sig of this.listeSignataire) {
            if (this.roleChangedToSignatory == true){
                sig.enterpriseName = "";
            }
            if (!BaseViewModel.allContact.some((s => s.email == sig.email && s.name==sig.name && s.lastName==sig.lastName && s.enterpriseName == sig.enenterpriseName))) {
                let signatoryQuery = this.avocatService.addContact(sig);
                Promise.all([signatoryQuery.catch(err => {
                    this.messageService.add({ severity: 'error', summary: 'Ajout contact', detail: 'Cette personne existe déjà dans la liste des contacts', life: 4000 });
                    setTimeout(() => {
                        BaseViewModel.displayContactAdd = true;
                    }, 4000);
                    console.log('err', err)
                    throw err
                })]).then(results => {
                    results[0].roles = results[0].roles[0];
                    if (results[0].roles == 'ROLE_ENTERPRISE') {
                        results[0]["role"] = 'enterprise';
                    }
                    else if (results[0].roles == 'ROLE_COUNSEL') {
                        results[0]["role"] = 'counsel';
                    }
                    else if (results[0].roles == 'ROLE_SIGNATORY') {
                        results[0]["role"] = 'signatory';
                    }
                    BaseViewModel.allContact.push(results[0]);
                    BaseViewModel.contactToModify = new Signataire();
                    this.messageService.add({ severity: 'success', summary: 'Ajout contact', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' a été rajouté avec succès', life: 3000 });
                    setTimeout(() => {
                        BaseViewModel.displayContactAdd = false;
                    }, 4000);
                })
            }
            else {
                this.messageService.add({ severity: 'error', summary: 'Ajout contact', detail: sig.name[0].toUpperCase() + sig.name.slice(1) + ' existe déjà', life: 4000 });
                BaseViewModel.displayContactAdd = true;
            }
        }
    }

    modifyContact() {
        for (let sig of this.listeSignataire) {
            if (sig.birthDate == 'Invalid date') {
                sig.birthDate = null;
            }
            sig.roles = sig.roles[0];
            if (sig.roles == 'ROLE_ENTERPRISE') {
                sig["role"] = 'enterprise';
            }
            else if (sig.roles == 'ROLE_COUNSEL') {
                sig["role"] = 'counsel';
            }
            else if (sig.roles == 'ROLE_SIGNATORY') {
                sig["role"] = 'signatory';
            }
            let signatoryQuery = this.avocatService.modifyContact(sig);
            Promise.all([signatoryQuery.catch(err => {
                BaseViewModel.displayContactAdd = true;
                throw err
            })]).then(results => {
                sig.phoneNumber = (sig.phoneNumber[0] == '0') ? sig.phoneNumber.substring(1) : sig.phoneNumber;
                if (sig == undefined) {
                    sig = '+33';
                }
                sig.birthDate = moment(sig.birthDate).format("DD/MM/YYYY");
                BaseViewModel.allContact = BaseViewModel.allContact.filter(contact => contact.id != sig.id);
                BaseViewModel.allContact.push(sig);
                if (BaseViewModel.allAvocat.some(avocat => avocat.actId && avocat.id == sig.id) || BaseViewModel.allSignataire.some(signataire => signataire.actId && signataire.id == sig.id)) {
                    this.messageService.add({ severity: 'info', summary: 'Contact', detail: 'Vous devez envoyer une relance (obligatoire) pour que les données soit modifiés pour '+sig.name, life: 4000 });
                    console.log('in if');
                }
                setTimeout(() => {
                    BaseViewModel.displayContactAdd = false;
                }, 4000);

            })
        }
    }

    deleteSignataire(i: any) {
        if (this.listeSignataire.length == 1) {
            this.listeSignataire.splice(i, 1);
            this.listeSignataire = [];
            this.listeSignataire.splice(i, 1);
            BaseViewModel.displayContactAdd = false;
        }
        else {
            this.listeSignataire.splice(i, 1);
        }
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


    get listSignataire(): Signataire[] {
        return this.listSignataire;
    }
    set listSignataire(listSignataire: Signataire[]) {
        this.listSignataire = listSignataire;
    }
    get displayContactAdd(): boolean {
        return BaseViewModel.displayContactAdd;
    }
    set displayContactAdd(displayContactAdd: boolean) {
        BaseViewModel.displayContactAdd = displayContactAdd;
    }
    getCountry() {
        return 'fr';
    }

    allowedCountries() {
        return ['af', 'al', 'dz', 'as', 'ad', 'ao', 'ai', 'aq', 'ag', 'ar', 'am', 'aw', 'au', 'at', 'az', 'bs', 'bh', 'bd', 'bb', 'by', 'be', 'bz', 'bj', 'bm', 'bt', 'bo', 'ba', 'bw', 'bv', 'br', 'io', 'bn', 'bg', 'bf', 'bi', 'bq', 'kh', 'cm', 'ca', 'cv', 'ky', 'cf', 'td', 'cl', 'cn', 'cx', 'cc', 'co', 'km', 'cd', 'cg', 'ck', 'cr', 'ci', 'hr', 'cu', 'cw', 'cy', 'cz', 'dk', 'dj', 'dm', 'do', 'ec', 'eg', 'sv', 'gq', 'er', 'ee', 'et', 'fk', 'fo', 'fj', 'fi', 'fr', 'gf', 'pf', 'tf', 'ga', 'gm', 'ge', 'de', 'gh', 'gi', 'gr', 'gl', 'gd', 'gp', 'gu', 'gt', 'gg', 'gn', 'gw', 'gy', 'ht', 'hm', 'hn', 'hk', 'hu', 'is', 'in', 'id', 'ir', 'iq', 'ie', 'im', 'il', 'it', 'jm', 'jp', 'je', 'jo', 'kz', 'ke', 'ki', 'kp', 'kr', 'xk', 'kw', 'kg', 'la', 'lv', 'lb', 'ls', 'lr', 'ly', 'li', 'lt', 'lu', 'mo', 'mk', 'mg', 'mw', 'my', 'mv', 'ml', 'mt', 'mh', 'mq', 'mr', 'mu', 'yt', 'mx', 'fm', 'md', 'mc', 'mn', 'me', 'ms', 'ma', 'mz', 'mm', 'na', 'nr', 'np', 'nl', 'an', 'nc', 'nz', 'ni', 'ne', 'ng', 'nu', 'nf', 'mp', 'no', 'om', 'pk', 'pw', 'ps', 'pa', 'pg', 'py', 'pe', 'ph', 'pn', 'pl', 'pt', 'pr', 'qa', 're', 'ro', 'ru', 'rw', 'sh', 'kn', 'lc', 'pm', 'vc', 'ws', 'bl', 'sm', 'st', 'sa', 'sn', 'cs', 'rs', 'sc', 'sl', 'sg', 'sx', 'sk', 'si', 'sb', 'so', 'za', 'gs', 'es', 'lk', 'sd', 'ss', 'sr', 'sj', 'sz', 'se', 'ch', 'sy', 'tw', 'tj', 'tz', 'th', 'tl', 'tg', 'tk', 'to', 'tt', 'tn', 'tr', 'tm', 'tc', 'tv', 'ug', 'ua', 'ae', 'gb', 'us', 'um', 'uy', 'uz', 'vu', 'va', 've', 'vn', 'vg', 'vi', 'wf', 'eh', 'ye', 'zm', 'zw'];
    }
    focusOnchange(){
        setTimeout(() =>  this.yourInput.nativeElement.focus(), 300);

    }

    phoneNumberRestrict(event: any) {
        const pattern = /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
        let inputChar = String.fromCharCode(event.keyCode);
        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }
    testSignatoryRoleSelected(): boolean {
        return !(this.listeSignataire.length == 0 || this.listeSignataire.some(s => s["roles"] == 'ROLE_SIGNATORY' && !s.role));
    }

    disabledAddAndModifyButton(): boolean {
        console.log(this.listeSignataire);
        
        return this.listeSignataire.some(
            signtaire => !signtaire.name ||
             !signtaire.email ||
              !signtaire.lastName ||
               !signtaire.phoneNumber ||
            (signtaire.role === 'enterprise' && (!signtaire.enterpriseName || !signtaire.siren) ));
    }
    public static set modeModify(modeModify: boolean) {
        BaseViewModel.modeModify = modeModify;
    }

    public static get modeModify() {
        return BaseViewModel.modeModify;
    }
}
