import {animate, Component, Input, OnDestroy, OnInit, state, style, transition, trigger} from '@angular/core';
import {MessageService} from 'primeng/api';
import {OrdersViewModelMaster} from '../view-models/bases/OrdersViewModelMaster';
import {Order} from '../../../shared/entities/order';
import {IOrdersViewModelMaster} from '../view-models/interfaces/IOrdersViewModelMaster';
import {DomSanitizer} from '@angular/platform-browser';
import {Signataire} from '../../../shared/entities/signataire';
import {Document} from "../../../shared/entities/document";
import {Avocat} from "../../../shared/entities/avocat";
import {User} from "../../../shared/entities/user";
import {ActeService} from "../../../shared/services/acte.services";
import * as moment from "moment";

declare const $: any;

@Component({
    selector: 'consultSignedActComponent',
    templateUrl: './consultSignedActComponent.html',
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

export class consultSignedActComponent implements OnInit , OnDestroy {
    @Input() vm: IOrdersViewModelMaster;
    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    canSendActToValidation: boolean = false;
    date: string;
    randomString : string;

    constructor(private sanitized: DomSanitizer,private acteService: ActeService) {
    }

    ngOnInit(): void {
        window.scrollTo(0, 0);
        this.date=moment(this.currentActe.signingDate,"DD/MM/YYYY").format("YYYYMMDD");
        // this.downloadProof();
    }

    ngOnDestroy(): void {
        let deleteProof = this.vm.services.orderService.deleteProofFile('/tmp/' + this.currentActe.folderNumber + "Proof.pdf");
        Promise.all([deleteProof]).then(result=>{
            console.log('proof file deleted')
        })
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

    getFileSize(name: String): number {
        return (this.listDocument.filter(doc => doc.name == name)[0].size) / 1000000;
    }
    
    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }
    downloadPDF(file: any): string {
        if (this.currentActe.status != 'Signee'){
            return '/documents/'+this.currentActe.id +'/' + file.name.replace(/\s/g, '') + '.pdf';
        }else{
            let downloadSign = this.acteService.downloadSigne(this.currentActe.id);
            Promise.all([downloadSign]).then(results => {
                console.log(results)
                this.randomString = results[0][1];
                console.log(this.randomString)
                let a = document.createElement("a");
                a.href = '/documents/' + this.currentActe.folderNumber+this.randomString + 'ForSigning.pdf';
                a.download = this.currentActe.name+'-Signé';
                a.click();
                let deleteSign = this.acteService.deleteSigne(this.currentActe , this.randomString);


            });

        }
    }

    viewPdf(file: any) {
        let pdfWindow = window.open("");
        let downloadSign = this.acteService.downloadSigne(this.currentActe.id);
        if (this.currentActe.status != 'Signee') {
            pdfWindow.document.write("<iframe width='100%' height='100%' src='/documents/" + this.currentActe.id + '/' + file.name.replace(/\s/g, '') + ".pdf'></iframe>");
        } else {
            Promise.all([downloadSign]).then(results => {
                this.randomString = results[0][1];
                pdfWindow.document.write("<iframe id='test' width='100%' height='100%' src='/documents/" + this.currentActe.folderNumber +this.randomString+ "ForSigning.pdf' [attr.title]='ffff.pdf'></iframe>");
                let iframe = pdfWindow.document.getElementById('test');
                let iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iframeDoc.readyState == 'complete') {
                    setTimeout(() =>  this.acteService.deleteSigne(this.currentActe , this.randomString), 500);
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


    isAvocat(item): boolean {
        return this.listAvocat.some(av => av.actId == item.actId && av.name == item.name)
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

    get displayConsultSignedActComponent():boolean{
        return OrdersViewModelMaster.displayConsultSignedActComponent;
    }

    set displayConsultSignedActComponent(displayConsultSignedActComponent:boolean){
        OrdersViewModelMaster.displayConsultSignedActComponent = displayConsultSignedActComponent;
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

    dateValidatedOrRefused(item: any) {
        if (item.validator && !item.actValidated && item.comment) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:red'> Validation refusée le</span> " + item.validatedAt);
        }
        else if ((item.validator && item.actValidated && item.validatedAt)) {
            return this.sanitized.bypassSecurityTrustHtml("<span style='color:green'> Validé le</span> " + item.validatedAt);
        }
        else {
            return "";
        }
    }

    get displayComments(): boolean {
        return this.listAvocat.some(avocat => avocat.comment && avocat.comment.length > 0) || this.listSignataire.some(signatire => signatire.comment && signatire.comment.length > 0);
    }
    downloadProof(){
        let proof = this.acteService.downloadProof(this.currentActe.id);
        Promise.all([proof]).then(results => {
        });
        return 'done';
    }
    viewProof() {
        let proof = this.acteService.downloadProof(this.currentActe.id);
        Promise.all([proof]).then(results => {
            let fileQuery = this.vm.services.orderService.getBase64ProofFile("/tmp/" + this.currentActe.folderNumber + "Proof.pdf");
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let path = 'data:application/pdf;base64,' + results;
                    let pdfWindow = window.open("");
                    pdfWindow.document.write("<iframe  width='100%' height='100%' src=" + path + "></iframe>");
                    let deleteProof = this.vm.services.orderService.deleteProofFile('/tmp/' + this.currentActe.folderNumber + "Proof.pdf");
                    Promise.all([deleteProof]).then(result=>{
                        console.log('proof file deleted')
                    })
                }
            });
        });
    }
    get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster.displaySearchAvocat;
    }

    set displaySearchAvocat(value: boolean) {
        OrdersViewModelMaster.displaySearchAvocat = value;
    }
    downloadProofFile(){
        let proof = this.acteService.downloadProof(this.currentActe.id);
        Promise.all([proof]).then(results => {
            let fileQuery = this.vm.services.orderService.getBase64ProofFile('/tmp/' + this.currentActe.folderNumber + "Proof.pdf");
            Promise.all([fileQuery]).then(results => {
                if (results) {
                    let nameProof= 'Dossier de preuve-'+this.currentActe.name+'-'+this.date;
                    const linkSource = 'data:application/pdf;base64,' + results;
                    const downloadLink = document.createElement("a");
                    const fileName =nameProof.replace(/[$_ /:]/g,'_');
                    downloadLink.href = linkSource;
                    downloadLink.download = fileName;
                    downloadLink.click();
                    let deleteProof = this.vm.services.orderService.deleteProofFile('/tmp/' + this.currentActe.folderNumber + "Proof.pdf");
                    Promise.all([deleteProof]).then(result=>{
                        console.log('proof file deleted')
                    })
                }
            });
        });
    }
}

