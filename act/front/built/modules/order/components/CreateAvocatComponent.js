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
const OrdersViewModelMaster_1 = require("../view-models/bases/OrdersViewModelMaster");
const avocat_1 = require("../../../shared/entities/avocat");
const api_1 = require("primeng/api");
exports.fadeInOut = (name = 'fadeInOut', duration = 0.1) => core_1.trigger(name, [
    core_1.transition(':enter', [
        core_1.style({ opacity: 0 }),
        core_1.animate(`${duration}s ease-in-out`)
    ]),
    core_1.transition(':leave', [core_1.animate(`${duration}s ease-in-out`, core_1.style({ opacity: 0 }))])
]);
let CreateAvocatComponent = class CreateAvocatComponent {
    constructor(messageService) {
        this.messageService = messageService;
        this.listeAvocat = [];
        this.array = Array;
        this.active = '';
        //this.vm = new OrdersViewModelMaster(orderService, avocatService, documentService, signataireService);
    }
    ngOnInit() {
        this.listeAvocat = new Array(0).fill({});
        OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify && OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify.name ? this.listeAvocat.push(OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify) : this.listeAvocat.push(new avocat_1.Avocat());
        ;
        window.scrollTo(0, 0);
    }
    emptyAvocatToModifiy() {
        OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify = new avocat_1.Avocat();
    }
    createNewAvocat() {
        this.listeAvocat.push(new avocat_1.Avocat());
    }
    assignAvocat(value, index) {
        this.listeAvocat[index] = value;
        this.displayDivForAutoComplete = false;
    }
    get displayModalAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat;
    }
    set displayModalAvocat(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = value;
    }
    saveAvocat() {
        for (let avocat of this.listeAvocat) {
            if (!this.listAvocat.some((s => s.name == avocat.name && s.email == avocat.email) || (s => s.name == avocat.name) || (s => s.email == avocat.email))) {
                avocat.birthDate = null;
                avocat.birthPlace = null;
                avocat.role = "counsel";
                avocat.enterpriseName = null;
                avocat.actId = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe.toString();
                let avocatyQuery = this.vm.services.avocatService.postSignataire(avocat);
                Promise.all([avocatyQuery.catch(err => {
                        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = true;
                    this.messageService.add({ severity: 'error', summary: 'Ajout de l\'avocat au dossier', detail: 'Erreur lors de l\'ajout de l\'avocat ', life: 4000 });
                        console.log('err', err);
                        throw err;
                    })]).then(results => {
                    OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat.push(avocat);
                    OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify = new avocat_1.Avocat();
                    OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = false;
                    //if (!this.vm.allAvocat.some(s => s.name == s.name && s.email == s.email)) {
                    this.vm.allAvocat.push(avocat);
                    //}
                });
            }
            else {
                this.messageService.add({ severity: 'error', summary: 'Ajout de l\'avocat au dossier', detail: avocat.name.toUpperCase() + ' existe déjà',life: 4000 });
                OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = true;
            }
        }
    }
    modifyAvocat() {
        for (let sig of this.listeAvocat) {
            if (OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat.some(s => s.name == sig.name)) {
                if (!sig.role) {
                    sig["role"] = "counsel";
                }
                sig.actId = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe.toString();
                let signatoryQuery = this.vm.services.signataireService.modifySignataire(sig);
                Promise.all([signatoryQuery]).then(results => {
                    OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify = new avocat_1.Avocat();
                });
            }
        }
    }
    deleteAvocat(avocat) {
        this.vm.services.avocatService.deleteAvocat(avocat).then(() => {
            this.listeAvocat = this.listeAvocat.filter(s => s.name != avocat.name);
            OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat = OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat.filter(s => s.name != avocat.name);
            OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify = new avocat_1.Avocat();
            if (this.listeAvocat.length == 0) {
                OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = false;
            }
        });
    }
    get avocatToModify() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify;
    }
    get listAvocat() {
        return this.removeDuplicates(OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat, "name");
    }
    get allAvocat() {
        return this.removeDuplicates(OrdersViewModelMaster_1.OrdersViewModelMaster.allAvocat, "name");
    }
    removeDuplicates(myArr, prop) {
        return myArr.filter((obj, pos, arr) => {
            return arr.map((mapObj) => mapObj[prop]).indexOf(obj[prop]) === pos;
        });
    }
};
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], CreateAvocatComponent.prototype, "vm", void 0);
CreateAvocatComponent = __decorate([
    core_1.Component({
        selector: 'createAvocat',
        templateUrl: './CreateAvocatComponent.html',
        styleUrls: ['./OrdersComponent.css'],
        animations: [
            exports.fadeInOut('fadeInOut-3', 2)
        ]
    }),
    __metadata("design:paramtypes", [api_1.MessageService])
], CreateAvocatComponent);
exports.CreateAvocatComponent = CreateAvocatComponent;
