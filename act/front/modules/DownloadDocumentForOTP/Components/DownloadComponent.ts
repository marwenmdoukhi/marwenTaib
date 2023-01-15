import {AfterViewInit, Component, Input, OnInit, ViewChild} from '@angular/core';
import {MessageService} from 'primeng/api';
import {ActeService} from '../../../shared/services/acte.services';
import {User} from "../../../shared/entities/user";
import {DocumentService} from '../../../shared/services/document.services';
import {Document} from '../../../shared/entities/document';
import {Order} from '../../../shared/entities/order';
import {SendMail} from "../../../shared/services/sendMail.services";
import {WindowService} from '../../../shared/services/window.services';
import * as firebase from 'firebase';
import {CookieService} from 'ngx-cookie-service';
import {Cookies} from '../../../shared/entities/cookies';
import {NgxSpinnerService} from "ngx-spinner";
import {IOrdersViewModelMaster} from "../../order/view-models/interfaces/IOrdersViewModelMaster";
import * as moment from "moment"
import {ReCaptchaComponent} from "angular2-recaptcha";
import {SignatureProcess} from '../../../shared/services/signatureProcess.services';




declare const $: any;

@Component({
    selector: 'download',
    templateUrl: './DownloadComponent.html',
    providers: [MessageService],
    styleUrls: ['./DownloadComponent.css'],
})

export class DownloadComponent implements OnInit, AfterViewInit {

    @Input() vm: IOrdersViewModelMaster;
    @ViewChild(ReCaptchaComponent) captcha: ReCaptchaComponent;
    displayCookies: boolean = true;
    displayCookiesPrametres: boolean = false;
    cookiesInofmration: Cookies;
    acctpePiwic: boolean = false;
    displayCookiesModal: boolean = false;


    showMenuCouncel: boolean = false;
    showMenuSignatory: boolean = false;
    timeLeft: number = 900;
    display;
    interval;
    showMenuAction: boolean = false;
    acceptCondition: boolean = false;
    commentRefuseAct: string = "";
    userConnected: User;
    actId: string;
    timestamp : string;
    listDocument: Document[];
    refuseActe: boolean;
    validatedClicked: boolean = false;
    currentActe: Order;
    displayOtpAuthentification: boolean = true;
    isUserConnected: boolean = false;
    buttonAnnuler: boolean = false;
    hasSentCode: boolean = false;
    clicked : boolean = false;
    document : string;
    test :string;
    exist : boolean = false;
    proof : boolean = false;
    otpTries : number = 5;
    disabled : boolean = false;
    step1 : boolean = true;
    randomString : string;
    captchaStep : boolean = true;
    siteId : string = '6LdkST4cAAAAAJuXaLdzg06l2eNae-YF3MI5zT3P';



    firebaseConfig = {
        apiKey: "AIzaSyAs2YO7jXAUI0Nvw_dtPS-FLzaLqattlvo",
        authDomain: "actesssp.firebaseapp.com",
        databaseURL: "https://actesssp.firebaseio.com",
        projectId: "actesssp",
        storageBucket: "actesssp.appspot.com",
        messagingSenderId: "89678324047",
        appId: "1:89678324047:web:d4ce4fd85a7e5f6ddce53a"
    };

    phoneNumber: string;
    windowRef: any;
    verificationCode: string;
    user: any;
    phoneNumberMasked: string;
    displayCGU: boolean = false;
    displayRefuseCGU: boolean = false;
    displayPC: boolean = false;
    displayRefusePC: boolean = false;
    env: any = {};
    date: string;
    id : any;


    constructor(private cookiesService: CookieService,private messageService: MessageService, private windowService: WindowService, private userService: ActeService, public documentService: DocumentService, private sendMail: SendMail, public orderService: ActeService, private spinner: NgxSpinnerService , private signatureProcessService: SignatureProcess) {
    }

    ngOnInit(): void {
        const parameters = new URLSearchParams(window.location.search);
        this.id = parameters.get("act");
        this.getData();
        window.scrollTo(0, 0);
        firebase.initializeApp(this.firebaseConfig);
        this.windowRef = this.windowService.windowRef;
        let array = [this.actId , this.timestamp]
        let getCookies = this.orderService.getCookies();
        let downloadOtp = this.documentService.getSignedDocumentOtp(array)
        Promise.all([getCookies]).then(results => {
            if ((this.cookiesService.get('assp-cookies'))) {
                this.displayCookiesModal = results[0];
            }else{
                this.displayCookiesModal=true;
            }
        });

        Promise.all([downloadOtp]).then(result => {
            this.document = result[0];
        });


    }


    handleCorrectCaptcha(event){
        if (this.captcha.getResponse()){
            this.clicked = true;
        }
    }


    checkBrowser(): string {
        switch (true) {
            case window.navigator.userAgent.toLowerCase().indexOf("edge") > -1: return "edge";
            case window.navigator.userAgent.toLowerCase().indexOf("edg") > -1: return "chromium based edge (dev or canary)";
            case window.navigator.userAgent.toLowerCase().indexOf("opr") > -1: return "opera";
            case window.navigator.userAgent.toLowerCase().indexOf("chrome") > -1: return "chrome";
            case window.navigator.userAgent.toLowerCase().indexOf("trident") > -1: return "ie";
            case window.navigator.userAgent.toLowerCase().indexOf("firefox") > -1: return "firefox";
            case window.navigator.userAgent.toLowerCase().indexOf("safari") > -1: return "safari";
            default: return "other";
        }
    }
    get ifCookiesSaved() {
        return !!this.cookiesService.get('assp-cookies');
    }

    onKey(event: any){
        event.target.value = event.target.value.charAt(0).toLocaleUpperCase()+event.target.value.slice(1);
    }


    saveCookies() {
        if (this.acctpePiwic == false) {
            this.cookiesService.delete('assp-cookies');
            this.displayCookies = false;
            this.displayCookiesModal = false;
            this.displayCookiesPrametres = false;
        } else {

            this.cookiesInofmration = new Cookies();
            this.cookiesInofmration.date = new Date().toUTCString();
            this.cookiesInofmration.guid = this.createGuid();
            this.cookiesInofmration.navigateur = this.checkBrowser();
            this.cookiesInofmration.piwikIgnore = this.acctpePiwic;
            let saveCookies = this.orderService.postCookies(this.cookiesInofmration);
            Promise.all([saveCookies]).then(results => {
                this.cookiesService.set('assp-cookies', JSON.stringify(this.cookiesInofmration), 365, null, null, null);
                this.displayCookies = false;
                this.displayCookiesModal = false;
                this.displayCookiesPrametres = false;
            });
        }
    }
    getCookies() {
        this.cookiesService.get('');
    }
    createGuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }


    redirect() {
        window.location.href = 'https://www.cnb.avocat.fr/';
    }
    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.signature-action-button button').each(function(index, el) {
                $(el).blur();
            });
        });
    }

    verifyLoginCode() {
        let validateOtp = this.signatureProcessService.validateOtp(this.id , this.verificationCode)
        Promise.all([validateOtp]).then(result=>{
            if (result[0] == true){
                this.isUserConnected = true;
                this.getSigningData();
            }
        }).catch(error=>{
            this.messageService.add({ severity: 'error', summary: 'SMS', detail: "merci de verifier votre code otp ", life: 4000 });
            this.isUserConnected = false;
            this.verificationCode = null;
            this.step1 = true;
            this.captchaStep = true;
            this.captcha.reset();
        });
    }


    startTimer() {
        this.interval = setInterval(() => {
            if (this.timeLeft > 0) {
                this.timeLeft--;
            }
            this.display = this.transform(this.timeLeft)
        }, 1000)
    }
    transform(value : number) :string{
        const minutes :number = Math.floor(value /60);
        return minutes + ':'+ (value - minutes * 60);
    }

    sendLoginCode() {
        this.hasSentCode = true;
        if (this.clicked){
            this.timeLeft=900;
            this.clicked = false;
            this.captchaStep = false;
        }
        this.step1 = false;
        let generateOtp = this.signatureProcessService.generateOtp(this.id , this.userConnected)
        Promise.all([generateOtp]).then(result=>{
            this.windowRef.confirmationResult = result;
        }).catch(error=>{
            this.messageService.add({ severity: 'error', summary: 'SMS', detail: error, life: 4000 });
        });
    }
    quitter() {
        window.location.href = location.origin+'/logout';
        window.location.href = 'https://www.cnb.avocat.fr/';
    }


    downloadPDF() {
        let downloadSign = this.orderService.downloadSigne(this.currentActe.id);
        Promise.all([downloadSign , this.spinner.show()]).then(results => {
            this.randomString = results[0][1];
            let a = document.createElement("a");
            console.log(results);
            console.log('/documents/' + this.document+this.randomString+ 'ForSigning.pdf')
            a.href = '/documents/' + this.document+this.randomString+ 'ForSigning.pdf';
            a.download = this.currentActe.folderNumber;
            a.click();
            this.spinner.hide();
            this.exist = true;
            let deleteSign = this.orderService.deleteSigne(this.currentActe , this.randomString);
        });
    }
    consultShowModals(param: any) {
        if (param === "cgu") {
            this.displayCGU = this.env.showModalCgu;
        } else if (param === "pc") {
            this.displayPC = this.env.showModalPc;
        }


    }


    async getData(): Promise<void> {
        this.startTimer();
        const parameters = new URLSearchParams(window.location.search);
        this.actId = parameters.get("act");
        this.timestamp = parameters.get("timestamp");
        let userQuery = this.userService.getUserconnectedAsyncOtp(this.actId);
        // let orderQuery = this.orderService.getActByIdAsync(parseInt(this.actId));
        let envQuery = this.orderService.getEnvVariables();

        Promise.all([userQuery,envQuery,this.spinner.show()]).then(results => {
            results[0].comment = "";
            this.userConnected = results[0];
            // this.currentActe = results[1];
            this.phoneNumber = "" + this.userConnected.codeCountry + this.userConnected.phoneNumber;
            this.phoneNumberMasked = '***' + this.userConnected.phoneNumber.toString().replace(/^.{6}/g, '******');
            this.env = results[1];
            this.spinner.hide();
            if (this.displayCookiesModal == false) {
                this.consultShowModals('cgu');
            }
        });
        //this.downloadProof();
    }

    getSigningData(){
        const parameters = new URLSearchParams(window.location.search);
        this.actId = parameters.get("act");
        let act = parameters.get("act");
        let orderQuery = this.orderService.getActByIdAsync(parseInt(act));
        Promise.all([orderQuery]).then(results => {
            this.currentActe = results[0];
            this.date = moment(this.currentActe.signingDate, "DD/MM/YYYY").format("YYYYMMDD");
            // let actsDocuments = this.documentService.getDocumentsForAct([this.actId]);
            // Promise.all([actsDocuments,this.spinner.show()]).then(documentsResult => {
            //     this.spinner.hide();
            //     this.listDocument = documentsResult[0];
            //
            //     this.listDocument = this.listDocument.sort((a, b) => (a.position > b.position) ? 1 : ((b.position > a.position) ? -1 : 0));
            // })
        });

    }


    downloadProof() {
        let proof = this.orderService.downloadProof(parseInt(this.actId));
        Promise.all([proof]).then(results => {
            console.log(results);
        });
        return 'done';
    }

    downloadProofFilee() {
        let proofFile = this.userService.downloadProof(this.currentActe.id);
        Promise.all([proofFile]).then(results => {
            let fileQuery = this.orderService.getBase64ProofFile('/tmp/' + this.document + "Proof.pdf");
            Promise.all([fileQuery, this.spinner.show()]).then(results => {
                if (results) {
                    let nameProof = 'Dossier de preuve-' + this.currentActe.name + '-' + this.date;
                    const linkSource = 'data:application/pdf;base64,' + results;
                    const downloadLink = document.createElement("a");
                    const fileName = nameProof.replace(/[$_ /:]/g, '_');
                    console.log(linkSource);
                    downloadLink.href = linkSource;
                    downloadLink.download = fileName;
                    downloadLink.click();
                    this.spinner.hide();
                    this.proof = true;
                    // let deleteProof = this.orderService.deleteProofFile('/tmp/' + this.document + "Proof.pdf");
                    // Promise.all([deleteProof]).then(result => {
                    //     console.log('proof file deleted')
                    // })
                }
            });
        });
    }
    downloadProofFile() {
        this.userService.downloadProof(this.currentActe.id).then((results)=>{
            this.spinner.show();
            this.orderService.getBase64ProofFile('/tmp/' + this.document + "Proof.pdf").then((fileQuery)=>{
                let nameProof = 'Dossier de preuve-' + this.currentActe.name + '-' + this.date;
                const linkSource = 'data:application/pdf;base64,' + fileQuery;
                const downloadLink = document.createElement("a");
                const fileName = nameProof.replace(/[$_ /:]/g, '_');
                downloadLink.href = linkSource;
                downloadLink.download = fileName;
                downloadLink.click();
                this.spinner.hide();
                this.proof = true;
                this.orderService.deleteProofFile('/tmp/' + this.document + "Proof.pdf").then((deleteProof)=>{
                    console.log('proof file deleted')
                })
            });
        });
    }
}