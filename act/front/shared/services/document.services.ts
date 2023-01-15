import { Injectable } from '@angular/core';
import {Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import { Document } from '../entities/document';
import { HttpHeaders } from '@angular/common/http';

@Injectable()
export class DocumentService {

    route: string = '/api/documents/';
    routeForActsDocuments : string = '/api/documents/acts-documents';
    routeForDocumentActs: string= '/api/documents/documents-act';
    routePosition: string = '/api/documents/document-position';
    routeForMergeDocument: string = '/api/documents/merge';
    routeForGetMergeDocument: string = '/api/documents/get-merge';
    routeForMergeDocumentSigned: string = '/api/documents/merge-signed';
    routeForDownloadSignedDocOtp : string = '/api/users/signed-doc-otp';

    constructor(private http: Http) { }

    getAllDocumentsAsync(): Promise<Document[]> {
        return this.http.get(this.route).toPromise().then(response => response.json() as Document[]).catch(this.handleError);
    }

    getAllActsDocument(acts : any[]) : Promise<Document[]>{
        return this.http.post(this.routeForActsDocuments , acts).toPromise().then(response => response.json() as Document[]).catch(this.handleError);
    }
    getDocumentsForAct(acts : any[]) : Promise<Document[]>{
        return this.http.post(this.routeForDocumentActs , acts).toPromise().then(response => response.json() as Document[]).catch(this.handleError);
    }

    postsDocumentsPositionAsync(documents:Document[]): Promise<string> {
        return this.http.put(this.routePosition, documents).toPromise().then(response => response.json() as string).catch(this.handleErrorForMergeDocument);
    }

    getMergedDocumentAsync(documents: string[]): Promise<string> {
        return this.http.post(this.routeForMergeDocument, documents).toPromise().then(response => response.json() as string).catch(this.handleErrorForMergeDocument);
    }
    getMergedDocument(any: string[]): Promise<string> {
        return this.http.post(this.routeForGetMergeDocument, any).toPromise().then(response => response.json() as string).catch(this.handleErrorForMergeDocument);
    }

    postDocument(document: Document) {
        return this.http.post(this.route, document).toPromise().then(response => response.json()).catch(this.handleError);
    }

    deleteDocument(doc: Document) {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(doc)
        };
        return this.http.delete(this.route, options).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getMergedDocumentSignedAsync(documents: string[]): Promise<string> {
        return this.http.post(this.routeForMergeDocumentSigned, documents).toPromise().then(response => response.json() as string).catch(this.handleErrorForMergeDocument);
    }

    private handleError(error: any): Promise<Document[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    private handleErrorForMergeDocument(error: any): Promise<string> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    getSignedDocumentOtp(arr : string[] ):Promise<any>{
        return this.http.post(this.routeForDownloadSignedDocOtp,arr).toPromise().then(response => response.json()).catch(this.handleError);
    }
}

