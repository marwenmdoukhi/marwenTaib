import {Injectable} from '@angular/core';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Order} from '../entities/order';
import {User} from "../entities/user";
import {HttpHeaders, HttpParams} from '@angular/common/http';
import { SearchBarUser } from '../entities/searchBarUser';
import { Cookies } from '../entities/cookies';
import {Observable} from "rxjs";

@Injectable()
export class ActeService {

    route: string = '/api/acts/';
    userRoute: string = 'api/users/connected';
    synthesePdf: string = '/api/acts/synthese-pdf';
    deleteSyntheseFile : string = '/api/acts/delete-synthese-file';
    deleteActeRoute: string = '/api/acts/delete';
    routeValidateAct: string = 'api/acts/validate-act';
    routeRefusAct: string = 'api/acts/refuse-act';
    routeSynthese: string = '/synthese-pdf';
    searchBarUser: string = '/api/search/'
    cookies: string = '/api/piwik/add-piwik';
    envRoute : string = '/api/environment/variabels';
    manualsRoute : string = '/api/users/manuals';
    getCookie: string = '/api/piwik/get-piwik';
    downloadFileProof: string = '/api/acts/download-proof';
    actArchive: string = '/api/acts/get-archive-by-act/';
    countForDashboard: string = '/api/acts/dashboard-count';
    archiveDashboard: string = '/api/acts/get-archive';
    disabledUser : string ='/api/acts/disable-user/';
    downloadSinged : string ='/api/oodrive/get-signed-file';
    deleteSigned : string ='/api/oodrive/delete-signed-file';
    base64File : string ='/api/acts/get-file-base64';
    base64ProofFile: string='/api/acts/get-proof-file-base64';
    deleteProofFileRoute : string='api/acts/delete-proof-file-base64';
    getActUsersRoute : string = '/api/acts/get-act-users'


    constructor(private http: Http) {
    }

    getActArchive(actId):Promise<any>{
        return this.http.get(this.actArchive+actId,).toPromise().then(response => response.json()).catch(this.handleError)
    }

    postCookies(cookies: Cookies) {
        return this.http.post(this.cookies, cookies).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getBase64File(actId: any,fileName: any) {
        return this.http.post(this.base64File, [actId,fileName]).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getBase64FileFromServer(file: any) {
        return this.http.post(this.base64File, file).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getBase64ProofFile(file: any) {
        return this.http.post(this.base64ProofFile, file).toPromise().then(response => response.json()).catch(this.handleError);
    }

    deleteProofFile(file:any){
        return this.http.post(this.deleteProofFileRoute, file).toPromise().then(response => response.json()).catch(this.handleError);
    }

    deleteSigne(act: any,randomString : string) {
        return this.http.post(this.deleteSigned, [act , randomString]).toPromise().then(response => response.json()).catch(this.handleError);
    }
    downloadSigne(act: any){
        return this.http.post(this.downloadSinged,act).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getCookies(){
        return this.http.get(this.getCookie).toPromise().then(response => response.json()).catch(this.handleError);
    }

    deleteActe(order: Order): Promise<Order[]> {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(order)
        };
        return this.http.delete(this.deleteActeRoute, options).toPromise().then(response => response.json() as Order[]).catch(this.handleError);
    }

    refuserActe(user: User): Promise<Order> {
        return this.http.post(this.routeRefusAct, user).toPromise().then(response => response.json() as Order).catch(this.handleErrorOrder);
    }

    validateActe(user:User): Promise<Order> {
        return this.http.post(this.routeValidateAct, user).toPromise().then(response => response.json() as Order).catch(this.handleErrorOrder);
    }

    getAllActesAsync(): Promise<Order[]> {
        return this.http.get(this.route).toPromise().then(response => response.json() as Order[]).catch(this.handleError);
    }

    getActByIdAsync(id: number): Promise<Order> {
        return this.http.get(this.route + id).toPromise().then(response => response.json() as Order).catch(this.handleErrorGetById);
    }

    postAct(order: Order) {
        if (order.id)
            return this.http.put(this.route, order).toPromise().then(response => response.json()).catch(this.handleError);
        return this.http.post(this.route, order).toPromise().then(response => response.json()).catch(this.handleError);
    }

    getUserconnectedAsync(): Promise<User> {
        return this.http.get(this.userRoute).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getUserconnectedAsyncOtp(act:any): Promise<User> {
        return this.http.get(this.userRoute,{params:{act:act}}).toPromise().then(response => response.json()).catch(this.handleError);
    }

    downloadSyntheseAct(currentActe, listSignataire, listAvocat, listDocument) {
        return this.http.post(this.synthesePdf, [currentActe, listSignataire, listAvocat, listDocument]).toPromise().then().catch(this.handleErrorSynthese);
    }

    deleteSynthese(syntheseFile){
        return this.http.post(this.deleteSyntheseFile, [syntheseFile]).toPromise().then().catch(this.handleErrorSynthese);
    }

    private handleErrorSynthese(error: any): Promise<any> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }

    private handleErrorOrder(error: any): Promise<Order> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }

    private handleError(error: any): Promise<Order[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    private handleErrorSearchBar(error: any): Promise<SearchBarUser[]> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    private handleErrorGetById(error: any): Promise<Order> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }

    postSearchBar(searchBar: SearchBarUser) {
        return this.http.post(this.searchBarUser, searchBar).toPromise().then(response => response.json()).catch(this.handleError);
    }

    getAllActesAsyncSearchBar(): Promise<SearchBarUser[]> {
        return this.http.get(this.searchBarUser).toPromise().then(response => response.json()).catch(this.handleErrorSearchBar);
    }

    deleteSearch(searchBarUser: SearchBarUser): Promise<SearchBarUser[]> {
        const options = {
            Headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            body: JSON.stringify(searchBarUser)
        };
        return this.http.delete(this.searchBarUser, options).toPromise().then(response => response.json() as SearchBarUser[]).catch(this.handleErrorSearchBar);
    }

    getEnvVariables(): Promise<any>{
        return this.http.get(this.envRoute).toPromise().then(response => response.json()).catch(this.handleError);
    }

    getManuals(): Promise<any>{
        return this.http.get(this.manualsRoute).toPromise().then(response => response.json()).catch(this.handleError);
    }
    downloadProof(act: any): Promise<any>{
        return this.http.post(this.downloadFileProof,[act]).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getCountAct(){
        return this.http.get(this.countForDashboard).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getArchiveForDashboard(){
        return this.http.get(this.archiveDashboard).toPromise().then(response => response.json()).catch(this.handleError);
    }
    postDisableUser(actId , userId){
        return this.http.post(this.disabledUser,[actId , userId] ).toPromise().then(response =>response.json()).catch(this.handleError);
    }
    getAllActesUsersAsync(user:User): Promise<Order[]> {
        return this.http.post(this.getActUsersRoute,user).toPromise().then(response => response.json() as Order[]).catch(this.handleError);
    }
}

