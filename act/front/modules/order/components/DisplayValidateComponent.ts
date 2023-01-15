import { Component, OnInit, AfterViewInit, Input, trigger, transition, style, animate, state } from '@angular/core';
import { MessageService } from 'primeng/api';
import { OrdersViewModelMaster } from '../view-models/bases/OrdersViewModelMaster';
import { Order } from '../../../shared/entities/order';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { DomSanitizer } from '@angular/platform-browser';
import { Document } from "../../../shared/entities/document";
import { User } from "../../../shared/entities/user";
import * as moment from "moment";


declare const $: any;

@Component({
    selector: 'display-component-validate',
    templateUrl: './DisplayValidateComponent.html',
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

export class DisplayComponentValidate implements OnInit, AfterViewInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    showMenuAction: boolean = false;
    acceptCondition: boolean = false;
    commentRefuseAct: string = "";
    validatedClicked: boolean = false;
    validateCompleted: boolean = false;
    constructor(private sanitized: DomSanitizer, private messageService: MessageService) { }
    ngOnInit(): void {
        window.scrollTo(0, 0);
    }

    get listDocument(): Document[] {
        return OrdersViewModelMaster.listDocument;
    }

    get currentActe(): Order {
        return OrdersViewModelMaster.currentActe;
    }

    refuseAct() {
        if (this.acceptCondition) {
            if (!this.commentRefuseAct || this.commentRefuseAct.length == 0) {
                this.messageService.add({ severity: 'error', summary: 'Validation', detail: 'Veuillez ajouter un commentaire de refus', life: 4000 });
            }
            else {
                debugger;
                let validatUdser = this.userConnected;
                validatUdser["actId"] = this.currentActe.id;
                validatUdser.comment = this.commentRefuseAct;
                this.currentActe.requestDate = moment().format('DD/MM/YYYY HH:mm:ss');
                let orderQuery = this.vm.services.orderService.refuserActe(validatUdser);
                Promise.all([orderQuery]).then(results => {
                    debugger;
                    this.vm.listOrder = this.vm.listOrder.filter(act => act.id != results[0].id);
                    results[0].actValidated = 'false';
                    this.vm.listOrder.push(results[0]);
                    this.sendRefuseValidation();
                });
            }
        }
        else {
            this.validatedClicked = true;
        }
    }

    get activatRefuseButton(): boolean {
        return !this.acceptCondition || (this.acceptCondition && !this.userConnected.comment && this.userConnected.comment.length == 0);
    }

    validateActe() {
        if (this.acceptCondition) {
            let validatUdser = this.userConnected;
            validatUdser["actId"] = this.currentActe.id;
            let orderQuery = this.vm.services.orderService.validateActe(validatUdser);
            Promise.all([orderQuery]).then(results => {
                this.vm.listOrder = this.vm.listOrder.filter(act => act.id != results[0].id);
                results[0].actValidated = 'true';
                this.vm.listOrder.push(results[0]);
                this.validateCompleted = true;
            });
        }
        else {
            this.validatedClicked = true;
        }
    }

    sendValidator() {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id;
        let sendValidatorNotification = this.vm.services.sendMailToValidate.sendValidatorNotifcation(validatUdser);
        Promise.all([sendValidatorNotification]).then(results => {
        });
    }

    sendRefuseValidation(){
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id;
        let sendRefuseValidatorNotification = this.vm.services.sendMailToValidate.sendRefuseValidatorNotifcation(validatUdser);
        Promise.all([sendRefuseValidatorNotification]).then(results => {
        });
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
            let fileQuery = this.vm.services.orderService.getBase64FileFromServer("/documents/" + this.listDocument[0]['actId'] + '/' + file.name.replace(/\s/g, '') + 'ForSigning.pdf');
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
            let fileQuery = this.vm.services.orderService.getBase64FileFromServer("/documents/" + this.listDocument[0]['actId'] + '/' + file.name.replace(/\s/g, '') + 'ForSigning.pdf');
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let path = 'data:application/pdf;base64,' + results;
                    let pdfWindow = window.open("");
                    pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");
                }
            });
        }
    }

    setContactToValidator() {

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

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.signature-action-button button').each(function(index, el) {
                $(el).blur();
            });
        });
    }
}