import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import { Order } from '../entities/order';
import { User } from "../entities/user";
import { HttpHeaders } from '@angular/common/http';
import { OrderOodrive } from '../entities/OrderOodrive';

@Injectable()
export class SignatureProcess {

    routeCreateOrder: string = '/api/oodrive/create-order';
    routeValidateOodrive: string = '/api/oodrive/sign-otp';
    routerefuseSignAct: string = '/api/acts/refuse-signature';
    routeCurrentSigning : string = '/api/oodrive/signing-status';
    routeReleaseSignatory : string = '/api/oodrive/release-signatory';
    routeUpdateSigningSlot : string='/api/oodrive/update-signing-slot';
    generateOtpRoute : string = '/api/oodrive/generate_otp';
    validateOtpRoute : string = '/api/oodrive/validate-otp';

    constructor(private http: Http) {
    }

    refuseSignAct(user: User): Promise<string> {
        return this.http.post(this.routerefuseSignAct, user).toPromise().then(response => response.json() as string).catch(this.handleErrorCreateOrder);
    }

    createOrder(user: OrderOodrive): Promise<string> {
        return this.http.post(this.routeCreateOrder, user).toPromise().then(response => response.json() as string).catch(this.handleErrorCreateOrder);
    }

    validateOodrive(user: OrderOodrive): Promise<string> {
        return this.http.post(this.routeValidateOodrive, user).toPromise().then(response => response.json() as string).catch(this.handleErrorCreateOrder);
    }

    getCurrentSignings(order) : Promise<any>{
        return this.http.post(this.routeCurrentSigning , order).toPromise().then(response=>response.json() as string).catch(this.handleErrorCreateOrder)
    }
    updateSigningInProgress(order , status) : Promise<any>{
        return this.http.post(this.routeReleaseSignatory , [order, status]).toPromise().then(response=>response.json() as string).catch(this.handleErrorCreateOrder)
    }

    generateOtp(order , userConnected): Promise<any>{
        return this.http.post(this.generateOtpRoute , [order , userConnected]).toPromise().then(response=>response.json() as string).catch(this.handleErrorCreateOrder)
    }
    validateOtp(order , otpCode): Promise<any>{
        return this.http.post(this.validateOtpRoute , [order , otpCode]).toPromise().then(response=>response.json() as string).catch(this.handleErrorCreateOrder)
    }
    updateSigningSlot(order) : Promise<any>{
        return this.http.post(this.routeUpdateSigningSlot , order).toPromise().then(response=>response.json() as string).catch(this.handleErrorCreateOrder)
    }
    private handleErrorCreateOrder(error: any): Promise<string> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
}

