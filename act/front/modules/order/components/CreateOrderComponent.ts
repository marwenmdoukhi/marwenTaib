import {
    AfterViewInit,
    animate,
    Component,
    ElementRef,
    HostListener,
    Input,
    OnInit,
    state,
    style,
    transition,
    trigger,
    ViewChild,
} from '@angular/core';
import {MenuItem, MessageService} from 'primeng/api';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Document} from '../../../shared/entities/document';
import {Signataire} from '../../../shared/entities/signataire';
import {Avocat} from '../../../shared/entities/avocat';
import * as moment from "moment";
import {NgxSpinnerService} from "ngx-spinner";
import {User} from "../../../shared/entities/user";
import {ActeService} from '../../../shared/services/acte.services';



@Component({
    selector: 'create',
    templateUrl: './CreateOrderComponent.html',
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

export class CreateOrderComponent implements OnInit, AfterViewInit {
    @ViewChild('inputFile') myInputVariable: ElementRef;

    items: MenuItem[];
    @Input() vm: IOrdersViewModelMaster;
    displayDialogAddSignataire: boolean = false;
    sigataireToAdd: Signataire = new Signataire();
    savedDocument: boolean = false;
    savedAct: boolean = false;
    display: boolean = false;
    orderChanged: boolean = false;
    lengthListDocument: boolean = false;
    displayPopupForDeleteSignatory: boolean = false;
    displayPopupForDeleteCouncel: boolean = false;
    showSpinner: boolean = false;
    readyOnlyForUser :boolean = false;
    orderStatus: boolean = false;
    notValidpdf: boolean = false;
    orders : any[];
    updatedActName : boolean = false;
    updatedActFolderName : boolean = false;
    todayDate: Date;
    constructor(private messageService: MessageService , private el : ElementRef , private spinner: NgxSpinnerService , private acteService : ActeService) {
    }

    get internalFolder(): string {
        return "A-" + OrdersViewModelMaster.idActe + "-" + ("0" + (new Date().getDate())).slice(-2) + "" + ("0" + (new Date().getMonth() + 1)).slice(-2);
    }

    showDialog() {
        this.display = true;
    }

    ngOnInit() {
        this.todayDate = new Date;
        this.readyOnlyForUser=this.oneMonthResiliation();
        this.items = [{
            label: 'Informations de l\'acte',
            command: (event: any) => {
                this.activeIndex = 0;
            }
        },
        {
            label: 'Ajouter les documents',
            command: (event: any) => {
                this.activeIndex = 1;
            }
        },
        {
            label: 'Ajouter les contacts',
            command: (event: any) => {
                this.activeIndex = 2;
            }
        },
        {
            label: 'Synthèse',
            command: (event: any) => {
                if (this.orderDetails.status != 'Validation refusee' && this.orderDetails.status != 'Signature refusee' ) {
                    this.activeIndex = 3;
                }
                else {
                    this.vm.displayActeRefused = true;
                    this.displayCreateOrder = false;
                }
            }
        }
        ];
        this.savedAct = false;
        window.scrollTo(0, 0);

        if(window.innerWidth <= 768) {
            this.changeResponsiveStepsContent();
        }
        let orderQuery = this.acteService.getAllActesAsync();
        Promise.all([orderQuery]).then(result => {
            this.orders = result[0];
        })
    }

    get listSignataire(): Signataire[] {
        return OrdersViewModelMaster.listSignataire;
    }
    set listSignataire(listSignataire: Signataire[]) {
        OrdersViewModelMaster.listSignataire = listSignataire;
    }

    deleteSignatairePopup(){
        this.displayPopupForDeleteSignatory = true;
    }

    deleteSignataire(signataire: any) {
        console.log(signataire);
        
        this.vm.services.signataireService.deleteSignataire(signataire).then(() => {
            this.vm.services.messageService.add({ severity: 'success', summary: 'Supression signataire', detail:  signataire.name[0].toUpperCase() + signataire.name.slice(1) + ' supprimé avec succès', life: 4000 });
            for (let av of this.listSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId && av.enterpriseName == signataire.enterpriseName) {
                    this.listSignataire = this.listSignataire.filter(a => a != av);
                }
            }
            for (let av of this.vm.allSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId && av.enterpriseName == signataire.enterpriseName) {
                    this.vm.allSignataire = this.vm.allSignataire.filter(a => a != av);
                }
            }
        });
    }

    deleteAvocatPopup(){
       this.displayPopupForDeleteCouncel = true;
    }

    deleteAvocat(avocat: any) {
        this.vm.services.avocatService.deleteAvocat(avocat).then(() => {
            this.vm.services.messageService.add({ severity: 'success', summary: 'Supression avocat', detail: avocat.name[0].toUpperCase() + avocat.name.slice(1) + ' supprimé avec succès', life: 4000 });
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

    onKey(event: any){
        event.target.value = event.target.value.charAt(0).toLocaleUpperCase()+event.target.value.slice(1);
    }

    keyPressAlphanumeric(event: any){
        var inp = String.fromCharCode(event.keyCode);

        if (/^[-\w\s]+$/.test(inp)) {
            return true;
        } else {
            event.preventDefault();
            return false;
        }
    }


    showDialogAddSignataire(): void {
        this.displayDialogAddSignataire = true;
        this.sigataireToAdd = new Signataire();
    }
    set listAvocat(value: Avocat[]) {
        OrdersViewModelMaster.listAvocat = value;
    }
    get listAvocat(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }
    nextStage() {
        if (this.activeIndex == 2 && (this.orderDetails.status == 'Validation refusee' || this.orderDetails.status == 'Signature refusee')) {
            this.vm.displayActeRefused = true;
            this.displayCreateOrder = false;
        }
        else if (this.activeIndex != 3)  {
             this.activeIndex = this.activeIndex + 1;
        }
    }

    displayPToast() {
        this.vm.services.messageService.add({ severity: 'success', summary: 'Informations de l\'acte', detail: 'L\'acte a bien été enregistré', life: 4000 });
    }

    verifyContacts(){
        if (this.listSignataire.length > 0){
            this.nextStage();
        }else{
            this.vm.services.messageService.add({ severity: 'error', summary: 'Informations de l\'acte', detail: 'L\'ajout d\'un signataire est obligatoire', life: 4000 });
        }
    }

    addActe() {
            if (this.savedAct == true) {
                OrdersViewModelMaster.mode = 0;
                if (!this.orderDetails.id) {
                    this.orderDetails.id = OrdersViewModelMaster.idActe;
                }
                let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
                Promise.all([saveOrderQuery]).then(results => {
                    OrdersViewModelMaster.idActe = results[0].id;
                    this.orderDetails = results[0];
                    this.orderDetails.requestDate = moment(results[0].requestDate.date).format("DD/MM/YYYY HH:mm:ss");  //formatter la date
                    if (!this.vm.listOrder.some(act => act.id == this.orderDetails.id)){
                        this.vm.listOrder.unshift(this.orderDetails);
                    }
                    if (!this.orderDetails.id) {
                        this.getActById();
                    }
                    this.savedAct = false;
                });
                this.vm.services.messageService.add({ severity: 'success', summary: 'Informations de l\'acte', detail: 'Acte sauvegardé avec succès', life: 4000 });
            }
            else {
                this.vm.services.messageService.add({ severity: 'success', summary: 'Informations de l\'acte', detail: 'L’acte a bien été enregistré', life: 4000 });
            }

    }

    changeStatusToSentToValidate() {
        this.orderDetails.status = "En cours de validation";
        this.orderDetails.folderNumber = this.internalFolder;
        let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
        Promise.all([saveOrderQuery]).then(results => {
        });
    }

    changeStatusToSentToSignature() {
        this.orderDetails.status = "En cours de signature";
        this.orderDetails.folderNumber = this.internalFolder;
    }

    updateStatutActe() {
        if (this.listSignataire.length > 0){
            if (this.orderDetails.status != "Cree" && this.listDocument.length > 0 ) {
                this.orderDetails.status = "Cree";
                this.orderDetails.folderNumber = this.internalFolder;
                this.orderStatus = true;
                let saveOrderQuery = this.vm.services.orderService.postAct(this.orderDetails);
                Promise.all([saveOrderQuery]).then(results => {
                });
            }
        }else{
            this.vm.services.messageService.add({ severity: 'error', summary: 'Informations de l\'acte', detail: 'Afin de créer l\'acte ajouter un signataire', life: 4000 });
        }

    }

    updateStatutActeCree() {
        if (this.listSignataire.length > 0) {
            
        } else {
            this.vm.services.messageService.add({ severity: 'error', summary: 'Informations de l\'acte', detail: 'Afin de créer l\'acte ajouter un signataire', life: 4000 });
        }

    }

    savingActProcess(): void {
        if (!this.orders.some(act => act.name == this.orderDetails.name && act.folderName == this.orderDetails.internalNumber && act.internalNumber == this.orderDetails.folderNumber)) {
            this.addActe();
            this.nextStage();
        }
        else {
            this.vm.services.messageService.add({ severity: 'error', summary: 'Informations de l\'acte', detail: 'Il existe déja un acte portant ce nom, veuillez le changer', life: 4000 });
        }
    }

    get listDocument(): Document[] {
        return OrdersViewModelMaster.listDocument;
    }
    set listDocument(lisDocument: Document[]) {
        OrdersViewModelMaster.listDocument = lisDocument;
    }

    downloadAllDocument() {
        for (let file of this.listDocument as any) {
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

    downloadPDF(file: any) {
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

    getFileSize(name: String): number {
        return (this.listDocument.filter(doc => doc.name == name)[0].size) / 1048576;
    }

    deleteDocument(file: Document) {
        this.vm.services.documentService.deleteDocument(file).then(() => {
            OrdersViewModelMaster.listDocument = OrdersViewModelMaster.listDocument.filter(d => d.name != file.name);
            this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == file.actId && elm.name == file.name));
            this.vm.services.messageService.add({
                severity: 'success',
                summary: 'Documents',
                detail: ' Suppression du document effectuée avec succès',
                life: 4000
            });

        });
    }
    viewPdf(file: any) {
        let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
        Promise.all([fileQuery]).then(results => {
            if (results) {
                let path = 'data:application/pdf;base64,' + results;
                let pdfWindow = window.open("");
                pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");

            }
        });

    }
    onAddDocument(event: any) {
        let documentToLoad = 0;
        console.log(event.target.files)
        if (event.target.files) {
            documentToLoad = event.target.files.length;
            for (let file of event.target.files) {
                let documentModel = new Document();
                const reader = new FileReader();
                reader.onload = ((e) => {
                    documentToLoad--;
                    let base64str = e.target['result'].toString();
                    documentModel.file = base64str.split(',')[1];
                    documentModel.name = file.name.substr(0, file.name.lastIndexOf('.'));
                    documentModel.extension = file.type.substr(file.type.indexOf('/') + 1);
                    documentModel.actId = OrdersViewModelMaster.idActe;
                    documentModel.size = file.size;
                    if (!this.listDocument.some(d => d.name == documentModel.name.replace(/\s/g, ''))) {
                        let s = (documentModel.size/1048576).toFixed(3);
                        if (documentModel.extension == 'jpeg' || documentModel.extension == 'pdf' || documentModel.extension == 'png' || documentModel.extension == 'bmp'){
                            if (parseFloat(s) < 40) {
                                this.showSpinner = true;
                                this.saveDocument(documentModel, documentToLoad);
                            }else{
                                this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Taille du ' + documentModel.name + ' dépasse 40 Mo', life: 50000 });
                            }
                        }else{
                            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: documentModel.name + ' doit être au format .pdf ou .png', life: 4000 });
                        }
                    }
                    else {
                        this.messageService.add({ severity: 'warn', summary: 'Conversion de documents', detail: documentModel.name + ' existe déjà pour cet acte', life: 4000 });
                    }
                });
                reader.readAsDataURL(file);
            }
            this.myInputVariable.nativeElement.value = '';
        }
    }
    handleDrop(e: any) {
        let documentToLoad = 0;
        this.showSpinner = true;
        e.preventDefault();
        var files: any = e.dataTransfer.files;
        Object.keys(files).forEach((key) => {
            documentToLoad = files.length;
            let file: File = files[key];
                let documentModel = new Document();
                const reader = new FileReader();
            reader.onload = ((e) => {
                documentToLoad--;
                    let base64str = e.target['result'].toString();
                    documentModel.file = base64str.split(',')[1];
                    documentModel.name = file.name.substr(0, file.name.lastIndexOf('.'));
                    documentModel.extension = file.type.substr(file.type.indexOf('/') + 1);
                    documentModel['actId'] = OrdersViewModelMaster.idActe;
                    documentModel.size = file.size;
                    if (!this.listDocument.some(d => d.name == documentModel.name.replace(/\s/g, ''))) {
                        let s = (documentModel.size / 1048576).toFixed(3);
                        if (documentModel.extension == 'jpeg' || documentModel.extension == 'pdf' || documentModel.extension == 'png') {
                            if (s < '40') {
                                this.showSpinner = true;
                                this.saveDocument(documentModel, documentToLoad);
                            } else {
                                this.showSpinner = false;
                                this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Taille du ' + documentModel.name + ' dépasse 40 Mo', life: 4000 });
                            }
                        } else {
                            this.showSpinner = false;
                            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: documentModel.name + ' doit être au format .pdf ou .png', life: 4000 });
                        }
                    }
                    else {
                        this.showSpinner = false;
                        this.messageService.add({ severity: 'warn', summary: 'Conversion de documents', detail: documentModel.name + ' existe déjà pour cet acte', life: 4000 });
                    }
                });
                reader.readAsDataURL(file);
        });
    }
    Base64DecodeUrl(str){
        str = (str + '===').slice(0, str.length + (str.length % 4));
        return str.replace(/-/g, '+').replace(/_/g, '/');
    }
    saveDocument(document: Document,documentToLoad:number) {
        let maxSize=0;
        for(let i=0;i<OrdersViewModelMaster.listDocument.length;i++){
            maxSize+=OrdersViewModelMaster.listDocument[i].size;
        }
        maxSize+=document.size;
        maxSize=maxSize/1048576;
        if(maxSize<=40){
            document.file=this.Base64DecodeUrl(document.file);
            document.position = OrdersViewModelMaster.listDocument.length;
            let orderQuery = this.vm.services.documentService.postDocument(document);
            Promise.all([orderQuery.catch(err => {
                this.showSpinner = false;
                this.vm.services.messageService.add({ severity: 'error', summary: 'Ajout de documents', detail: 'Veuillez vérifier que les noms des documents déposés ne contiennent pas de caractères spéciaux et/ou que la taille de l\'ensemble des documents déposés ne dépasse pas les 40Mo autorisés. Veuillez également remplir l\'ensemble des champs obligatoires. Si les difficultés persistent, veuillez contacter l’assistance au 09.70.82.33.21 (coût d\'une communication normale vers la France métropolitaine).', life: 4000 });
                throw err;
            })]).then(results => {
                if (results[0] === "not a valid pdfa2b") {
                    this.showSpinner = false;
                    this.notValidpdf = true;
                } else if(results[0] === "error while upload document to s3"){
                    this.showSpinner = false;
                    this.vm.services.messageService.add({
                        severity: 'error',
                        summary: 'upload du document',
                        detail: document.name + ' n\'a pas été correctement uploadé ,merci de verifier que le nom du document ne contient pas des caractères spéciaux',
                        life: 8000
                    });
                } else
                    {
                    document = results[0][0];
                    OrdersViewModelMaster.listDocument.push(document);
                    if (!this.vm.listDocument.some(d => d.name == document.name && d.actId == document.actId)) {
                        this.vm.listDocument.push(document);
                    }
                    if (documentToLoad == 0) {
                        this.showSpinner = false;
                    }
                    this.vm.services.messageService.add({
                        severity: 'success',
                        summary: 'Conversion de documents',
                        detail: document.name + ' ajouté et converti avec succès',
                        life: 8000
                    });

                    this.savedDocument = true;
                }

            });
        }else{
            this.showSpinner = false;
            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Taille totale des documents dépasse 40 Mo', life: 4000 });
        }


    }
    saveSignataire(): void {
        this.vm.signatairesModel.push(this.sigataireToAdd);
        this.vm.services.signataireService.postSignataire(this.vm.signatairesModel[0]);
    }

    documentSavingProcess(): void {
        if (this.listDocument.length > 0) {
            for (let doc of this.listDocument) {
                doc.position = this.listDocument.indexOf(doc);
                this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == doc.actId && elm.name==doc.name));
                this.vm.listDocument.push(doc);
            }
            if (this.orderChanged || this.listDocument.filter(d => d.position == 0).length > 1) {
                this.vm.services.documentService.postsDocumentsPositionAsync(this.listDocument).then(results => {
                    if (this.savedDocument == true) {
                        this.nextStage();
                    }
                });
            }
            else {
                if (this.savedDocument == true && this.listDocument.length > 0) {
                    this.nextStage();
                } else if (this.savedDocument == false && this.listDocument.length > 0) {
                    //this.saveDocument();
                    this.nextStage();
                }
            }
        }
        else {
            this.vm.services.messageService.add({ severity: 'error', summary: 'Ajout de documents', detail: 'L\'ajout d\'un document est obligatoire', life: 4000});
            this.lengthListDocument = true;
            setTimeout(() => {
                this.lengthListDocument = false;
            }, 5000);
        }
    }

    sauveagerderDocument() {

        if (this.listDocument.length > 0) {
            this.vm.services.messageService.add({ severity: 'success', summary: 'Ajout de documents', detail: 'L’acte a bien été enregistré', life: 4000 });
        }
        else {
            this.vm.services.messageService.add({ severity: 'error', summary: 'Ajout de documents', detail: 'L\'ajout d\'un document est obligatoire', life: 4000 });

             this.lengthListDocument = true;
            setTimeout(() => {
                this.lengthListDocument = false;
            }, 5000);
        }
    }

    getActById() {
        this.vm.services.orderService.getActByIdAsync(OrdersViewModelMaster.idActe).then(results => {
            this.orderDetails = results;
        });
    }

    signataireToModify(signataire: any): void {
        OrdersViewModelMaster.signataireToModify = signataire;
    }
    avocatToModify(avocat: any): void {
        OrdersViewModelMaster.avocatToModify = avocat;
    }
    get displayModalSignataire(): boolean {
        return OrdersViewModelMaster.displayModalSignataire;
    }

    set displayModalSignataire(value: boolean) {
        OrdersViewModelMaster.displayModalSignataire = value;
    }

    get displayModalAvocat(): boolean {
        return OrdersViewModelMaster.displayModalAvocat;
    }

    set displayModalAvocat(value: boolean) {
        OrdersViewModelMaster.displayModalAvocat = value;
    }

    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }

    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }


    get listeSignataire(): Signataire[] {
        return OrdersViewModelMaster.listSignataire;
    }
    set listeSignataire(value: Signataire[]) {
        OrdersViewModelMaster.listSignataire = value;
    }

    get listeAvocat(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }
    set listeAvocat(value: Avocat[]) {
        OrdersViewModelMaster.listAvocat = value;
    }

    get activeIndex(): number {
        return OrdersViewModelMaster.activeIndex;
    }
    set activeIndex(activeIndex: number) {
        OrdersViewModelMaster.activeIndex = activeIndex;
    }

    get orderDetails(): Order {
        return OrdersViewModelMaster.currentActe;
    }
    set orderDetails(orderDetails: Order) {
        OrdersViewModelMaster.currentActe = orderDetails;
    }
    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        this.orderChanged = true;
        return arr;
    }

  

    upDocumentPosition(file: any) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) - 1);
    }

    downDocumentPosition(file: any) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) + 1);
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




    @HostListener('window:resize', ['$event'])
    onResize(event) {
        let windowWidth:number = window.innerWidth;
        if(windowWidth <= 768) {
            this.changeResponsiveStepsContent();
        }
    }

    changeResponsiveStepsContent() {
        this.items = [{
            label: 'Informations',
            command: (event: any) => {
                this.activeIndex = 0;
            }
        },
            {
                label: 'Documents',
                command: (event: any) => {
                    this.activeIndex = 1;
                }
            },
            {
                label: 'Contacts',
                command: (event: any) => {
                    this.activeIndex = 2;
                }
            },
            {
                label: 'Synthèse',
                command: (event: any) => {
                    if (this.orderDetails.status != 'Validation refusee' && this.orderDetails.status != 'Signature refusee' ) {
                        this.activeIndex = 3;
                    }
                    else {
                        this.vm.displayActeRefused = true;
                        this.displayCreateOrder = false;
                    }
                }
            }
        ];
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

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.add-form-actions button').each(function(index, el) {
                $(el).blur();
            });
        });
       let steps = document.querySelectorAll('.ui-state-complete');
       if (this.listDocument.length == 0){
           if (steps[1] && steps[1].classList.contains('ui-state-complete')){
               steps[1].classList.remove('ui-state-complete');
           }
       }
       if (this.listeSignataire.length == 0){
           console.log('1')
           if (steps[2] && steps[2].classList.contains('ui-state-complete')) {
               console.log("2")
               steps[2].classList.remove('ui-state-complete');
           }
       }
    }
    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }
    oneMonthResiliation(){
        let dateDiff=moment().diff(moment(this.userConnected.resiliation,'DD/MM/YYYY'),'months',true);
        return dateDiff <=1 || this.userConnected.resiliation ===null;
    }
}