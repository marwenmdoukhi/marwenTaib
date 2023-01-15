import {Injectable} from '@angular/core';
import {Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Avocat} from '../entities/avocat';
import {HttpHeaders} from "@angular/common/http";

@Injectable()
export class AvocatService {

    route: string = '/api/basic_users/';
    routeAllContact: string = '/api/users/get-contact';
    routeContact: string = 'api/users/';
    routeResiliation: string = 'api/users/resiliation';

    constructor(private http: Http) {
    }

    autocomplete(lastNameValue:any,firstName:any) {
        return this.http.post(this.route + "autocomplete",{'lastname':lastNameValue,'name':firstName}).toPromise().then(response => response.json()).catch(this.handleError);
    }

    resiliate(user: any) {
        return this.http.post(this.routeResiliation, user).toPromise().then(response => response.json()).catch(this.handleError);
    }

    addContact(contact: any) {
        return this.http.post(this.routeContact, contact).toPromise().then(response => response.json()).catch(this.handleError);
    }

    modifyContact(contact: any) {

        return this.http.put(this.routeContact, contact).toPromise().then(response => response.json()).catch(this.handleError);
    }
    deleteContact(avocat: any) {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(avocat)
        };
        return this.http.delete(this.routeContact, options).toPromise().then(response => response.json()).catch(this.handleError);
    }

    getAllAvocatsAsync(): Promise<Avocat[]> {
        return this.http.get(this.route + "counsels").toPromise().then(response => response.json() as Avocat[]).catch(this.handleError);
    }

    getAllContactAsync(): Promise<any[]> {
        return this.http.get(this.routeAllContact).toPromise().then(response => response.json() as any[]).catch(this.handleError);
    }


    postSignataire(avocat: Avocat) {
        return this.http.post(this.route, avocat).toPromise().then(response => response.json()).catch(this.handleError);
    }
    modifySignataire(avocat: Avocat) {
        return this.http.put(this.route, avocat).toPromise().then(response => response.json()).catch(this.handleError);
    }
    deleteAvocat(avocat: Avocat) {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(avocat)
        };
        return this.http.delete(this.route +"delete_counsel", options).toPromise().then(response => response.json()).catch(this.handleError);
    }

    private handleError(error: any): Promise<Avocat[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
}

