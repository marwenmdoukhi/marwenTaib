import { Component, OnInit, trigger, transition, style, animate, state, Input } from '@angular/core';
import { MessageService } from 'primeng/api';
import { OrdersViewModelMaster } from '../view-models/bases/OrdersViewModelMaster';
import { Order } from '../../../shared/entities/order';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { Signataire } from '../../../shared/entities/signataire';
import { Document } from "../../../shared/entities/document";
import { Avocat } from "../../../shared/entities/avocat";
import { User } from "../../../shared/entities/user";
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
    selector: 'displayComments',
    templateUrl: './displayComments.html',
    providers: [MessageService],
    styleUrls: ['./OrdersComponent.css'],
    animations: [
        fadeInOut('fadeInOut-3', 2)
    ]

})

export class DisplayComments implements OnInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    constructor() { }

    ngOnInit(): void {
        window.scrollTo(0, 0);
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

    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
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

    get displayComments(): boolean {
        return this.listAvocat.some(avocat => avocat.comment && avocat.comment.length > 0) || this.listSignataire.some(signatire => signatire.comment && signatire.comment.length > 0);
    }

    get commentsValidationSignatory(): Signataire[] {
        return this.listSignataire.filter(signatire => signatire.comment && signatire.comment.length > 0); 
    }

    get commentsValidationAvocat(): Avocat[] {
        return this.listAvocat.filter(avocat => avocat.comment && avocat.comment.length > 0);
    }

    get commentsSignatureSignatory(): Signataire[] {
         return this.listSignataire.filter(signatire => signatire.signatureComment && signatire.signatureComment.length > 0); 
    }
    changeComponent() {
        this.vm.displayComments = false; this.vm.displayActeRefused = false;
    }
}