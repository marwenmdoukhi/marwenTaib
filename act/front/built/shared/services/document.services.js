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
const http_1 = require("@angular/http");
require("rxjs/add/operator/toPromise");
const http_2 = require("@angular/common/http");
let DocumentService = class DocumentService {
    constructor(http) {
        this.http = http;
        this.route = '/api/documents/';
        this.routeForMergeDocument = '/api/documents/merge';
    }
    getAllDocumentsAsync() {
        return this.http.get(this.route).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getMergedDocumentAsync(documents) {
        return this.http.post(this.routeForMergeDocument, documents).toPromise().then(response => response.json()).catch(this.handleErrorForMergeDocument);
    }
    postDocument(document) {
        return this.http.post(this.route, document).toPromise().then(response => response.json()).catch(this.handleError);
    }
    deleteDocument(doc) {
        const options = {
            Headers: new http_2.HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(doc)
        };
        return this.http.delete(this.route, options).toPromise().then(response => response.json()).catch(this.handleError);
    }
    handleError(error) {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    handleErrorForMergeDocument(error) {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
};
DocumentService = __decorate([
    core_1.Injectable(),
    __metadata("design:paramtypes", [http_1.Http])
], DocumentService);
exports.DocumentService = DocumentService;
