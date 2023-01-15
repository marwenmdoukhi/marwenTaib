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
var OrdersViewModelMaster_1;
Object.defineProperty(exports, "__esModule", { value: true });
const core_1 = require("@angular/core");
const Column_1 = require("../../../../shared/models/Column");
const order_1 = require("../../../../shared/entities/order");
const user_1 = require("../../../../shared/entities/user");
const BaseViewModel_1 = require("./BaseViewModel");
const SignatureServiceInjector_1 = require("../../SignatureServiceInjector");
/**
 * View model de l'app createOrder
 */
let OrdersViewModelMaster = OrdersViewModelMaster_1 = class OrdersViewModelMaster extends BaseViewModel_1.BaseViewModel {
    constructor(services) {
        super(services);
        this.statuts = [];
        this.documentsModel = [];
        this.signatairesModel = [];
        this.avocatModel = [];
        this.listDocument = [];
        this.userConnected = new user_1.User();
        this.allAvocat = [];
        this.getData();
        this.columns = [
            //isFrozen,isSortableDisabled, isReorderableDisabled, header, colkey,width,colspan.
            new Column_1.Column(false, true, false, 'Nom de l\'acte / N° de l\'acte', 'folderName', '20px', 3),
            new Column_1.Column(false, true, false, 'Nom du dossier', 'name', '20px', 3),
            new Column_1.Column(false, true, false, 'Date de la dernière modification', 'requestDate', '20px', 3),
            new Column_1.Column(false, true, false, 'Date de réception', 'signingDate', '20px', 3),
            new Column_1.Column(false, true, false, 'statut', 'status', '20px', 3),
        ];
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
        this.statuts.push({ label: 'En Projet', value: 'En Projet' });
        this.statuts.push({ label: 'Créé', value: 'Cree' });
        this.statuts.push({ label: 'En cours de validation', value: 'En cours de validation' });
        this.statuts.push({ label: 'Validé', value: 'Valide' });
        this.statuts.push({ label: 'Validation refusée', value: 'Validation refusee' });
        this.statuts.push({ label: 'En cours de signature', value: 'En cours de signature' });
        this.statuts.push({ label: 'Signé', value: 'Signe' });
        this.statuts.push({ label: 'Signature refusée', value: 'Signature refusee' });
        this.statuts.push({ label: 'Abandonné', value: 'Abandonne' });
    }
    static get currentActe() {
        return OrdersViewModelMaster_1._currentActe;
    }
    static set currentActe(currentActe) {
        OrdersViewModelMaster_1._currentActe = currentActe;
    }
    static get activeIndex() {
        return OrdersViewModelMaster_1._activeIndex;
    }
    static set activeIndex(activeIndex) {
        OrdersViewModelMaster_1._activeIndex = activeIndex;
    }
    static get reloadComplete() {
        return OrdersViewModelMaster_1._reloadComplete;
    }
    static set reloadComplete(reloadComplete) {
        OrdersViewModelMaster_1._reloadComplete = reloadComplete;
    }
    static get mode() {
        return OrdersViewModelMaster_1._mode;
    }
    static set mode(mode) {
        OrdersViewModelMaster_1._mode = mode;
    }
    static get avocatToModify() {
        return OrdersViewModelMaster_1._avocatToModify;
    }
    static set avocatToModify(avocatToModify) {
        OrdersViewModelMaster_1._avocatToModify = avocatToModify;
    }
    static get signataireToModify() {
        return OrdersViewModelMaster_1._signataireToModify;
    }
    static set signataireToModify(signataireToModify) {
        OrdersViewModelMaster_1._signataireToModify = signataireToModify;
    }
    static get displayCreateOrder() {
        return OrdersViewModelMaster_1._displayCreateOrder;
    }
    static set displayCreateOrder(displayCreateOrder) {
        OrdersViewModelMaster_1._displayCreateOrder = displayCreateOrder;
    }
    static get displayConsultOrder() {
        return OrdersViewModelMaster_1._displayConsultOrder;
    }
    static set displayConsultOrder(displayConsultOrder) {
        OrdersViewModelMaster_1._displayConsultOrder = displayConsultOrder;
    }
    static get displayModalSignataire() {
        return OrdersViewModelMaster_1._displayModalSignataire;
    }
    static set displayModalSignataire(displayModalSignataire) {
        OrdersViewModelMaster_1._displayModalSignataire = displayModalSignataire;
    }
    static get displayModalAvocat() {
        return OrdersViewModelMaster_1._displayModalAvocat;
    }
    static set displayModalAvocat(displayModalAvocat) {
        OrdersViewModelMaster_1._displayModalAvocat = displayModalAvocat;
    }
    /**
     * Retourne les Signataire
     *
     * @returns [[number]].
     */
    static get allAvocat() {
        return OrdersViewModelMaster_1._allAvocat;
    }
    /**
     * Met à jour les documents
     *
     * @param [[listDocument: Signataire[]]].
     */
    static set allAvocat(allAvocat) {
        OrdersViewModelMaster_1._allAvocat = allAvocat;
    }
    /**
     * Retourne les Signataire
     *
     * @returns [[number]].
     */
    static get listAvocat() {
        return OrdersViewModelMaster_1._listAvocat;
    }
    /**
     * Met à jour les documents
     *
     * @param [[listDocument: Signataire[]]].
     */
    static set listAvocat(listAvocat) {
        OrdersViewModelMaster_1._listAvocat = listAvocat;
    }
    /**
     * Retourne les Signataire
     *
     * @returns [[number]].
     */
    static get listSignataire() {
        return OrdersViewModelMaster_1._listSignataire;
    }
    /**
     * Met à jour les documents
     *
     * @param [[listDocument: Signataire[]]].
     */
    static set listSignataire(listSignataire) {
        OrdersViewModelMaster_1._listSignataire = listSignataire;
    }
    /**
     * Retourne les Signataire
     *
     * @returns [[number]].
     */
    static get allSignataire() {
        return OrdersViewModelMaster_1._allSignataire;
    }
    /**
     * Met à jour les documents
     *
     * @param [[listDocument: Signataire[]]].
     */
    static set allSignataire(allSignataire) {
        OrdersViewModelMaster_1._allSignataire = allSignataire;
    }
    /**
     * Retourne les documents
     *
     * @returns [[number]].
     */
    static get listDocument() {
        return OrdersViewModelMaster_1._listDocument;
    }
    /**
     * Met à jour les documents
     *
     * @param [[listDocument: Document[]]].
     */
    static set listDocument(listDocument) {
        OrdersViewModelMaster_1._listDocument = listDocument;
    }
    /**
     * Retourne l'id de l'acte
     *
     * @returns [[number]].
     */
    static get idActe() {
        return OrdersViewModelMaster_1._idActe;
    }
    /**
     * Met à jour l'id de l'acte
     *
     * @param [[idActe: number]].
     */
    static set idActe(idActe) {
        OrdersViewModelMaster_1._idActe = idActe;
    }
    static get userConnected() {
        return OrdersViewModelMaster_1._userConnected;
    }
    static set userConnected(userConnected) {
        OrdersViewModelMaster_1._userConnected = userConnected;
    }
    static get displaySendTovalidation() {
        return OrdersViewModelMaster_1._displaySendTovalidation;
    }
    static set displaySendTovalidation(displaySendTovalidation) {
        OrdersViewModelMaster_1._displaySendTovalidation = displaySendTovalidation;
    }
    /**
     * Initialise le model avec les ordres(document).
     */
    getData() {
        OrdersViewModelMaster_1.reloadComplete = false;
        let orderQuery = this.services.orderService.getAllActesAsync();
        let sigataireQuery = this.services.signataireService.getAllSignataireAsync();
        let avocatQuery = this.services.avocatService.getAllSignataireAsync();
        let documentQuery = this.services.documentService.getAllDocumentsAsync();
        let userQuery = this.services.orderService.getUserconnectedAsync();
        Promise.all([orderQuery, sigataireQuery, avocatQuery, documentQuery, userQuery]).then(results => {
            this.listOrder = results[0];
            this.allSignataire = results[1];
            for (let s of this.allSignataire) {
                if (!s.enterpriseName) {
                    s.role = "signatory";
                }
                else {
                    s.role = "enterprise";
                }
            }
            this.allAvocat = results[2];
            this.listDocument = results[3];
            OrdersViewModelMaster_1.userConnected = results[4];
            OrdersViewModelMaster_1.reloadComplete = true;
        });
    }
};
//Index de l'etape active
OrdersViewModelMaster._currentActe = new order_1.Order();
OrdersViewModelMaster._userConnected = new user_1.User();
//Index de l'etape active
OrdersViewModelMaster._activeIndex = 0;
OrdersViewModelMaster._reloadComplete = false;
//Mode Ajout(0)-Modification(1)
OrdersViewModelMaster._mode = 0;
/** Liste des Signataire */
OrdersViewModelMaster._allAvocat = [];
/** Liste des Signataire */
OrdersViewModelMaster._listAvocat = [];
/** Liste des Signataire */
OrdersViewModelMaster._listSignataire = [];
/** Liste des Signataire */
OrdersViewModelMaster._allSignataire = [];
/** Liste des fournisseurs */
OrdersViewModelMaster._listDocument = [];
OrdersViewModelMaster = OrdersViewModelMaster_1 = __decorate([
    core_1.Injectable(),
    __metadata("design:paramtypes", [SignatureServiceInjector_1.SignatureServiceInjector])
], OrdersViewModelMaster);
exports.OrdersViewModelMaster = OrdersViewModelMaster;
