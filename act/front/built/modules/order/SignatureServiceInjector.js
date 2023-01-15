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
const acte_services_1 = require("../../shared/services/acte.services");
const avocat_services_1 = require("../../shared/services/avocat.services");
const document_services_1 = require("../../shared/services/document.services");
const signataire_services_1 = require("../../shared/services/signataire.services");
let SignatureServiceInjector = class SignatureServiceInjector {
    constructor(orderService, avocatService, documentService, signataireService) {
        this.orderService = orderService;
        this.avocatService = avocatService;
        this.documentService = documentService;
        this.signataireService = signataireService;
    }
};
SignatureServiceInjector = __decorate([
    core_1.Injectable(),
    __metadata("design:paramtypes", [acte_services_1.ActeService,
        avocat_services_1.AvocatService,
        document_services_1.DocumentService,
        signataire_services_1.SignataireService])
], SignatureServiceInjector);
exports.SignatureServiceInjector = SignatureServiceInjector;
