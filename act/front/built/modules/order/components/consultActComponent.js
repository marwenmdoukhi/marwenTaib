"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
const core_1 = require("@angular/core");
const api_1 = require("primeng/api");
const OrdersViewModelMaster_1 = require("../view-models/bases/OrdersViewModelMaster");
let consultActComponent = class consultActComponent {
    constructor() {
        this.showMenuCouncel = false;
        this.showMenuSignatory = false;
        this.showMenuAction = false;
    }
    ngOnInit() {
        window.scrollTo(0, 0);
    }
    get listDocument() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument;
    }
    get listSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire;
    }
    get listAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat;
    }
    get currentActe() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.currentActe;
    }
    showUserCouncelMenu() {
        this.showMenuCouncel = !this.showMenuCouncel;
    }
    showUserSignatoryMenu() {
        this.showMenuSignatory = !this.showMenuSignatory;
    }
    showMenuAct() {
        this.showMenuAction = !this.showMenuAction;
    }
    /*
 Calcule de la taille de fichier
 */
    getFileSize(name) {
        return (this.listDocument.filter(doc => doc.name == name)[0].size) / 1000000;
    }
    downloadAllDocument() {
        for (let file of OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument) {
            let link = document.createElement('a');
            link.href = '/documents/' + file.name.replace(/\s/g, '') + '.pdf';
            link.download = file.name.replace(/\s/g, '');
            link.click();
        }
    }
    get userConnected() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.userConnected;
    }
    /*
     * Downloader un pdf
     */
    downloadPDF(file) {
        return '/documents/' + file.name.replace(/\s/g, '') + '.pdf';
    }
    viewPdf(file) {
        let pdfWindow = window.open("");
        pdfWindow.document.write("<iframe width='100%' height='100%' src='/documents/" + file.name.replace(/\s/g, '') + ".pdf'></iframe>");
    }
    getStatut(value) {
        switch (value) {
            case "Cree": {
                return "Créé";
            }
            case "Valide": {
                return "Validé";
            }
            case "Validation refusee": {
                return "Validation refusée";
            }
            case "Signe": {
                return "Signé";
            }
            case "Signe refusee": {
                return "Signature refusée";
            }
            default: {
                return value;
            }
        }
    }
    setContactToValidator() {
        let test1 = this.listAvocat;
        let test2 = this.listSignataire;
    }
    canSendToValidation() {
        return this.listAvocat.some(avocat => avocat.validator && avocat.validator == true) && this.listSignataire.some(signataire => signataire.validator && signataire.validator == true);
    }
    set displayCreateOrder(displayCreateOrder) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayCreateOrder = displayCreateOrder;
    }
    get displayCreateOrder() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayCreateOrder;
    }
    set displayConsultOrder(displayConsultOrder) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayConsultOrder = displayConsultOrder;
    }
    get displayConsultOrder() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayConsultOrder;
    }
    set displaySendTovalidation(displaySendTovalidation) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displaySendTovalidation = displaySendTovalidation;
    }
    get displaySendTovalidation() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displaySendTovalidation;
    }
    set displayModalSignataire(displayModalSignataire) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = displayModalSignataire;
    }
    get displayModalSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire;
    }
    get displayModalAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat;
    }
    set displayModalAvocat(displayModalAvocat) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = displayModalAvocat;
    }
};
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], consultActComponent.prototype, "vm", void 0);
consultActComponent = __decorate([
    core_1.Component({
        selector: 'consultActComponent',
        templateUrl: './consultActComponent.html',
        providers: [api_1.MessageService],
        styleUrls: ['./OrdersComponent.css'],
        animations: [
            core_1.trigger('slideView', [
                core_1.state('true', core_1.style({ transform: 'translateX(100%)', opacity: 0 })),
                core_1.state('false', core_1.style({ transform: 'translateX(0)', opacity: 1 })),
                core_1.transition('0 => 1', core_1.animate('500ms', core_1.style({ transform: 'translateX(0)', 'opacity': 1 }))),
                core_1.transition('1 => 1', core_1.animate('500ms', core_1.style({ transform: 'translateX(100%)', 'opacity': 0 }))),
            ]),
            core_1.trigger('slideInOut', [
                core_1.transition(':enter', [
                    core_1.style({ transform: 'translateX(100%)', opacity: 0 }),
                    core_1.animate('600ms ease-in', core_1.style({ transform: 'translateX(0%)', 'opacity': 1 }))
                ]),
                core_1.transition(':leave', [
                    core_1.style({ transform: 'translateX(0%)', opacity: 1 }),
                    core_1.animate('0ms ease-in', core_1.style({ transform: 'translateX(100%)', 'opacity': 0 }))
                ])
            ])
        ]
    }),
    __metadata("design:paramtypes", [])
], consultActComponent);
exports.consultActComponent = consultActComponent;
