import { Component, OnInit, trigger, transition, style, animate, state, Input, HostListener, ViewChild } from '@angular/core';
import { MessageService } from 'primeng/api';
import { OrdersViewModelMaster } from '../view-models/bases/OrdersViewModelMaster';
import { Order } from '../../../shared/entities/order';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { DomSanitizer } from '@angular/platform-browser';
import { Signataire } from '../../../shared/entities/signataire';
import { Document } from "../../../shared/entities/document";
import { Avocat } from "../../../shared/entities/avocat";
import { User } from "../../../shared/entities/user";

declare const $: any;

@Component({
    selector: 'consultActForAvocatComponent',
    templateUrl: './consultActForAvocatComponent.html',
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

export class consultActForAvocatComponent implements OnInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    randomString : string;

    constructor(private sanitized: DomSanitizer) { }
    ngOnInit(): void {
        window.scrollTo(0, 0);
        debugger;
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
            if (this.currentActe.status != 'Signee'){
                return '/documents/'+this.currentActe.id +'/' + file.name.replace(/\s/g, '') + '.pdf';
            }else{
                let downloadSign = this.vm.services.orderService.downloadSigne(this.currentActe.id);
                Promise.all([downloadSign]).then(results => {
                    this.randomString = results[0][1];
                    console.log(this.randomString)
                    let a = document.createElement("a");
                    a.href = '/documents/' + this.currentActe.folderNumber+this.randomString + 'ForSigning.pdf';
                    a.download = this.currentActe.folderNumber;
                    a.click();
                    let deleteSign = this.vm.services.orderService.deleteSigne(this.currentActe,this.randomString);
                });
            }
        }
    }
    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }
    downloadPDF(file: any) {
        if (this.currentActe.status != 'Signee'){
            return '/documents/'+this.currentActe.id +'/' + file.name.replace(/\s/g, '') + '.pdf';
        }else{
            let downloadSign = this.vm.services.orderService.downloadSigne(this.currentActe.id);
            Promise.all([downloadSign]).then(results => {
                this.randomString = results[0][1];
                console.log(this.randomString)
                let a = document.createElement("a");
                a.href = '/documents/' + this.currentActe.folderNumber+this.randomString + 'ForSigning.pdf';
                a.download = this.currentActe.folderNumber;
                a.click();
                let deleteSign = this.vm.services.orderService.deleteSigne(this.currentActe , this.randomString);


            });

        }
    }
    viewPdf(file: any) {
        let pdfWindow = window.open("");
        let downloadSign = this.vm.services.orderService.downloadSigne(this.currentActe.id);
        if (this.currentActe.status != 'Signee') {
            pdfWindow.document.write("<iframe width='100%' height='100%' src='/documents/" + this.currentActe.id + '/' + file.name.replace(/\s/g, '') + ".pdf'></iframe>");
        } else {
            Promise.all([downloadSign]).then(results => {
                this.randomString = results[0][1];
                pdfWindow.document.write("<iframe id='test' width='100%' height='100%' src='/documents/" + this.currentActe.folderNumber +this.randomString+ "ForSigning.pdf' [attr.title]='ffff.pdf'></iframe>");
                let iframe = pdfWindow.document.getElementById('test');
                let iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iframeDoc.readyState == 'complete') {
                    setTimeout(() =>  this.vm.services.orderService.deleteSigne(this.currentActe , this.randomString), 500);
                }
            });
        }

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

    dateValidatedOrRefused(item: any) {
        if (item.validator && !item.actValidated && item.comment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> Validation réfusé le</span> " + item.validatedAt);
        }
        else if ((item.validator && item.actValidated)) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:green'> Validé le</span> " + item.validatedAt);
        }
        else {
            return "";
        }
    }

    dateSigneddOrRefused(item: any) {
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
    get displayComments(): boolean {
        return this.listAvocat.some(avocat => avocat.comment && avocat.comment.length > 0) || this.listSignataire.some(signatire => signatire.comment && signatire.comment.length>0 );
    }
}