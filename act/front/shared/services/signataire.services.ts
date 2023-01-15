import { Injectable } from '@angular/core';
import { Http, RequestMethod, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import { Signataire } from '../entities/signataire';
import { HttpHeaders } from '@angular/common/http';
import {Avocat} from "../entities/avocat";
import {Order} from "../entities/order";

@Injectable()
export class SignataireService {

    route: string = '/api/basic_users/';
    headers: Headers;
    options: RequestOptions;

    constructor(private http: Http) { }

    getAllSignataireAsync(): Promise<Signataire[]> {
        return this.http.get(this.route + "signatories").toPromise().then(response => response.json() as Signataire[]).catch(this.handleError);
    }

    postSignataire(signataire: Signataire) {
        return this.http.post(this.route, signataire).toPromise().then(response => response.json()).catch(this.handleError);
    }
    modifySignataire(signataire: Signataire) {
        return this.http.put(this.route, signataire).toPromise().then(response => response.json()).catch(this.handleError);
    }

    deleteSignataire(signataire: Signataire) {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(signataire)
        };
        return this.http.delete(this.route +"delete_signatory", options).toPromise().then(response => response.json()).catch(this.handleError);
    }

    private handleError(error: any): Promise<Signataire[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
}

