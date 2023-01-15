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
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
Object.defineProperty(exports, "__esModule", { value: true });
const core_1 = require("@angular/core");
const OrdersViewModelMaster_1 = require("../view-models/bases/OrdersViewModelMaster");
const platform_browser_1 = require("@angular/platform-browser");
const signature_pad_1 = require("angular2-signaturepad/signature-pad");
const jsPDF = require("jspdf");
const pdf_lib_1 = require("pdf-lib");
let SignatureComponent = class SignatureComponent {
    constructor(sanitizer) {
        this.sanitizer = sanitizer;
        this.response = '';
        this.isChecked = false;
        this.filesToMerge = [];
        this.fileToDisplay = '';
        this.signaturePadOptions = {
            'minWidth': 5,
            'canvasWidth': 500,
            'canvasHeight': 300
        };
        //this.vm = new OrdersViewModelMaster(orderService, avocatService, documentService, signataireService);
    }
    addFilesToMerge(event) {
        if (this.filesToMerge.some(d => d == event)) {
            this.filesToMerge = this.filesToMerge.filter(f => f !== event);
        }
        else {
            this.filesToMerge.push(event);
        }
        this.createMergeFile(this.filesToMerge);
        //  this.fileToDisplay = this.sanitizer.bypassSecurityTrustResourceUrl("data:application/pdf;base64," + OrdersViewModelMaster.listDocument.filter(f => f.name == this.filesToMerge[0])[0].file + ".pdf")
    }
    createMergeFile(document) {
        if (!document.some(d => d == "3")) {
            document.splice(0, 0, '3');
        }
        let mergeFileQuery = this.vm.services.documentService.getMergedDocumentAsync(document);
        Promise.all([mergeFileQuery]).then(results => {
            this.fileToDisplay = this.sanitizer.bypassSecurityTrustResourceUrl("data:application/pdf;base64," + results[0]);
        });
    }
    createNewSignataireMethod() {
    }
    ngOnInit() {
        this.dragElement(document.getElementById("mydiv"));
    }
    saveSignataire() {
    }
    addSignature(event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = ((e) => {
                let base64str = e.target['result'].toString();
                this.signaturePad.clear();
                this.signaturePad.fromDataURL(base64str);
            });
            reader.readAsDataURL(event.target.files[0]);
        }
    }
    convertDataURIToBinary(dataURI) {
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
    drawComplete() {
        return __awaiter(this, void 0, void 0, function* () {
            const pdfDoc = yield pdf_lib_1.PDFDocument.load("" + this.fileToDisplay);
            // Embed the Helvetica font
            console.log(pdfDoc.saveAsBase64());
            // Get the first page of the document
            const pages = pdfDoc.getPages();
            const firstPage = pages[(pages.length) - 1];
            // Get the width and height of the first page
            const { height } = firstPage.getSize();
            const jpgImage = yield pdfDoc.embedJpg(this.signaturePad.toDataURL());
            // Draw a string of text diagonally across the first page
            firstPage.drawImage(jpgImage, {
                x: 5,
                y: height / 2 + 300,
            });
            // Serialize the PDFDocument to bytes (a Uint8Array)
            var doc = new jsPDF(this.fileToDisplay);
            doc;
            //doc.extractInfoFromBase64DataURI(this.signaturePad.toDataURL());
            var imgData = this.signaturePad.toDataURL();
            console.log(imgData);
            //doc.setFontSize(40);
            // doc.text(30, 20, 'Hello world!');
            doc.addImage(imgData, 'png', 15, 40, 180, 160);
            //doc.output('datauri');
            console.log(doc.output('datauri'));
        });
    }
    generatePDF(img) {
        //var options = { orientation: 'p', unit: 'mm', format: custom };
        //var doc = new jsPDF(options);
        //doc.addImage(img, 'JPEG', 0, 0, 100, 50);
    }
    getImgFromUrl(logo_url, callback) {
        var img = new Image();
        img.src = logo_url;
        img.onload = function () {
            callback(img);
        };
    }
    dragElement(elmnt) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        if (document.getElementById(elmnt.id + "header")) {
            /* if present, the header is where you move the DIV from:*/
            document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
        }
        else {
            /* otherwise, move the DIV from anywhere inside the DIV:*/
            elmnt.onmousedown = dragMouseDown;
        }
        function dragMouseDown(e) {
            e = e || window.event;
            e.preventDefault();
            // get the mouse cursor position at startup:
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            // call a function whenever the cursor moves:
            document.onmousemove = elementDrag;
        }
        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            // calculate the new cursor position:
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            // set the element's new position:
            elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
            elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
        }
        function closeDragElement() {
            /* stop moving when mouse button is released:*/
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }
    get listeDocuments() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.listDocument;
    }
    get mode() {
        return OrdersViewModelMaster_1.OrdersViewModelMaster.mode;
    }
};
__decorate([
    core_1.Input(),
    __metadata("design:type", Object)
], SignatureComponent.prototype, "vm", void 0);
__decorate([
    core_1.ViewChild(signature_pad_1.SignaturePad),
    __metadata("design:type", signature_pad_1.SignaturePad)
], SignatureComponent.prototype, "signaturePad", void 0);
SignatureComponent = __decorate([
    core_1.Component({
        selector: 'signature',
        templateUrl: './SignatureComponent.html',
        styleUrls: ['./OrdersComponent.css']
    }),
    __metadata("design:paramtypes", [platform_browser_1.DomSanitizer])
], SignatureComponent);
exports.SignatureComponent = SignatureComponent;
