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
const table_1 = require("primeng/table");
const OrdersViewModelMaster_1 = require("../view-models/bases/OrdersViewModelMaster");
const order_1 = require("../../../shared/entities/order");
const moment = require("moment");
exports.fadeInOut = (name = 'fadeInOut', duration = 0.1) => core_1.trigger(name, [
    core_1.transition(':enter', [
        core_1.style({ opacity: 0 }),
        core_1.animate(`${duration}s ease-in-out`)
    ]),
    core_1.transition(':leave', [core_1.animate(`${duration}s ease-in-out`, core_1.style({ opacity: 0 }))])
]);
let OrdersComponent = class OrdersComponent {
    constructor() {
        this.filterDateCreation = false;
        this.filterDateSigning = false;
        this.choices = [
            { label: 'Filtre par date', value: null },
            { label: 'Date de création', value: 1 },
            { label: 'Date de signature', value: 2 },
        ];
    }
    test(event) {
        if (event == 1) {
            this.filterDateCreation = true;
            this.filterDateSigning = false;
        }
        else if (event == 2) {
            this.filterDateCreation = false;
            this.filterDateSigning = true;
        }
        else {
            this.filterDateCreation = false;
            this.filterDateSigning = false;
        }
    }
    ngOnInit() {
    }
    ngAfterViewInit() {
        window.scrollTo(0, 0);
        //OrdersViewModelMaster.reloadComplete = false;
        OrdersViewModelMaster_1.OrdersViewModelMaster.idActe = null;
        this.vm.choosenStatuts = null;
        OrdersViewModelMaster_1.OrdersViewModelMaster.displaySendTovalidation = false;
        if (this.dataTable) {
            this.dataTable.filterConstraints['requestDateFilter'] = (value, filter) => {
                let v = moment(value, 'DD/MM/YYYY').format("MM/DD/YYYY");
                let fmin = moment(filter[0], 'MM/DD/YYYY').format("MM/DD/YYYY");
                let fmax = moment(filter[1], 'MM/DD/YYYY').format("MM/DD/YYYY");
                if (fmax != "Invalid date") {
                    return v >= fmin && v <= fmax;
                }
                else {
                    return v == fmin;
                }
            };
            this.dataTable.filterConstraints['signingDateFilter'] = (value, filter) => {
                let v = moment(value, 'DD/MM/YYYY').format("MM/DD/YYYY");
                let fmin = moment(filter[0], 'MM/DD/YYYY').format("MM/DD/YYYY");
                let fmax = moment(filter[1], 'MM/DD/YYYY').format("MM/DD/YYYY");
                if (fmax != "Invalid date") {
                    return v >= fmin && v <= fmax;
                }
                else {
                    return v == fmin;
                }
            };
        }
    }
    onChangeFiltre(filtre) {
        if (filtre == "1") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = 1;
            // calls the endpoint
            this.dataTable.sortField = 'requestDate';
        }
        else if (filtre == "0") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = -1;
            this.dataTable.sortField = 'requestDate';
        }
        else if (filtre == "3") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = -1;
            this.dataTable.sortField = 'signingDate';
        }
        else if (filtre == "2") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = -1;
            this.dataTable.sortField = 'signingDate';
        }
        else if (filtre == "5") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = -1;
            this.dataTable.sortField = 'folderName';
        }
        else if (filtre == "4") {
            this.dataTable.lazy = false;
            this.dataTable.reset();
            this.dataTable._sortOrder = 1;
            this.dataTable.sortField = 'folderName';
        }
    }
    isEmpty(event) {
        if (event.filteredValue.length == 0) {
            this.noRecordMsgFlag = true;
        }
        else {
            this.noRecordMsgFlag = false;
        }
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
            case "Signature refusee": {
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
    styleObject(col) {
        if (col.header == 'statut') {
            return { 'width': col.width, 'text-align': 'center' };
        }
        return { 'width': col.width };
    }
    assignActeModel(item) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.activeIndex = 3;
        //passer en mode Modification
        OrdersViewModelMaster_1.OrdersViewModelMaster.mode = 1;
        OrdersViewModelMaster_1.OrdersViewModelMaster.currentActe = item;
        OrdersViewModelMaster_1.OrdersViewModelMaster.idActe = item.id;
        OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat = this.vm.allAvocat.filter(avocat => avocat.actId == item.id.toString());
        OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire = this.vm.allSignataire.filter(signataire => signataire.actId == item.id.toString());
        OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument = this.vm.listDocument.filter(doc => doc.actId == item.id);
        if (item.status == "En Projet" || item.status == "Cree" || item.status == "Validation refusee" || item.status == "Signature refusee") {
            this.displayCreateOrder = true;
        }
        else {
            this.displayConsultOrder = true;
        }
    }
    create() {
        OrdersViewModelMaster_1.OrdersViewModelMaster.currentActe = new order_1.Order();
        OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument = [];
        OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat = [];
        OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire = [];
        OrdersViewModelMaster_1.OrdersViewModelMaster.idActe = null;
        this.activeIndex = 0;
        this.displayCreateOrder = true;
    }
    get activeIndex() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.activeIndex;
    }
    set activeIndex(activeIndex) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.activeIndex = activeIndex;
    }
    set reloadComplete(reloadComplete) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.reloadComplete = reloadComplete;
    }
    get reloadComplete() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.reloadComplete;
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
    get displayModalSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire;
    }
    get displayModalAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat;
    }
};
__decorate([
    core_1.ViewChild('dataTable'),
    __metadata("design:type", table_1.Table)
], OrdersComponent.prototype, "dataTable", void 0);
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], OrdersComponent.prototype, "vm", void 0);
OrdersComponent = __decorate([
    core_1.Component({
        selector: 'orders',
        templateUrl: './OrdersComponent.html',
        styleUrls: ['./OrdersComponent.css'],
        animations: [
            exports.fadeInOut('fadeInOut-3', 3)
        ]
    }),
    __metadata("design:paramtypes", [])
], OrdersComponent);
exports.OrdersComponent = OrdersComponent;
