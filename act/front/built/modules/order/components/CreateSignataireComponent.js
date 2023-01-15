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
const signataire_1 = require("../../../shared/entities/signataire");
exports.fadeInOut = (name = 'fadeInOut', duration = 0.1) => core_1.trigger(name, [
    core_1.transition(':enter', [
        core_1.style({ opacity: 0 }),
        core_1.animate(`${duration}s ease-in-out`)
    ]),
    core_1.transition(':leave', [core_1.animate(`${duration}s ease-in-out`, core_1.style({ opacity: 0 }))])
]);
let CreateSignataireComponent = class CreateSignataireComponent {
    constructor(messageService) {
        this.messageService = messageService;
        this.listeSignataire = [];
        this.array = Array;
    }
    ngOnInit() {
        this.listeSignataire = new Array(0).fill({});
        let sig = new signataire_1.Signataire();
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify && OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify.name ? this.listeSignataire.push(OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify) : this.listeSignataire.push(sig);
        ;
        window.scrollTo(0, 0);
    }
    emptySignataireToModifiy() {
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = new signataire_1.Signataire();
    }
    createNewSignataire() {
        let sig = new signataire_1.Signataire();
        this.listeSignataire.push(sig);
    }
    assignSignatory(value, index) {
        this.listeSignataire[index] = value;
        this.displayDivForAutoComplete = false;
    }
    //Condition sur l'ajout
    saveSignataire() {
        for (let sig of this.listeSignataire) {
            if (!this.listSignataire.some((s => s.name == sig.name && s.email == sig.email) || (s => s.name == sig.name) || (s => s.email == sig.email))) {
                if (!sig.role) {
                    sig["role"] = "signatory";
                }
                sig.actId = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe.toString();
                let signatoryQuery = this.vm.services.signataireService.postSignataire(sig);
                Promise.all([signatoryQuery.catch(err => {
                        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = true;
                    this.messageService.add({ severity: 'error', summary: 'Ajout de signataire(s)', detail: 'Erreur lors de l\'ajout du signataire', life: 4000 });
                        console.log('err', err);
                        throw err;
                    })]).then(results => {
                    this.listSignataire.push(sig);
                    //if (!this.vm.allSignataire.some(s => s.name == s.name && s.email == s.email)) {
                    this.vm.allSignataire.push(sig);
                    //}
                    OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = new signataire_1.Signataire();
                    OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = false;
                });
            }
            else {
                this.messageService.add({ severity: 'error', summary: 'Ajout de signataire(s)', detail: sig.name + ' existe déjà', life: 4000 });
                OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = true;
            }
        }
    }
    modifySignataire() {
        for (let sig of this.listeSignataire) {
            if (this.listSignataire.some(s => s.name == sig.name)) {
                if (!sig.role) {
                    sig["role"] = "signatory";
                }
                sig.actId = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe.toString();
                let signatoryQuery = this.vm.services.signataireService.modifySignataire(sig);
                Promise.all([signatoryQuery]).then(results => {
                });
            }
        }
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = new signataire_1.Signataire();
    }
    deleteSignataire(signataire) {
        this.listeSignataire = this.listeSignataire.filter(s => s.name != signataire.name);
        this.listSignataire = OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire.filter(s => s.name != signataire.name);
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = new signataire_1.Signataire();
        if (this.listeSignataire.length == 0) {
            OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = false;
        }
        this.vm.services.signataireService.deleteSignataire(signataire).then(() => {
        });
    }
    get listSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire;
    }
    set listSignataire(listSignataire) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire = listSignataire;
    }
    get allSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.allSignataire;
    }
    get signataireToModify() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify;
    }
    get displayModalSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire;
    }
    set displayModalSignataire(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = value;
    }
    testSignatoryRoleSelected() {
        if (this.listeSignataire.length == 0 || this.listeSignataire.some(s => !s.role)) {
            return false;
        }
        else {
            return true;
        }
    }
};
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], CreateSignataireComponent.prototype, "vm", void 0);
CreateSignataireComponent = __decorate([
    core_1.Component({
        selector: 'CreateSignataireComponent',
        templateUrl: './CreateSignataireComponent.html',
        providers: [api_1.MessageService],
        styleUrls: ['./OrdersComponent.css'],
        animations: [
            exports.fadeInOut('fadeInOut-3', 2)
        ]
    }),
    __metadata("design:paramtypes", [api_1.MessageService])
], CreateSignataireComponent);
exports.CreateSignataireComponent = CreateSignataireComponent;
