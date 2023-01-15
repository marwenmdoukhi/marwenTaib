import {
    AfterViewInit,
    animate,
    Component,
    ElementRef,
    Input,
    OnInit,
    state,
    style,
    transition,
    trigger,
    ViewChild
} from '@angular/core';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {DomSanitizer} from '@angular/platform-browser';
import {Signataire} from '../../../shared/entities/signataire';
import {Document} from "../../../shared/entities/document";
import {Avocat} from "../../../shared/entities/avocat";
import {User} from "../../../shared/entities/user";

declare const $: any;

@Component({
    selector: 'syntheseActeRefusee',
    templateUrl: './syntheseActeRefusee.html',
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

export class syntheseActeRefusee implements OnInit, AfterViewInit {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    orderChanged: boolean = false;
    @ViewChild('inputFile') myInputVariable: ElementRef;
    @ViewChild('inputElSig') inputElSig;

    constructor(private sanitized: DomSanitizer, private messageService: MessageService) { }
    ngOnInit(): void {
        window.scrollTo(0, 0);
    }
    get listDocument(): Document[] {
        return OrdersViewModelMaster.listDocument;
    }
    set listDocument(lisDocument: Document[]) {
        OrdersViewModelMaster.listDocument = lisDocument;
    }

    get listSignataire(): Signataire[] {
        return OrdersViewModelMaster.listSignataire;
    }
    get listAvocat(): Avocat[] {
        return OrdersViewModelMaster.listAvocat;
    }

    get activeIndex(): number {
        return OrdersViewModelMaster.activeIndex;
    }
    set activeIndex(activeIndex: number) {
        OrdersViewModelMaster.activeIndex = activeIndex;
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
            let fileQuery = this.vm.services.orderService.getBase64File(this.listDocument[0]['actId'],file.name);
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let path = 'data:application/pdf;base64,' + results;
                    let pdfWindow = window.open("");
                    pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");
                }
            });
        }
    }


    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        this.orderChanged = true;
        return arr;
    }
    changeDocumentPosition() {
        for (let doc of this.listDocument) {
            doc.position = this.listDocument.indexOf(doc);
            this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == doc.actId && elm.name == doc.name));
            this.vm.listDocument.push(doc);
        }
        this.vm.services.documentService.postsDocumentsPositionAsync(this.listDocument).then(results => {
        });
    }
    upDocumentPosition(file: any) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) - 1);
    }

    downDocumentPosition(file: any) {
        this.listDocument = this.arrayChangePosition(this.listDocument, this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]), this.listDocument.indexOf(this.listDocument.filter(doc => doc.name == file.name)[0]) + 1);
    }

    sendMailRelance(itemMail: string) {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id.toString();
        if (itemMail) {
            validatUdser["unitUser"] = itemMail;
        }

        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendRelanceToValidateAct(validatUdser);
        Promise.all([sendMailToAvocat]).then(results => {
            this.currentActe.lastResentDate = new Date().toLocaleString();
        });
    }

    sendMailSignRelance(itemMail: string) {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        validatUdser["actId"] = this.currentActe.id;
        if (itemMail) {
            validatUdser["unitUser"] = itemMail;
        }
        let sendRelanceSignMail = this.vm.services.sendMailToValidate.sendRelanceToSignAct(validatUdser);
        Promise.all([sendRelanceSignMail]).then(results => {
            this.currentActe.lastResentDate = new Date().toLocaleString();
        });
    }


    onAddDocument(event: any) {
        if (event.target.files ) {
            for (let file of event.target.files) {
                let documentModel = new Document();
                const reader = new FileReader();
                reader.onload = ((e) => {
                    let base64str = e.target['result'].toString();
                    documentModel.file = base64str.split(',')[1];
                    documentModel.name = file.name.substr(0, file.name.indexOf('.'));
                    documentModel.extension = file.type.substr(file.type.indexOf('/') + 1);
                    documentModel.actId = OrdersViewModelMaster.idActe;
                    documentModel.size = file.size;

                    if (!this.listDocument.some(d => d.name == documentModel.name.replace(/\s/g, ''))) {
                        let s = (documentModel.size / 10485760).toFixed(3);
                        if (documentModel.extension == 'jpeg' || documentModel.extension == 'pdf' || documentModel.extension == 'png') {
                            if (s < '40') {
                                this.saveDocument(documentModel);
                            } else {
                                this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Taille du ' + documentModel.name + ' dépasse 40 Mo', life: 4000 });
                            }
                        } else {
                            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: documentModel.name + 'doit être au format .pdf ou .png', life: 4000 });
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

    saveDocument(document: Document) {
        document.position = OrdersViewModelMaster.listDocument.length;
        let orderQuery = this.vm.services.documentService.postDocument(document);
        Promise.all([orderQuery.catch(err => {
            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Document ' + document.name + ' n\'est pas convertie correctement veuillez réessayer', life: 4000 });
            throw err;
        })]).then(results => {
            document = results[0][0];
            OrdersViewModelMaster.listDocument.push(document);
            if (!this.vm.listDocument.some(d => d.name == document.name && d.actId == document.actId)) {
                this.vm.listDocument.push(document);
            }
            this.vm.services.messageService.add({ severity: 'success', summary: 'Conversion de documents', detail: 'Document ' + document.name + ' converti avec succès', life: 4000 });
        });
    }
    handleDrop(e: any) {
        e.preventDefault();
        var files: any = e.dataTransfer.files;
        Object.keys(files).forEach((key) => {
            let file: File = files[key];
                let documentModel = new Document();
                const reader = new FileReader();
                reader.onload = ((e) => {
                    let base64str = e.target['result'].toString();
                    documentModel.file = base64str.split(',')[1];
                    documentModel.name = file.name.substr(0, file.name.indexOf('.'));
                    documentModel.extension = file.type.substr(file.type.indexOf('/') + 1);
                    documentModel['actId'] = OrdersViewModelMaster.idActe;
                    documentModel.size = file.size;
                    if (!this.listDocument.some(d => d.name == documentModel.name.replace(/\s/g, ''))) {
                        let s = (documentModel.size / 10485760).toFixed(3);
                        if (documentModel.extension == 'jpeg' || documentModel.extension == 'pdf' || documentModel.extension == 'png') {
                            if (s < '40') {
                                this.saveDocument(documentModel);
                            } else {
                                this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: 'Taille du ' + documentModel.name + ' dépasse 40 Mo', life: 4000 });
                            }
                        } else {
                            this.vm.services.messageService.add({ severity: 'error', summary: 'Conversion de documents', detail: documentModel.name + 'doit être au format .pdf ou .png', life: 4000, });
                        }
                    }
                    else {
                        this.messageService.add({ severity: 'warn', summary: 'Conversion de documents', detail: documentModel.name + ' existe déjà pour cet acte', life: 4000 });
                    }
                });
                reader.readAsDataURL(file);
        });
    }
    deleteDocument(file: Document) {
        this.vm.services.documentService.deleteDocument(file).then(() => {
            OrdersViewModelMaster.listDocument = OrdersViewModelMaster.listDocument.filter(d => d.name != file.name);
            this.vm.listDocument = this.vm.listDocument.filter(elm => !(elm.actId == file.actId && elm.name == file.name));
            this.vm.services.messageService.add({ severity: 'success', summary: 'Documents', detail: ' Suppression du document effectuée avec succès', life: 4000 });
        });
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




    deleteSignataire(signataire: any) {
        this.vm.services.signataireService.deleteSignataire(signataire).then(() => {
            for (let av of this.listSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId) {
                    this.listSignataire = this.listSignataire.filter(a => a != av);
                }
            }
            for (let av of this.vm.allSignataire) {
                if (av.name == signataire.name && av.email == signataire.email && av.actId == signataire.actId) {
                    this.vm.allSignataire = this.vm.allSignataire.filter(a => a != av);
                }
            }
        });
    }

    deleteAvocat(avocat: any) {
        this.vm.services.avocatService.deleteAvocat(avocat).then(() => {
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

    set listSignataire(listSignataire: Signataire[]) {
        OrdersViewModelMaster.listSignataire = listSignataire;
    }
    get canSendToValidation(): boolean {
        return this.listAvocat.some(avocat => avocat.validator && avocat.validator == true) || this.listSignataire.some(signataire => signataire.validator && signataire.validator == true)
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
        if (item.validatedAt && item.comment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> Validation refusée le</span> " + item.validatedAt);
        }
        else if (item.validatedAt && !item.comment) {
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
    createMergeFile(document: string[]) {

        let mergeFileQuery = this.vm.services.documentService.getMergedDocumentAsync(document);
        Promise.all([mergeFileQuery]).then(results => {
        });
    }

    setContactToValidator() {
        console.log(this.inputElSig.nativeElement.checked);
        let userToSendMail: any[] = [];
        for (let avocat of this.listAvocat) {
            avocat.actValidated = null;
            avocat.comment = null;
        }
        for (let sig of this.listSignataire) {
            sig.signatureComment = null;
            sig.signedAt = null;
            sig.actValidated = null;
            sig.comment = null;
            sig.actValidated = null;
        }
        for (let avocat of this.listAvocat.filter(av => av.validator && av.validator == true)) {
            avocat.mailSent = true;
            userToSendMail.push(avocat);
        }
        for (let sig of this.listSignataire.filter(sig => sig.validator && sig.validator == true)) {
            sig.mailSent = true;
            userToSendMail.push(sig);
        }

        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendMailToValidateAct(userToSendMail);
        Promise.all([sendMailToAvocat]).then(results => {
            if (results[0] == 'email sent') {
                let changeUsertoValidator = this.vm.services.sendMailToValidate.changeUserToValidator(userToSendMail);
                this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) envoyé(s) avec succès', life: 4000 });
                Promise.all([changeUsertoValidator]).then(results => {
                    this.currentActe.status = "En cours de validation";
                    let saveOrderQuery = this.vm.services.orderService.postAct(this.currentActe);
                    Promise.all([saveOrderQuery]).then(results => {
                    });
                    this.displayCreateOrder = false;
                    this.displayConsultOrder = false;
                    this.displaySendTovalidation = false;
                    this.vm.displaySentToSignature = false;
                    this.vm.displayComments = false;
                    this.vm.displayActeRefused = false;
                });
            }
            else {
                this.vm.services.messageService.add({ severity: 'error', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) non envoyé(s)', life: 4000 });
            }
        });
    }


    sendMailToSign() {
        let userToSendMail: any[] = [];
        for (let sig of this.listSignataire) {
            sig.signatureComment = null;
            sig.signedAt = null;
        }

        for (let sig of this.listSignataire) {
            userToSendMail.push(sig);
        }
        let sendMailToAvocat = this.vm.services.sendMailToValidate.sendMailToSign(userToSendMail);
        Promise.all([sendMailToAvocat]).then(results => {
            if (results[0] == 'email sent') {
                this.createMergeFile(['' + this.currentActe.id]);
                this.vm.services.messageService.add({ severity: 'success', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) envoyé(s) avec succès', life: 4000 });
                this.displayCreateOrder = false;
                this.displayConsultOrder = false;
                this.displaySendTovalidation = false;
                this.vm.displaySentToSignature = false;
                this.vm.displayComments = false;
                this.vm.displayActeRefused = false;
                let act = this.vm.listOrder.filter(act => act.id == userToSendMail[0].actId)[0];
                this.vm.listOrder = this.vm.listOrder.filter(act => act.id != userToSendMail[0].actId);
                act.status = 'En cours de signature';
                this.vm.listOrder.push(act);
            }
            else {
                this.vm.services.messageService.add({ severity: 'error', summary: 'Envoi d\'e-mails', detail: 'E-mail(s) non envoyé(s)', life: 4000 });
            }
        });
    }
    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }
    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }

    get displayComments(): boolean {
        return this.listAvocat.some(avocat => avocat.comment && avocat.comment.length > 0) || this.listSignataire.some(signatire => signatire.comment && signatire.comment.length > 0) || this.listSignataire.some(signatire => signatire.signatureComment && signatire.signatureComment.length > 0);
    }

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.add-form-actions button').each(function(index, el) {
                $(el).blur();
            });
        });
    }
}