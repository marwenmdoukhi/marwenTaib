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
const document_1 = require("../../../shared/entities/document");
const signataire_1 = require("../../../shared/entities/signataire");
const avocat_1 = require("../../../shared/entities/avocat");
let CreateOrderComponent = class CreateOrderComponent {
    constructor(messageService) {
        this.messageService = messageService;
        this.displayDialogAddSignataire = false;
        this.sigataireToAdd = new signataire_1.Signataire();
        this.savedDocument = false;
        this.savedAct = false;
        this.display = false;
    }
    //A-ID-DDMM.
    get internalFolder() {
        return "A-" + OrdersViewModelMaster_1.OrdersViewModelMaster.idActe + "-" + ("0" + (new Date().getDate())).slice(-2) + "" + ("0" + (new Date().getMonth() + 1)).slice(-2);
    }
    showDialog() {
        this.display = true;
    }
    ngOnInit() {
        this.items = [{
                label: 'Informations de l\'acte',
                command: (event) => {
                    this.activeIndex = 0;
                }
            },
            {
                label: 'Ajouter les documents',
                command: (event) => {
                    this.activeIndex = 1;
                }
            },
            {
                label: 'Ajouter les contacts',
                command: (event) => {
                    this.activeIndex = 2;
                }
            },
            {
                label: 'Synthèse',
                command: (event) => {
                    this.activeIndex = 3;
                }
            }
        ];
        //OrdersViewModelMaster.idActe = null;
        //this.vm.acteModel = new ActModel(new Order());
        window.scrollTo(0, 0);
    }
    deleteSignataire(signataire) {
        this.listeSignataire = this.listeSignataire.filter(s => s.name != signataire.name);
        OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire = OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire.filter(s => s.name != signataire.name);
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = new signataire_1.Signataire();
        if (this.listeSignataire.length == 0) {
            OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = false;
        }
        this.vm.services.signataireService.deleteSignataire(signataire).then(() => {
        });
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
    showDialogAddSignataire() {
        this.displayDialogAddSignataire = true;
        this.sigataireToAdd = new signataire_1.Signataire();
    }
    /*
      Passer à l'étape suivante
    */
    nextStage() {
        if (this.activeIndex != 3) {
            this.activeIndex = this.activeIndex + 1;
        }
    }
    /*
    sauvgarder un acte
    */
    addActe() {
        //Mode Création
        OrdersViewModelMaster_1.OrdersViewModelMaster.mode = 0;
        if (!this.orderDetails.id) {
            this.orderDetails.id = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe;
        }
        let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
        Promise.all([saveOrderQuery]).then(results => {
            OrdersViewModelMaster_1.OrdersViewModelMaster.idActe = results[0].id;
            this.orderDetails = results[0];
            this.orderDetails.requestDate = results[0].requestDate.date; //formatter la date
            this.vm.listOrder.push(this.orderDetails);
            if (!this.orderDetails.id) {
                this.getActById();
            }
        });
        this.savedAct = true;
    }
    changeStatusToSentToValidate() {
        this.orderDetails.status = "En cours de validation";
        this.orderDetails.folderNumber = this.internalFolder;
        let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
        Promise.all([saveOrderQuery]).then(results => {
            // this.router.navigateByUrl("/act", { skipLocationChange: true });
        });
    }
    changeStatusToSentToSignature() {
        this.orderDetails.status = "En cours de signature";
        this.orderDetails.folderNumber = this.internalFolder;
        let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
        Promise.all([saveOrderQuery]).then(results => {
            // this.router.navigateByUrl("/act", { skipLocationChange: true });
        });
    }
    updateStatutActe() {
        this.orderDetails.status = "Cree";
        this.orderDetails.folderNumber = this.internalFolder;
        let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
        Promise.all([saveOrderQuery]).then(results => {
        });
    }
    savingActProcess() {
        this.addActe();
        this.nextStage();
    }

    get listDocument() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument;
    }
    set listDocument(lisDocument) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument = lisDocument;
    }
    downloadAllDocument() {
        for (let file of this.listDocument) {
            var link = document.createElement('a');
            link.href = '/documents/' + file.name.replace(/\s/g, '') + '.pdf';
            link.download = file.name.replace(/\s/g, '');
            link.click();
        }
    }
    /*
     * Downloader un pdf
     */
    downloadPDF(file) {
        return '/documents/' + file.name.replace(/\s/g, '') + '.pdf';
    }
    /*
   Calcule de la taille de fichier
   */
    getFileSize(name) {
        return (this.listDocument.filter(doc => doc.name == name)[0].size) / 1000000;
    }
    /*
   Supprimier un document
   */
    deleteDocument(file) {
        this.vm.services.documentService.deleteDocument(file).then(() => {
            OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument = OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument.filter(d => d.name != file.name);
            this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == doc.actId && elm.name == doc.name));
        });
    }
    /*
     Afiicher un document
     */
    viewPdf(file) {
        let pdfWindow = window.open("");
        pdfWindow.document.write("<iframe width='100%' height='100%' src='/documents/" + file.name.replace(/\s/g, '') + ".pdf'></iframe>");
    }
    test() {
        //console.log(this.orderDetails);
    }
    /*
Ajout d'un document via window
*/
    onAddDocument(event) {
        let documentModel = new document_1.Document();
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = ((e) => {
                let base64str = e.target['result'].toString();
                documentModel.file = base64str.split(',')[1];
                documentModel.name = event.target.files[0].name.substr(0, event.target.files[0].name.indexOf('.'));
                documentModel.extension = event.target.files[0].type.substr(event.target.files[0].type.indexOf('/') + 1);
                documentModel.actId = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe;
                if (!this.listDocument.some(d => d.name == documentModel.name)) {
                    this.saveDocument(documentModel);
                }
                else {
                    this.messageService.add({ severity: 'warn', summary: 'Conversion de documents', detail: +documentModel.name + ' existe déjà pour cet acte', life: 4000 });
                }
            });
            reader.readAsDataURL(event.target.files[0]);
        }
    }
    /*
    Ajout document avec drug and drop
     */
    handleDrop(e) {
        e.preventDefault();
        var files = e.dataTransfer.files;
        Object.keys(files).forEach((key) => {
            let file = files[key];
            if (!OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument.some(doc => doc.name == file.name)) {
                let documentModel = new document_1.Document();
                const reader = new FileReader();
                reader.onload = ((e) => {
                    let base64str = e.target['result'].toString();
                    documentModel.file = base64str.split(',')[1];
                    documentModel.name = file.name.substr(0, file.name.indexOf('.'));
                    documentModel.extension = file.type.substr(file.type.indexOf('/') + 1);
                    documentModel['actId'] = OrdersViewModelMaster_1.OrdersViewModelMaster.idActe;
                    if (!this.listDocument.some(d => d.name == documentModel.name)) {
                        this.saveDocument(documentModel);
                    }
                    else {
                        this.messageService.add({ severity: 'warn', summary: 'Conversion de documents', detail: 'Le document existe déjà', life: 4000 });
                    }
                });
                reader.readAsDataURL(file);
            }
        });
    }
    /*
     Persister un document
     */
    saveDocument(document) {
        let orderQuery = this.vm.services.documentService.postDocument(document);
        Promise.all([orderQuery.catch(err => {
            this.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Document ' + document.name + ' n\'est pas convertie correctement veuillez réessayer', life: 4000 });
                throw err;
            })]).then(results => {
            document = results[0][0];
            OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument.push(document);
            if (!this.vm.listDocument.some(d => d.name == document.name && d.actId == document.actId)) {
                this.vm.listDocument.push(document);
            }
                this.messageService.add({ severity: 'success', summary: 'Conversion de documents', detail: 'Document ' + document.name + ' converti avec succès', life: 4000 });
        });
        this.savedDocument = true;
    }
    /*
     Persister un signataire
     */
    saveSignataire() {
        this.vm.signatairesModel.push(this.sigataireToAdd);
        this.vm.services.signataireService.postSignataire(this.vm.signatairesModel[0]);
    }
    documentSavingProcess() {
        if (this.savedDocument == true) {
            this.nextStage();
        }
        else {
            //this.saveDocument();
            this.nextStage();
        }
    }
    getActById() {
        this.vm.services.orderService.getActByIdAsync(OrdersViewModelMaster_1.OrdersViewModelMaster.idActe).then(results => {
            this.orderDetails = results;
        });
    }
    signataireToModify(signataire) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.signataireToModify = signataire;
    }
    avocatToModify(avocat) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.avocatToModify = avocat;
    }
    get displayModalSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire;
    }
    set displayModalSignataire(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalSignataire = value;
    }
    get displayModalAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat;
    }
    set displayModalAvocat(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.displayModalAvocat = value;
    }
    get listeSignataire() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire;
    }
    set listeSignataire(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.listSignataire = value;
    }
    get listeAvocat() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat;
    }
    set listeAvocat(value) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.listAvocat = value;
    }
    get activeIndex() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.activeIndex;
    }
    set activeIndex(activeIndex) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.activeIndex = activeIndex;
    }
    get orderDetails() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.currentActe;
    }
    set orderDetails(orderDetails) {
        OrdersViewModelMaster_1.OrdersViewModelMaster.currentActe = orderDetails;
    }
    arrayChangePosition(arr, fromIndex, toIndex) {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        return arr;
    }
    upDocumentPosition(file) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) - 1);
    }
    downDocumentPosition(file) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) + 1);
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
    getStatut(value) {
        switch (value) {
            case "Cree": {
                return 'Créé';
            }
            case "Valide": {
                return 'Validé';
            }
            case "Validation refusee": {
                return 'Validation refusée';
            }
            case "Signe": {
                return 'Signé';
            }
            case "Signe": {
                return 'Signature refusée';
            }
            default: {
                return value;
            }
        }
    }
};
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], CreateOrderComponent.prototype, "vm", void 0);
CreateOrderComponent = __decorate([
    core_1.Component({
        selector: 'create',
        templateUrl: './CreateOrderComponent.html',
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
    __metadata("design:paramtypes", [api_1.MessageService])
], CreateOrderComponent);
exports.CreateOrderComponent = CreateOrderComponent;
