import {
    AfterViewChecked,
    AfterViewInit,
    Component,
    ElementRef, HostListener,
    Input,
    OnDestroy,
    OnInit,
    ViewChild,
    ViewEncapsulation
} from '@angular/core';
import * as firebase from 'firebase';
import {MessageService} from 'primeng/primeng';
import {ActeService} from '../../../shared/services/acte.services';
import {Order} from '../../../shared/entities/order';
import {DocumentService} from '../../../shared/services/document.services';
import {SignataireService} from '../../../shared/services/signataire.services';
import {IOrdersViewModelMaster} from '../../order/view-models/interfaces/IOrdersViewModelMaster';
import {DomSanitizer, SafeResourceUrl} from '@angular/platform-browser';
import {SignaturePad} from 'angular2-signaturepad/signature-pad';
import {AvocatService} from '../../../shared/services/avocat.services';
import {NgxSpinnerService} from "ngx-spinner";


import {User} from "../../../shared/entities/user";
import {OrderOodrive} from '../../../shared/entities/OrderOodrive';
import {SignatureProcess} from '../../../shared/services/signatureProcess.services';
import {SendMail} from "../../../shared/services/sendMail.services";
import {WindowService} from '../../../shared/services/window.services';
import {Cookies} from '../../../shared/entities/cookies';
import {CookieService} from 'ngx-cookie-service';
import 'rxjs/add/observable/interval';
import {Observable, Subscription} from "rxjs";
import {ReCaptchaComponent} from "angular2-recaptcha";


declare const $: any;

@Component({
    selector: 'signature',
    templateUrl: './SignatureComponent.html',
    styleUrls: ['./SignatureComponent.css'],
    providers: [MessageService]
})

export class SignatureComponent implements OnInit, AfterViewInit , AfterViewChecked , OnDestroy {


    @Input() vm: IOrdersViewModelMaster;
    @ViewChild(SignaturePad) signaturePad: SignaturePad;
    @ViewChild("recaptcha", { read: ElementRef }) recaptcha: ElementRef;
    @ViewChild(ReCaptchaComponent) captcha: ReCaptchaComponent;
    @ViewChild('validActs') validActs: ElementRef;
    @ViewChild('consultActs') consultActs: ElementRef;



    displayCookies: boolean = true;
    displayCookiesPrametres: boolean = false;
    cookiesInofmration: Cookies;
    acctpePiwic: boolean = false;
    displayCookiesModal: boolean = false;
    signGriffe : boolean = true;
    signImage : boolean = false;


    whiteSignature: string ='data:image/png;base64,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'
    whitePadImage:string='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABQCAYAAAAwa2i1AAACBElEQVR4Xu3TgREAIAgDMdl/aLxzDD9s0JTO7u5xBAh8LTCG/nW/whF4AobuEQgEBAw9ULKIBAzdDxAICBh6oGQRCRi6HyAQEDD0QMkiEjB0P0AgIGDogZJFJGDofoBAQMDQAyWLSMDQ/QCBgIChB0oWkYCh+wECAQFDD5QsIgFD9wMEAgKGHihZRAKG7gcIBAQMPVCyiAQM3Q8QCAgYeqBkEQkYuh8gEBAw9EDJIhIwdD9AICBg6IGSRSRg6H6AQEDA0AMli0jA0P0AgYCAoQdKFpGAofsBAgEBQw+ULCIBQ/cDBAIChh4oWUQChu4HCAQEDD1QsogEDN0PEAgIGHqgZBEJGLofIBAQMPRAySISMHQ/QCAgYOiBkkUkYOh+gEBAwNADJYtIwND9AIGAgKEHShaRgKH7AQIBAUMPlCwiAUP3AwQCAoYeKFlEAobuBwgEBAw9ULKIBAzdDxAICBh6oGQRCRi6HyAQEDD0QMkiEjB0P0AgIGDogZJFJGDofoBAQMDQAyWLSMDQ/QCBgIChB0oWkYCh+wECAQFDD5QsIgFD9wMEAgKGHihZRAKG7gcIBAQMPVCyiAQM3Q8QCAgYeqBkEQkYuh8gEBAw9EDJIhIwdD9AICBg6IGSRSRg6H6AQEDA0AMli0jA0P0AgYCAoQdKFpGAofsBAgEBQw+ULCKBC9ZQPyDBk0OBAAAAAElFTkSuQmCC';
    whitePadFireFoxImage : string = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABQCAYAAAAwa2i1AAABAklEQVR4nO3TQQ0AIAzAwPk3DSoWEnqnoJ/OAb43rwOAfUaHAKNDgNEhwOgQYHQIMDoEGB0CjA4BRocAo0OA0SHA6BBgdAgwOgQYHQKMDgFGhwCjQ4DRIcDoEGB0CDA6BBgdAowOAUaHAKNDgNEhwOgQYHQIMDoEGB0CjA4BRocAo0OA0SHA6BBgdAgwOgQYHQKMDgFGhwCjQ4DRIcDoEGB0CDA6BBgdAowOAUaHAKNDgNEhwOgQYHQIMDoEGB0CjA4BRocAo0OA0SHA6BBgdAgwOgQYHQKMDgFGhwCjQ4DRIcDoEGB0CDA6BBgdAowOAUaHAKNDgNEhwOgQYHQIMDoEXJ5zWboGEV9VAAAAAElFTkSuQmCC'
    response: string = '';
    isChecked: boolean = false;
    hideComment: boolean = true;
    acceptCondition: boolean = false;
    filesToMerge: string[] = [];
    fileToDisplay: SafeResourceUrl;
    userConnected: User;
    currentActe: Order;
    actId: string;
    private allAvocat: any[];
    private listDocument: any[];
    private currentAct: Order;
    private allSignataire: any[];
    displayDialogForOtpCode: boolean = false;
    exit : boolean = false;
    imgData: string = "";
    refuseSign: boolean = false;
    order: OrderOodrive = new OrderOodrive();
    displayOtpAuthentification: boolean = true;
    wrongOtp: boolean = false;
    creationOrderError: boolean = false;
    tries: number = 2;
    displayResendOtp: boolean = false;
    timeLeft: number = 900;
    timetosign : number = 900;
    display;
    displayTimer;
    interval;
    isUserConnected: boolean = false;
    phoneNumberMasked: string;
    displayModalRefusSignature: boolean = false;
    displayModalDocumentSigne: boolean = false;
    displayDialogForPad: boolean = false;
    hasSentCode: boolean = false;
    clicked : boolean = false;
    otpTries : number = 5;
    disabled : boolean = false;
    step1 : boolean = true;
    clickedSigning : boolean = false;
    signingNow : boolean = false;
    cansign :boolean = true;
    timer : any;
    signingStatus : boolean = true;
    destroy : boolean = true;
    siteId : string = '6LdkST4cAAAAAJuXaLdzg06l2eNae-YF3MI5zT3P';
    captchaStep : boolean = true;
signingSubscription: Subscription;

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
    displayCGU: boolean = false;
    displayRefuseCGU: boolean = false;
    displayPC: boolean = false;
    displayRefusePC: boolean = false;
    env: any = {};
    id : any;

    private signaturePadOptions: Object = {
        'canvasWidth': 250,
        'canvasHeight': 90,
        'penColor': 'black',
        'backgroundColor': 'white',
    };

    constructor(public orderService: ActeService, private cookiesService: CookieService, private windowService: WindowService, private sanitizer: DomSanitizer, private actService: ActeService, private documentService: DocumentService, private signataireService: SignataireService, private avocatService: AvocatService, private signatureProcessService: SignatureProcess, private sendMail: SendMail, private messageService: MessageService, private spinner: NgxSpinnerService) {
    }

    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function() {

            $('.signature-action-button button').each(function(index, el) {
                $(el).blur();
            });
        });
        window.onbeforeunload = () => this.ngOnDestroy();
    }

    handleCorrectCaptcha(event){
        if (this.captcha.getResponse()){
            this.clicked = true;
        }
    }

    ngOnInit() {
        const parameters = new URLSearchParams(window.location.search);
        this.id = parameters.get("act");
        this.getData();
        firebase.initializeApp(this.firebaseConfig);
        this.windowRef = this.windowService.windowRef;
        let getCookies = this.orderService.getCookies();
        Promise.all([getCookies]).then(results => {
            if (this.cookiesService.get('assp-cookies')) {
                this.displayCookiesModal = results[0];
            }else{
                this.displayCookiesModal=true;
            }
        });
    }
    ngAfterViewChecked(): void {
        // console.log(this.currentAct)
    }
    async ngOnDestroy() {
        if (this.destroy === true){
            let releaseSignatory = this.signatureProcessService.updateSigningInProgress(this.currentAct, false);
            await Promise.all([releaseSignatory]).then(result => {
            })
            console.log('done')
        }
    }

    @HostListener('window:unload', [ '$event' ])
    unloadHandler(event) {
        let releaseSignatory = this.signatureProcessService.updateSigningInProgress(this.currentAct, false);
        Promise.all([releaseSignatory]).then(result => {
        })
        console.log(event);
    }

    checkBrowser(): string {
        switch (true) {
            case window.navigator.userAgent.toLowerCase().indexOf("edge") > -1: return "edge";
            case window.navigator.userAgent.toLowerCase().indexOf("edg") > -1: return "chromium based edge (dev or canary)";
            case window.navigator.userAgent.toLowerCase().indexOf("opr") > -1: return "opera";
            case window.navigator.userAgent.toLowerCase().indexOf("chrome") > -1:
                return "chrome";
            case window.navigator.userAgent.toLowerCase().indexOf("trident") > -1:
                return "ie";
            case window.navigator.userAgent.toLowerCase().indexOf("firefox") > -1:
                return "firefox";
            case window.navigator.userAgent.toLowerCase().indexOf("safari") > -1:
                return "safari";
            default:
                return "other";
        }
    }

    get ifCookiesSaved() {
        return !!this.cookiesService.get('assp-cookies');
    }

    consultShowModals(param: any) {
        if (param === "cgu") {
            this.displayCGU = this.env.showModalCgu;
        } else if (param === "pc") {
            this.displayPC = this.env.showModalPc;
        }


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




    //Envoie du code Otp
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
                    this.captchaStep= true;
                    this.captcha.reset();
        });
    }
    get activatRefuseButton(): boolean {
        return !this.acceptCondition || (this.acceptCondition && !this.userConnected.comment && this.userConnected.comment.length == 0);
    }

    addSignature(event: any) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = ((e) => {
                let base64str = e.target['result'].toString();
                this.signaturePad.clear();
                this.signaturePad.fromDataURL(base64str);
            });
            reader.readAsDataURL(event.target.files[0]);
            this.signaturePad.off();
        }
    }

    convertDataURIToBinary(dataURI: any) {
        var base64Index = dataURI.indexOf(';base64,') + ';base64,'.length;
        var base64 = dataURI.substring(base64Index);
        var raw = window.atob(base64);
        var rawLength = raw.length;
        var array = new Uint8Array(new ArrayBuffer(rawLength));

        for (let i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }
        return array;
    }
    onKey(event: any){
        event.target.value = event.target.value.charAt(0).toLocaleUpperCase()+event.target.value.slice(1);
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

    close(){
        let releaseSignatory = this.signatureProcessService.updateSigningInProgress(this.currentAct , false);
        Promise.all([releaseSignatory]).then(result => {
            console.log(result)
        })
        this.displayDialogForPad = false;
    }


    signer() {
        this.imgData = this.signaturePad.toDataURL('image/png', 1);
        this.compressImage().then(compressed => {
            this.imgData = compressed.toString();
        });
        if (this.acceptCondition) {
            this.displayDialogForOtpCode = true;
            this.order = new OrderOodrive();
            this.order.folderName = this.currentAct.folderNumber;
            if (!this.imgData || this.imgData == this.whitePadImage || this.imgData == this.whitePadFireFoxImage) {
                console.log('here');
                this.imgData = this.whiteSignature;
            }
            this.order.image = this.imgData;

            let signedQuery = this.signatureProcessService.createOrder(this.order);
            let spinner = this.spinner.show();
            Promise.all([signedQuery.catch(err => {
                this.spinner.hide();
                this.creationOrderError = true;
                this.displayDialogForOtpCode = false;
                throw err
            }) , spinner]).then(results => {
                this.spinner.hide();
                console.log(results[0])

                if (results[0] == 'error order creation') {
                    this.creationOrderError = true;
                    this.displayDialogForOtpCode = false;
                } else {
                    this.order.orderId = '' + results;
                    this.phoneNumber = "" + this.userConnected.phoneNumber;
                }
            });
        }
        else {
            this.messageService.add({ severity: 'error', summary: 'Signature', detail: 'Veuillez confirmer que vous avez lu le document en totalité - Cocher la case', life: 4000 });
        }
    }

    refresh(): void {
        window.location.reload();
    }

    validateOtp() {
        if (this.order.otp) {
            this.order.orderId=(this.order.orderId).replace(',','');
            let validateOtp = this.signatureProcessService.validateOodrive(this.order);
            Promise.all([validateOtp.catch(err => {
                this.spinner.hide();
                this.creationOrderError = true;
                this.displayDialogForOtpCode = false;
                throw err
            }) , this.spinner.show()]).then(results => {
                this.spinner.hide();
                if (results[0] == 'Mauvais code : encore 2 essai(s)') {
                    this.displayDialogForOtpCode = true;
                    this.wrongOtp = true;
                    this.tries = 2;
                } else if (results[0] == 'Mauvais code : encore 1 essai(s)') {
                    this.displayDialogForOtpCode = true;
                    this.wrongOtp = true;
                    this.tries = 1;
                } else if (results[0] == 'Nombre d\'essai atteint') {
                    this.tries = 0;
                    this.displayDialogForOtpCode = false;
                    this.displayResendOtp = true;
                    this.wrongOtp = true;
                    let releaseSignatory = this.signatureProcessService.updateSigningInProgress(this.currentAct ,false);
                    Promise.all([releaseSignatory]).then(result=>{

                    })
                    } else if (results[0] == 'file signed created') {
                    this.sendSignNotification();
                    this.displayModalDocumentSigne = true;
                    this.displayDialogForOtpCode = false;
                    this.acceptCondition = false;
                    this.displayDialogForOtpCode = false;
                } else {
                    this.creationOrderError = true;
                    this.displayDialogForOtpCode = false;
                }
            });
        }
        else {
            this.messageService.add({ severity: 'error', summary: 'Signature', detail: 'Veuillez renseigner le code SMS reçu de la part de Certeurope', life: 4000 });
        }
    }

    refuseSigner() {
        if (!this.acceptCondition) {
            this.messageService.add({ severity: 'error', summary: 'Signature', detail: 'Veuillez confirmer que vous avez lu le document en totalité - Cocher la case', life: 4000 });
        }
        if (!this.userConnected["signatureComment"] || this.userConnected["signatureComment"].length == 0) {
            this.messageService.add({ severity: 'error', summary: 'Signature', detail: 'Veuillez ajouter un commentaire de refus', life: 4000 });
        }
        else if (this.userConnected["signatureComment"] && this.userConnected["signatureComment"].length >= 0 && this.acceptCondition) {
            this.userConnected["actId"] = this.actId;
            let signedQuery = this.signatureProcessService.refuseSignAct(this.userConnected);
            this.displayModalRefusSignature = true;
            Promise.all([signedQuery]).then(results => {

            });
        }

    }


    filterActs(event: any) {
        if (event.target.id == 'valid') {
            this.signaturePad.clear();
            this.signGriffe = true;
            this.validActs.nativeElement.classList.add('active-filter');
            this.validActs.nativeElement.classList.remove('inactive-filter');
            this.consultActs.nativeElement.classList.add('inactive-filter');
        }

        else if (event.target.id == 'consult') {
            this.signGriffe = false;
            this.signaturePad.clear();
            this.validActs.nativeElement.classList.add('inactive-filter');
        }
    }


    quitter() {
        this.destroy = false;
        let releaseSignatory = this.signatureProcessService.updateSigningInProgress(this.currentAct , false);
        Promise.all([releaseSignatory]).then(result => {
            console.log(result[0]);
            if (result[0] == true){
                setTimeout(function(){
                    window.location.href = location.origin+'/logout';
                    window.location.replace('https://www.cnb.avocat.fr/');
                    }, 500);
            }
        })
    }


    compressImage() {
        return new Promise((resolve, reject) => {
            const img = new Image();
            var canvas = document.createElement('canvas'),
                ctx = canvas.getContext("2d"),
                oc = document.createElement('canvas'),
                octx = oc.getContext('2d');
            img.src = this.imgData;
            img.onload = () => {
                canvas.width = 120;
                canvas.height = canvas.width * img.height / img.width;
                var currentWidhAndHeight = {
                    width: Math.floor(img.width * 0.5),
                    height: Math.floor(img.height * 0.5)
                }
                oc.width = currentWidhAndHeight.width;
                oc.height = currentWidhAndHeight.height;
                octx.drawImage(img, 0, 0, currentWidhAndHeight.width, currentWidhAndHeight.height);
                while (currentWidhAndHeight.width * 0.5 > 200) {
                    currentWidhAndHeight = {
                        width: Math.floor(currentWidhAndHeight.width * 0.5),
                        height: Math.floor(currentWidhAndHeight.height * 0.5)
                    };
                    octx.drawImage(oc, 0, 0, currentWidhAndHeight.width * 2, currentWidhAndHeight.height * 2, 0, 0, currentWidhAndHeight.width, currentWidhAndHeight.height);
                }
                ctx.drawImage(oc, 0, 0, currentWidhAndHeight.width, currentWidhAndHeight.height, 0, 0, canvas.width, canvas.height);
                resolve(canvas.toDataURL());
            }
            img.onerror = error => reject(error);
        })
    }

    drawComplete() {
        this.imgData = this.signaturePad.toDataURL('image/png', 1);
        this.compressImage().then(compressed => {
            this.imgData = compressed.toString();
        })
    }

    sendSignNotification() {
        if (this.userConnected["unitUser"]) {
            this.userConnected["unitUser"] = null;
        }
        let validatUdser = this.userConnected;
        const parameters = new URLSearchParams(window.location.search);
        this.actId = parameters.get("act");
        validatUdser["actId"] = this.actId;
        let sendSignNotification1 = this.sendMail.sendSignNotification(validatUdser);
        Promise.all([sendSignNotification1]).then(results => {
        });
    }
    getImgFromUrl(logo_url: any, callback: any) {
        var img = new Image();
        img.src = logo_url;
        img.onload = function () {
            callback(img);
        };
    }

    getData() {
        this.startTimer();
        const parameters = new URLSearchParams(window.location.search);
        this.actId = parameters.get("act");
        let act = parameters.get("act");
        // let orderQuery = this.actService.getActByIdAsync(parseInt(act));
        let sigataireQuery = this.signataireService.getAllSignataireAsync();
        let avocatQuery = this.avocatService.getAllAvocatsAsync();
        let userQuery = this.actService.getUserconnectedAsyncOtp(act);
        // let signedQuery = this.documentService.getMergedDocument(['' + act]);
        let envQuery = this.orderService.getEnvVariables();
        Promise.all([userQuery,sigataireQuery, avocatQuery,envQuery,this.spinner.show()]).then(results => {
            this.spinner.hide();
            this.userConnected = results[0];
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
            this.phoneNumber = "" +this.userConnected.codeCountry+this.userConnected.phoneNumber;
            this.phoneNumberMasked = '**'+this.userConnected.phoneNumber.toString().replace(/^.{6}/g, '******');
            this.env = results[3];
            if (this.displayCookiesModal == false) {
                this.consultShowModals('cgu');
            }
        });
    }

    getSigningData(){
        const parameters = new URLSearchParams(window.location.search);
        this.actId = parameters.get("act");
        let act = parameters.get("act");
        let orderQuery = this.actService.getActByIdAsync(parseInt(act));
        let signedQuery = this.documentService.getMergedDocument(['' + act]);
        Promise.all([orderQuery, signedQuery,this.spinner.show()]).then(results => {
            this.spinner.hide();
            this.currentAct = results[0];
            this.fileToDisplay = this.convertDataURIToBinary("data:application/pdf;base64," + results[1]);
        });

        }

    dragElement(elmnt: any) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        if (document.getElementById(elmnt.id + "header")) {
            document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
        } else {
            elmnt.onmousedown = dragMouseDown;
        }

        function dragMouseDown(e: any) {
            e = e || window.event;
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e: any) {
            e = e || window.event;
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
            elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    checkSigning(){
        let signingNow = this.signatureProcessService.getCurrentSignings(this.currentAct);
        Promise.all([signingNow]).then(result=> {
            if (result[0] == "wrn-0200" && this.acceptCondition == true){
                this.signingNow = true;
                this.displayDialogForPad = false;
            }else{
                this.signingNow= false;
                console.log(this.order.orderId)
                if(this.acceptCondition==true && this.order.orderId == undefined){
                    this.displayDialogForPad=true;
                }
                this.signingSubscription.unsubscribe();
            }
        })
    }

    checkConditionForPad(){
        let signingNow = this.signatureProcessService.getCurrentSignings(this.currentAct);
        Promise.all([signingNow]).then(result => {
            if (result[0] == "wrn-0200" && this.acceptCondition == true){
                this.displayDialogForPad = false;
                this.signingNow = true;
            }else{
                this.signingNow= false;
                if(this.acceptCondition==true){
                    this.displayDialogForPad=true;
                }
            }
        })
        this.timer = Observable.interval(15000);
        this.signingSubscription = this.timer.subscribe(t => {
            this.checkSigning();
        });
    }

    // last check if user can sign act
    lastCheckSigning(){

        let signingNow = this.signatureProcessService.getCurrentSignings(this.currentAct);

        Promise.all([signingNow]).then(result=> {
            if (result[0] == "wrn-0200" && this.acceptCondition == true){
                this.signingNow = true;
                this.displayDialogForPad = false;
            }
            else {
                this.displayDialogForPad=false;
                this.signingNow= false;
                this.signer();
            }
        })

        this.timer = Observable.interval(15000);
        this.signingSubscription = this.timer.subscribe(t => {
            this.checkSigning();
        });
    }
}