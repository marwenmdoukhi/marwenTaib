import { Injectable } from "@angular/core";
import { ActeService } from "../../shared/services/acte.services";
import { AvocatService } from "../../shared/services/avocat.services";
import { DocumentService } from "../../shared/services/document.services";
import { SignataireService } from "../../shared/services/signataire.services";
import { SendMail } from "../../shared/services/sendMail.services";
import { MessageService } from 'primeng/api';
import { SignatureProcess } from "../../shared/services/signatureProcess.services";
import { CookieService } from 'ngx-cookie-service';
import { NgxSpinnerService } from "ngx-spinner";



@Injectable()
export class SignatureServiceInjector {
    constructor(
        public orderService: ActeService,
        public messageService: MessageService,
        public avocatService: AvocatService,
        public documentService: DocumentService,
        public signataireService: SignataireService,
        public sendMailToValidate: SendMail,
        public signatureProcessService: SignatureProcess,
        public cookiesService: CookieService,
        public spinner: NgxSpinnerService
    ) { }
}