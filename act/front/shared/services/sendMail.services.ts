import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import { Order } from '../entities/order';
import { User } from "../entities/user";
import { Signataire } from '../entities/signataire';
import { Avocat } from '../entities/avocat';

@Injectable()
export class SendMail {

    route: string = 'api/acts/validation-email';
    routeValidator = 'api/acts/validator';
    routeValidatorResend = 'api/acts/resend-validation';
    routeSendToSign = "api/acts/signing-email";
    routeResendToSign ="api/acts/resend-signature";
    routeSignNotification="api/acts/sign-notification";
    routeValidatorNotification="api/acts/validate-notification";
    routeRefusValidatorNotification="api/acts/refuse-validate-notification";

    constructor(private http: Http) { }

    sendRelanceToValidateAct(userToSendMail: User): Promise<string> {
        return this.http.post(this.routeValidatorResend, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }

    sendRelanceToSignAct(userToSendMail: User): Promise<string> {
        return this.http.post(this.routeResendToSign, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }

    sendMailToSign(userToSendMail: any[]): Promise<string> {
        return this.http.post(this.routeSendToSign, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }

    sendMailToValidateAct(userToSendMail: any[]): Promise<string> {
        return this.http.post(this.route, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }
    changeUserToValidator(userToValidator: any[]): Promise<string> {
        return this.http.post(this.routeValidator, userToValidator).toPromise().then(response => response.json()).catch(this.handleError);
    }
    sendSignNotification(userToSendMail: User): Promise<string>{
        return this.http.post(this.routeSignNotification, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }
    sendValidatorNotifcation(userToSendMail: User): Promise<string>{
        return this.http.post(this.routeValidatorNotification, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }
    sendRefuseValidatorNotifcation(userToSendMail: User): Promise<string>{
        return this.http.post(this.routeRefusValidatorNotification, userToSendMail).toPromise().then(response => response.json()).catch(this.handleError);
    }


    private handleError(error: any): Promise<Order[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }

    private handleErrorGetById(error: any): Promise<String> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
}

