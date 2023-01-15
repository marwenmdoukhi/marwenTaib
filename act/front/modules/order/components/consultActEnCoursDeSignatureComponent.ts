import {AfterViewInit, animate, Component, Input, OnInit, state, style, transition, trigger} from '@angular/core';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {Signataire} from '../../../shared/entities/signataire';
import {Document} from "../../../shared/entities/document";
import {Avocat} from "../../../shared/entities/avocat";
import {User} from "../../../shared/entities/user";
import * as moment from "moment";

declare const $: any;

@Component({
    selector: 'consultActEnCoursDeSignatureComponent',
    templateUrl: './consultActEnCoursDeSignatureComponent.html',
    providers: [MessageService],
    styleUrls: ['./OrdersComponent.css'],
    animations: [
        trigger(
            'slideView',
            [
                state('true', style({transform: 'translateX(100%)', opacity: 0})),
                state('false', style({transform: 'translateX(0)', opacity: 1})),
                transition('0 => 1', animate('500ms', style({transform: 'translateX(0)', 'opacity': 1}))),
                transition('1 => 1', animate('500ms', style({transform: 'translateX(100%)', 'opacity': 0}))),
            ]),

        trigger('slideInOut', [
            transition(':enter', [
                style({ transform: 'translateX(100%)', opacity: 0 }),
                animate('600ms ease-in', style({ transform: 'translateX(0%)', 'opacity': 1 }))
            ]),

            transition(':leave', [
                style({transform: 'translateX(0%)', opacity: 1}),
                animate('0ms ease-in', style({transform: 'translateX(100%)', 'opacity': 0}))
            ])
        ])
    ]

})

export class consultActEnCoursDeSignatureComponent implements OnInit, AfterViewInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    readyOnlyForUser: boolean = false;

    constructor() {
    }

    ngOnInit(): void {
        window.scrollTo(0, 0);
        this.readyOnlyForUser = this.oneMonthResiliation();
    }
    get listDocument(): Document[] {
        return OrdersViewModelMaster.listDocument;
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
            let link = document.createElement('a');
            link.href = '/documents/'+this.listDocument[0]['actId']+'/' + file.name.replace(/\s/g, '') + '.pdf';
            link.download = file.name.replace(/\s/g, '');
            link.click();
        }
    }
    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }
    downloadPDF(file: any): string {
        return '/documents/'+this.listDocument[0]['actId']+'/' + file.name.replace(/\s/g, '') + '.pdf';
    }
    viewPdf(file: any) {
        let pdfWindow = window.open("");
        pdfWindow.document.write("<meta http-equiv='X-Frame-Options' content='sameorigin'><iframe width='100%' height='100%' src='/documents/"+this.listDocument[0]['actId']+'/' + file.name.replace(/\s/g, '') + ".pdf></iframe>")
    }


    sendMailRelance(itemMail: string) {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id;
        if (itemMail) {
            validatUdser["unitUser"] = itemMail;
        }
        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendRelanceToValidateAct(validatUdser);
        Promise.all([sendMailToAvocat]).then(results => {
            this.currentActe.lastResentDate = new Date().toLocaleString();
        });
    }

    disabledCheckBox(item: any): boolean {
        return item.validator == true && item.mailSent == true && item.actId == this.currentActe.id;
    }

    disabledResent(item: any): boolean {
        return item.validator == true || item.mailSent == true || item.actId == this.currentActe.id;
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
                this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) envoyé(s) avec succès', life: 4000 });
                Promise.all([changeUsertoValidator]).then(results => {
                });
                this.currentActe.status = "En cours de validation";
                let saveOrderQuery = this.vm.services.orderService.postAct(this.currentActe);
                Promise.all([saveOrderQuery]).then(results => {
                });
                this.displayCreateOrder = false;
                this.displayConsultOrder = false;
                this.displaySendTovalidation = false;
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
    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }
    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function () {
            $('.consultAES button').each(function (index, el) {
                $(el).blur();
                console.log(el)
            });
        });
    }

    oneMonthResiliation() {
        let dateDiff = moment().diff(moment(this.userConnected.resiliation, 'DD/MM/YYYY'), 'months', true);
        return dateDiff <= 1 || this.userConnected.resiliation === null;
    }
}