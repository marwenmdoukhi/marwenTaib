import {AfterViewInit, animate, Component, Input, OnInit, state, style, transition, trigger} from '@angular/core';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {DomSanitizer} from '@angular/platform-browser';
import {Signataire} from '../../../shared/entities/signataire';
import {Document} from "../../../shared/entities/document";
import {Avocat} from "../../../shared/entities/avocat";
import {User} from "../../../shared/entities/user";
import * as moment from "moment";

declare const $: any;

@Component({
    selector: 'consultActComponent',
    templateUrl: './consultActComponent.html',
    providers: [MessageService],
    styleUrls: ['./OrdersComponent.css'],
    animations: [
        trigger(
            'slideView',
            [
                state('true', style({ transform: 'translateX(100%)', opacity: 0 })),
                state('false', style({ transform: 'translateX(0)', opacity: 1 })),
                transition('0 => 1', animate('500ms', style({ transform: 'translateX(0)', 'opacity': 1 }))),
                transition('1 => 1', animate('500ms', style({ transform: 'translateX(100%)', 'opacity': 0 }))),
            ]),

        trigger('slideInOut', [
            transition(':enter', [
                style({ transform: 'translateX(100%)', opacity: 0 }),
                animate('600ms ease-in', style({ transform: 'translateX(0%)', 'opacity': 1 }))
            ]),

            transition(':leave', [
                style({ transform: 'translateX(0%)', opacity: 1 }),
                animate('0ms ease-in', style({ transform: 'translateX(100%)', 'opacity': 0 }))
            ])
        ])
    ]

})

export class consultActComponent implements OnInit, AfterViewInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    orderChanged: boolean = false;
    displayPopupForDeleteSignatory: boolean = false;
    displayPopupForDeleteCouncel: boolean = false;
    readyOnlyForUser :boolean = false;
    env: any = {};


    constructor(private sanitized: DomSanitizer) { }
    ngOnInit(): void {
        this.readyOnlyForUser=this.oneMonthResiliation();
        console.log(this.readyOnlyForUser);

        window.scrollTo(0, 0);
        let envQuery = this.vm.services.orderService.getEnvVariables();
        Promise.all([envQuery]).then(results => {
            this.env=results[0];
        });
    }

    get listDocument(): Document[] {
        return OrdersViewModelMaster.listDocument;
    }
    set listDocument(lisDocument: Document[]) {
        OrdersViewModelMaster.listDocument = lisDocument;
    }

    get listSignataire(): Signataire[] {
        return OrdersViewModelMaster.listSignataire;
    }
    get listAvocat(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }
    get currentActe(): Order {
        return OrdersViewModelMaster.currentActe;
    }
    showUserCouncelMenu() {
        this.showMenuCouncel = !this.showMenuCouncel
    }
    showUserSignatoryMenu() {
        this.showMenuSignatory = !this.showMenuSignatory
    }

    relancePourValidation() {
    }
    getFileSize(name: String): number {
        return (this.listDocument.filter(doc => doc.name == name)[0].size) / 1000000;
    }
    downloadAllDocument() {
        for (let file of OrdersViewModelMaster.listDocument as any) {
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    const linkSource = 'data:application/pdf;base64,' + results;
                    const downloadLink = document.createElement("a");
                    const fileName = file.name;
                    downloadLink.href = linkSource;
                    downloadLink.download = fileName;
                    downloadLink.click();
                }
            });
        }
    }

    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }

    downloadPDF(file: any) {
        if (this.currentActe.status != 'Signee') {
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    const linkSource = 'data:application/pdf;base64,' + results;
                    const downloadLink = document.createElement("a");
                    const fileName = file.name;
                    downloadLink.href = linkSource;
                    downloadLink.download = fileName;
                    downloadLink.click();
                }
            });
        }else {
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    const linkSource = 'data:application/pdf;base64,' + results;
                    const downloadLink = document.createElement("a");
                    const fileName = file.name;
                    downloadLink.href = linkSource;
                    downloadLink.download = fileName;
                    downloadLink.click();
                }
            });
        }
    }
    viewPdf(file: any) {
        if (this.currentActe.status != 'Signee') {
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let path = 'data:application/pdf;base64,' + results;
                    let pdfWindow = window.open("");
                    pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");
                }
            });
        }else {
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let path = 'data:application/pdf;base64,' + results;
                    let pdfWindow = window.open("");
                    pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");
                }
            });
        }
    }

    isAvocat(item): boolean {
        return this.listAvocat.some(av => av.actId == item.actId && av.name == item.name)
    }

    deleteSignatairePopup(){
        this.displayPopupForDeleteSignatory = true;
        console.log(this.displayPopupForDeleteSignatory);
    }

    deleteAvocatPopup(){
        console.log(this.displayPopupForDeleteCouncel)
        this.displayPopupForDeleteCouncel = true;
    }



    sendMailRelance(item) {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id.toString();
        if (item) {
            validatUdser["unitUser"] = item;
        }
        else {
            this.userConnected["unitUser"] = null;
        }
        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendRelanceToValidateAct(validatUdser);
        Promise.all([sendMailToAvocat]).then(results => {
            if(results[0] == 'email sent'){
                if(item){
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'Mail de relance de ' + item.name + ' pour validation envoyé avec succès', life: 4000 });
                    this.currentActe.lastResentDate = new Date().toLocaleString();

                }else{
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) de relance de l\'ensemble des validateurs envoyé(s) avec succès', life: 4000 });
                    this.currentActe.lastResentDate = new Date().toLocaleString();

                }
            }else{
                this.vm.services.messageService.add({ severity: 'error', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) de relance non transmis - merci de réessayer ultérieurement', life: 4000 });
            }
        });
    }

    sendMailSignRelance(item){
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id;
        if (item) {
            validatUdser["unitUser"] = item;
        }
        else {
            this.userConnected["unitUser"] = null;
        }
        let sendRelanceSignMail = this.vm.services.sendMailToValidate.sendRelanceToSignAct(validatUdser);
        Promise.all([sendRelanceSignMail]).then(results => {
            if(results[0] == 'email sent'){
                if(item){
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'Mail de relance de ' + item.name + ' pour signature envoyé avec succès', life: 4000 });
                    this.currentActe.lastResentDate = new Date().toLocaleString();

                }else{
                    this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) de relance des signataires envoyé(s) avec succès', life: 4000 });
                    this.currentActe.lastResentDate = new Date().toLocaleString();

                }
            }else{
                this.vm.services.messageService.add({ severity: 'error', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) de relance non transmis - merci de réessayer ultérieurement', life: 4000});
            }
        });
    }

    disabledCheckBox(item: any): boolean {
        return item.validator == true && item.mailSent == true && item.actId == this.currentActe.id;
    }

    disabledResent(item: any): boolean {
        return item.validator == true || item.mailSent == true || item.actId == this.currentActe.id;
    }

    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        this.orderChanged = true;
        return arr;
    }

    changeDocumentPosition() {
        for (let doc of this.listDocument) {
            doc.position = this.listDocument.indexOf(doc);
            this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == doc.actId && elm.name == doc.name));
            this.vm.listDocument.push(doc);
        }
        this.vm.services.documentService.postsDocumentsPositionAsync(this.listDocument).then(results => {
        });
    }

    upDocumentPosition(file: any) {

        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) - 1);
        
    }

    downDocumentPosition(file: any) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) + 1);
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

    setContactToValidator() {
        let userToSendMail: any[] = [];
        for (let avocat of this.listAvocat.filter(av => av.validator && av.validator == true)) {
            avocat.mailSent = true;
            userToSendMail.push(avocat);
        }
        for (let sig of this.listSignataire.filter(sig => sig.validator && sig.validator == true)) {
            sig.mailSent = true;
            userToSendMail.push(sig);
        }
        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendMailToValidateAct(userToSendMail);
        Promise.all([sendMailToAvocat]).then(results => {
            if (results[0] == 'email sent') {
                let changeUsertoValidator = this.vm.services.sendMailToValidate.changeUserToValidator(userToSendMail);
                this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) pour validation envoyé(s) avec succès', life: 4000 });
                Promise.all([changeUsertoValidator]).then(results => {
                });
                this.currentActe.status = "En cours de validation";
                this.displayConsultOrder = true;
                this.vm.displayActeRefused = false;
                this.currentActe.requestDate = moment().format('DD/MM/YYYY HH:mm:ss');
                let saveOrderQuery = this.vm.services.orderService.postAct(this.currentActe);
                Promise.all([saveOrderQuery]).then(results => {

                });
                this.displayCreateOrder = false;
                this.displayConsultOrder = false;
                this.displaySendTovalidation = false;
                this.vm.getData();
            }
            else {
                this.vm.services.messageService.add({ severity: 'error', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) non envoyé(s)', life: 4000 });
            }
        });
    }
  
    deleteSignataire(signataire: any) {
        this.vm.services.signataireService.deleteSignataire(signataire).then(() => {
            for (let av of this.listSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId) {
                    this.listSignataire = this.listSignataire.filter(a => a != av);
                }
            }
            for (let av of this.vm.allSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId) {
                    this.vm.allSignataire = this.vm.allSignataire.filter(a => a != av);
                }
            }
        });
    }

    signataireToModify(signataire: any): void {
        OrdersViewModelMaster.signataireToModify = signataire;
    }

    avocatToModify(avocat: any): void {
        OrdersViewModelMaster.avocatToModify = avocat;
    }

    deleteAvocat(avocat: any) {
        this.vm.services.avocatService.deleteAvocat(avocat).then(() => {
            for (let av of this.listAvocat) {
                if (av.name == avocat.name && av.email == avocat.email && av.actId == avocat.actId) {
                    this.listAvocat = this.listAvocat.filter(a => a != av);
                }
            }

            for (let av of this.vm.allAvocat) {
                if (av.name == avocat.name && av.email == avocat.email && av.actId == avocat.actId) {
                    this.vm.allAvocat = this.vm.allAvocat.filter(a => a != av);
                }
            }
        });
    }



    set listSignataire(listSignataire: Signataire[]) {
        OrdersViewModelMaster.listSignataire = listSignataire;
    }

    get canReSendToValidation(): boolean {
        return this.listAvocat.some(avocat => !avocat.mailSent && avocat.validator) || this.listSignataire.some(signataire => !signataire.mailSent && signataire.validator)
    }
    canSendToValidation(): boolean {
        return this.listAvocat.some(avocat => avocat.validator && avocat.validator == true) && this.listSignataire.some(signataire => signataire.validator && signataire.validator == true)
    }
    set listAvocat(value: Avocat[]) {
        OrdersViewModelMaster.listAvocat = value;
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
    set displayModalSignataire(displayModalSignataire: boolean) {
        OrdersViewModelMaster.displayModalSignataire = displayModalSignataire;
    }

    get displayModalSignataire(): boolean {
        return OrdersViewModelMaster.displayModalSignataire;
    }
    get displayModalAvocat(): boolean {
        return OrdersViewModelMaster.displayModalAvocat;
    }
    set displayModalAvocat(displayModalAvocat: boolean) {
        OrdersViewModelMaster.displayModalAvocat = displayModalAvocat;
    }

    dateSigneddOrRefused(item: any) {
        if(item.signedAt==null&& item.signatureComment==null && this.currentActe.receptionDate!=null && this.currentActe.status==='En cours de signature'){

            let deadLineEnv = this.env.deadLine;
            let deadLineEnvDate = moment(moment(this.currentActe.receptionDate, 'DD/MM/YYYY')).add(parseInt(deadLineEnv), 'days');
            let deadLineDate = (this.currentActe.expirationDate == null) ? deadLineEnvDate : moment(this.currentActe.expirationDate, "DD/MM/YYYY");
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> A signer avant le</span> " + deadLineDate.format('DD/MM/YYYY').slice(0, 10));
        }
        if (item.signedAt && item.signatureComment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> Signature refusée le</span> " + item.signedAt);
        }
        else if (item.signedAt && !item.signatureComment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:green'> Signé le</span> " + item.signedAt);
        }
        else {
            return "";
        }
    }

    dateValidatedOrRefused(item: any) {
        if(item.validator&& item.actValidated==null && this.currentActe.receptionDate!=null && this.currentActe.status==='En cours de validation'){

            let deadLineEnv = this.env.deadLine;
            let deadLineEnvDate = moment(moment(this.currentActe.receptionDate, 'DD/MM/YYYY')).add(parseInt(deadLineEnv), 'days');
            let deadLineDate = (this.currentActe.expirationDate == null) ? deadLineEnvDate : moment(this.currentActe.expirationDate, "DD/MM/YYYY");

            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> A valider avant le</span> " + deadLineDate.format('DD/MM/YYYY').slice(0, 10));
        }
        if (item.validator && !item.actValidated && item.comment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> Validation refusée le</span> " + item.validatedAt);
        }
        else if ((item.validator && item.actValidated && item.validatedAt)) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:green'> Validé le</span> " + item.validatedAt);
        }
        else {
            return "";
        }
    }

    get displayComments(): boolean {
        return this.listAvocat.some(avocat => avocat.comment && avocat.comment.length > 0) || this.listSignataire.some(signatire => signatire.comment && signatire.comment.length>0 );
    }

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.add-form-actions button').each(function(index, el) {
                $(el).blur();
            });
        });
    }
    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }

    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }
    oneMonthResiliation(){
        let dateDiff=moment().diff(moment(this.userConnected.resiliation,'DD/MM/YYYY'),'months',true);
        return dateDiff <=1 || this.userConnected.resiliation ===null;
    }
    testDeadLine(receptionDate, expirationDate, status){
        if(receptionDate!==null) {
            let deadLineEnv = this.env.deadLine;
            var deadLineDate = moment(moment(receptionDate, 'DD/MM/YYYY')).add(parseInt(deadLineEnv), 'days');

            if(expirationDate !== null) {
                deadLineDate = moment(expirationDate, "DD/MM/YYYY");
            }

            let today = moment(moment(), 'DD/MM/YYYY');
            if (status === 'En cours de signature' || status === 'En cours de validation') {
                if (deadLineDate.diff(today, 'days') < 0) {
                    return deadLineDate.format('DD/MM/YYYY').slice(0, 10) + '-délai dépassé';
                }
            }
        }

        return '';

    }
}