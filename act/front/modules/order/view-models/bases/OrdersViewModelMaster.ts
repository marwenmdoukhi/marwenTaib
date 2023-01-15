import {Injectable} from '@angular/core';
import {SelectItem} from 'primeng/primeng';
import {IOrdersViewModelMaster} from '../interfaces/IOrdersViewModelMaster';
import {Column} from '../../../../shared/models/Column';
import {Order} from '../../../../shared/entities/order';
import {Document} from '../../../../shared/entities/document';
import {Table} from 'primeng/table';
import {MenuItem} from 'primeng/api';
import {ActModel} from '../../models/actModel';
import {Signataire} from '../../../../shared/entities/signataire';
import {Avocat} from '../../../../shared/entities/avocat';
import {User} from "../../../../shared/entities/user";
import {BaseViewModel} from './BaseViewModel';
import {SignatureServiceInjector} from '../../SignatureServiceInjector';
import * as moment from "moment";
import {CommonModel} from '../../../../shared/entities/commonModel';
import {SearchBarUser} from '../../../../shared/entities/searchBarUser';
import {forkJoin} from "rxjs/observable/forkJoin";

@Injectable()
export class OrdersViewModelMaster extends BaseViewModel implements IOrdersViewModelMaster {
    testVariable: boolean;
    filterDateCreation: boolean;
    filterDateSigning: boolean;
    displayValidate: boolean;
    columns: Array<Column>;
    idActe: number;
    rangeDates: String[];
    listOrder: Order[];
    listAvocat: Avocat[];
    listSignataire: Signataire[];
    creationDate: string;
    signatureDate: string;
    fr: any;
    statuts: SelectItem[] = [];
    tableOrder: Table;
    items: MenuItem[];
    acteModel: ActModel;
    documentsModel: Document[] = [];
    signatairesModel: Signataire[] = [];
    avocatModel: Avocat[] = [];
    listDocument: Document[] = [];
    userConnected: User = new User();
    choosenStatuts: string;
    allSignataire: Signataire[];
    allAvocat: Avocat[] = [];
    filterChocie: string;
    loading: string;
    refuseActe: boolean = false;
    activateFilterClass: boolean = false;
    displaySentToSignature: boolean = false;
    displayComments: boolean = false;
    displayActeRefused: boolean = false;
    displayAbandonedAct : boolean = false;
    modeCreateOrModify: boolean = false;
    displayconsultActForAvocatComponent: boolean = false;
    displayConsultSignedActComponent: boolean = false;
    allData: CommonModel[] = [];
    private static _currentActe: Order = new Order();
    private static _userConnected: User;
    displayDivForReasearchBar: boolean = false;
    inputReasearchBar: string;
    displayAllResult: boolean = false;
    coloumnDateReception: Column;
    listSearchBarUser: SearchBarUser[];
    showSpinner: boolean = false;
    displayArchive: boolean = false;
    documentsActs : any = [];


    getRequestDate(date: any) {
        return moment(date.requestDate, "DD/MM/YYYY HH:mm:ss").format("DD/MM/YYYY");
    }
    getReceptionDate(date: any) {
        return moment(date.receptionDate, "DD/MM/YYYY HH:mm:ss").format("DD/MM/YYYY");
    }
    downloadSynthese() {
        let query = this.services.orderService.downloadSyntheseAct(OrdersViewModelMaster.currentActe, OrdersViewModelMaster.listSignataire, OrdersViewModelMaster.listAvocat, OrdersViewModelMaster.listDocument);
        Promise.all([query]).then(results => {
            var link = document.createElement('a');
            var deletFirstQuoate = results[0]._body.substring(1, results[0]._body.length - 1);
            var deletSecondQuoate = deletFirstQuoate.substring(0, deletFirstQuoate.length - 0);
            let today = moment().format('DD-MM-YYYY');
            let string = today+'_'+OrdersViewModelMaster.currentActe.folderNumber;
            link.href = '/documents/' + string + '.pdf';
            link.download = string + '.pdf';
            link.click();
            let deleteSynthese = this.services.orderService.deleteSynthese(string);
            Promise.all([deleteSynthese]).then(results=>{
                console.log('it works');
            })
        });
    }
    dismissActe(order: Order) {
        order.status = 'Abandonne'
        order.lastResentDate = null;
        let orderQuery = this.services.orderService.postAct(order);
        Promise.all([orderQuery]).then(results => {
            this.listOrder = this.listOrder.filter(acte => acte.id != order.id);
            this.listOrder.push(order);
            OrdersViewModelMaster.displayCreateOrder = false
            OrdersViewModelMaster.displayConsultOrder = false;
            OrdersViewModelMaster.displaySendTovalidation = false;
            this.displayValidate = false;
            this.displaySentToSignature = false;
            this.displayActeRefused = false;
            this.displayconsultActForAvocatComponent = false;
        });
    }

    onDeleteActe(acte: Order) {
        let orderQuery = this.services.orderService.deleteActe(acte);
        Promise.all([orderQuery]).then(results => {
            this.listOrder = this.listOrder.filter(act => act.id != acte.id);
            this.services.messageService.add({ severity: 'success', summary: 'Informations de l\'acte', detail: 'Acte supprimé avec succès', life: 4000 });
            OrdersViewModelMaster.displayCreateOrder = false;
            OrdersViewModelMaster.displayConsultOrder = false;
            this.displayAbandonedAct = false;
        });
    }

    public static get currentActe(): Order {
        return OrdersViewModelMaster._currentActe;
    }

    public static set currentActe(currentActe: Order) {
        OrdersViewModelMaster._currentActe = currentActe;
    }

    //Index de l'etape active
    private static _activeIndex: number = 0;


    public static get activeIndex(): number {
        return OrdersViewModelMaster._activeIndex;
    }

    public static set activeIndex(activeIndex: number) {
        OrdersViewModelMaster._activeIndex = activeIndex;
    }


    private static _reloadComplete: boolean = null;

    public static get reloadComplete(): boolean {
        return OrdersViewModelMaster._reloadComplete;
    }

    public static set reloadComplete(reloadComplete: boolean) {
        OrdersViewModelMaster._reloadComplete = reloadComplete;
    }


    //Mode Ajout(0)-Modification(1)
    private static _mode: number = 0;


    public static get mode(): number {
        return OrdersViewModelMaster._mode;
    }

    public static set mode(mode: number) {
        OrdersViewModelMaster._mode = mode;
    }

    private static _avocatToModify: Avocat;


    public static get avocatToModify(): Avocat {
        return OrdersViewModelMaster._avocatToModify;
    }


    public static set avocatToModify(avocatToModify: Avocat) {
        OrdersViewModelMaster._avocatToModify = avocatToModify;
    }


    private static _signataireToModify: Signataire;


    public static get signataireToModify(): Signataire {
        return OrdersViewModelMaster._signataireToModify;
    }


    public static set signataireToModify(signataireToModify: Signataire) {
        OrdersViewModelMaster._signataireToModify = signataireToModify;
    }


    private static _displayCreateOrder: boolean;


    public static get displayCreateOrder(): boolean {
        return OrdersViewModelMaster._displayCreateOrder;
    }


    public static set displayCreateOrder(displayCreateOrder: boolean) {
        OrdersViewModelMaster._displayCreateOrder = displayCreateOrder;
    }


    private static _displayConsultOrder: boolean;


    public static get displayConsultOrder(): boolean {
        return OrdersViewModelMaster._displayConsultOrder;
    }


    public static set displayConsultOrder(displayConsultOrder: boolean) {
        OrdersViewModelMaster._displayConsultOrder = displayConsultOrder;
    }

    private static _displayConsultSignedActComponent: boolean;


    public static get displayConsultSignedActComponent(): boolean {
        return OrdersViewModelMaster._displayConsultSignedActComponent;
    }

    public static set displayConsultSignedActComponent(displayConsultSignedActComponent: boolean) {
        OrdersViewModelMaster._displayConsultSignedActComponent = displayConsultSignedActComponent;
    }






    private static _displayModalSignataire: boolean;


    public static get displayModalSignataire(): boolean {
        return OrdersViewModelMaster._displayModalSignataire;
    }


    public static set displayModalSignataire(displayModalSignataire: boolean) {
        OrdersViewModelMaster._displayModalSignataire = displayModalSignataire;
    }

    private static _displayModalAvocat: boolean;


    public static get displayModalAvocat(): boolean {
        return OrdersViewModelMaster._displayModalAvocat;
    }


    public static set displayModalAvocat(displayModalAvocat: boolean) {
        OrdersViewModelMaster._displayModalAvocat = displayModalAvocat;
    }

    private static _displaySearchAvocat: boolean;


    public static get displaySearchAvocat(): boolean {
        return OrdersViewModelMaster._displaySearchAvocat;
    }


    public static set displaySearchAvocat(displaySearchAvocat: boolean) {
        OrdersViewModelMaster._displaySearchAvocat = displaySearchAvocat;
    }

    private static _allAvocat: Avocat[] = [];

    public static get allAvocat(): Avocat[] {
        return OrdersViewModelMaster._allAvocat;
    }

    public static set allAvocat(allAvocat: Avocat[]) {
        OrdersViewModelMaster._allAvocat = allAvocat;
    }
    private static _listAvocat: Avocat[] = [];
    public static get listAvocat(): Avocat[] {
        return OrdersViewModelMaster._listAvocat;
    }

    public static set listAvocat(listAvocat: Avocat[]) {
        OrdersViewModelMaster._listAvocat = listAvocat;
    }
    private static _listSignataire: Signataire[] = [];
    public static get listSignataire(): Signataire[] {
        return OrdersViewModelMaster._listSignataire;
    }
    public static set listSignataire(listSignataire: Signataire[]) {
        OrdersViewModelMaster._listSignataire = listSignataire;
    }
    private static _allSignataire: Signataire[] = [];
    public static get allSignataire(): Signataire[] {
        return OrdersViewModelMaster._allSignataire;
    }
    public static set allSignataire(allSignataire: Signataire[]) {
        OrdersViewModelMaster._allSignataire = allSignataire;
    }
    private static _listDocument: Document[] = [];
    public static get listDocument(): Document[] {
        return OrdersViewModelMaster._listDocument;
    }
    public static set listDocument(listDocument: Document[]) {
        OrdersViewModelMaster._listDocument = listDocument;
    }
    private static _idActe: number;

    public static get idActe(): number {
        return OrdersViewModelMaster._idActe;
    }
    public static set idActe(idActe: number) {
        OrdersViewModelMaster._idActe = idActe;
    }

    public static get userConnected(): User {
        return OrdersViewModelMaster._userConnected;
    }

    public static set userConnected(userConnected: User) {
        OrdersViewModelMaster._userConnected = userConnected;
    }

    private static _displaySendTovalidation: boolean;

    public static get displaySendTovalidation(): boolean {
        return OrdersViewModelMaster._displaySendTovalidation;
    }

    public static set displaySendTovalidation(displaySendTovalidation: boolean) {
        OrdersViewModelMaster._displaySendTovalidation = displaySendTovalidation;
    }

    constructor(services: SignatureServiceInjector) {
        super(services);
        this.fr = {
            firstDayOfWeek: 1,
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthNamesShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc"],
            today: "Aujourd'hui",
            clear: 'Effacer'
        };

        this.statuts.push({ label: 'En Projet', value: 'En Projet' });
        this.statuts.push({ label: 'Créé', value: 'Cree' });
        this.statuts.push({ label: 'En cours de validation', value: 'En cours de validation' });
        this.statuts.push({ label: 'Validation refusée', value: 'Validation refusee' });
        this.statuts.push({ label: 'En cours de signature', value: 'En cours de signature' });
        this.statuts.push({ label: 'Signé', value: 'Signe' });
        this.statuts.push({ label: 'Signature refusée', value: 'Signature refusee' });
        this.statuts.push({ label: 'Abandonné', value: 'Abandonne' });
    }


    getStatut(value: string): string {
        switch (value) {
            case "Cree": {
                return 'Cr\xE9\xE9';
            }
            case "Valide": {
                return "Valid\xE9";
            }
            case "Validation refusee": {
                return "Validation refus\xE9e";
            }
            case "Signee": {
                return "Sign\xE9";
            }
            case "Signature refusee": {
                return "Signature refus\xE9e";
            }
            case "Abandonne": {
                return "Abandonn\xE9";
            }
            default: {
                return value;
            }
        }
    }

    customSortDate(source: any, order: boolean, field: string, format: string) {
        return source.sort((a, b) => {
            if (b[field] && a[field]) {
                let result = moment(b[field], 'DD/MM/YYYY ' + format).diff(moment(a[field], 'DD/MM/YYYY ' + format));
                return order ? -result : result;
            }
            else if (!b[field]) {
                let result = moment('01/01/1900', 'DD/MM/YYYY ' + format).diff(moment(a[field], 'DD/MM/YYYY ' + format));
                return order ? -result : result;
            }
            else {
                let result = moment(b[field], 'DD/MM/YYYY ' + format).diff(moment('01/01/1900', 'DD/MM/YYYY' + format));
                return order ? -result : result;
            }
        });
    }

    arrayChangePosition(arr: any, fromIndex: any, toIndex: any): Document[] {
        var element = arr[fromIndex];
        arr.splice(fromIndex, 1);
        arr.splice(toIndex, 0, element);
        return arr
    }


    assignActeModel(item: Order) {
        if(item===undefined){
            return;
        }
        console.log(item);
        if (item["type"] && item["type"] == 2) {
            let reasearchUser = new SearchBarUser();
            reasearchUser.idUser = "" + OrdersViewModelMaster.userConnected.id;
            reasearchUser.type = item["type"];
            reasearchUser.idEntity = item["contactId"];
            let searchBarUser = this.services.orderService.postSearchBar(reasearchUser);
            Promise.all([searchBarUser]).then(results => {
            });
            window.location.href = '/contact';
        }
        else {
            if (item["type"] && (item["type"] == 1 || item["type"] == 0)) {
                item = this.listOrder.filter(ite => ite.id == item.id)[0];
            }
            this.displayDivForReasearchBar = false;
            this.inputReasearchBar = null;
            OrdersViewModelMaster.displayCreateOrder = false;
            OrdersViewModelMaster.displayConsultOrder = false;
            OrdersViewModelMaster.displaySendTovalidation = false;
            this.displayValidate = false;
            this.displaySentToSignature = false;
            this.displayActeRefused = false;
            this.displayconsultActForAvocatComponent = false;
            this.displayConsultSignedActComponent = false;

            OrdersViewModelMaster.activeIndex = 3;
            OrdersViewModelMaster.mode = 1;
            OrdersViewModelMaster.currentActe = item;
            OrdersViewModelMaster.idActe = item.id;
            OrdersViewModelMaster.listDocument = this.listDocument.filter(doc => doc.actId == item.id);

            OrdersViewModelMaster.listDocument = OrdersViewModelMaster.listDocument.sort((a, b) => (a.position > b.position) ? 1 : ((b.position > a.position) ? -1 : 0));

            if (OrdersViewModelMaster.userConnected && OrdersViewModelMaster.userConnected.roles && OrdersViewModelMaster.userConnected.roles[0] == 'ROLE_COUNSEL' && item.actValidated == 'null') {
                this.displayValidate = true;
            }
            if (OrdersViewModelMaster.userConnected && OrdersViewModelMaster.userConnected.roles && OrdersViewModelMaster.userConnected.roles[0] == 'ROLE_COUNSEL' && item.actValidated != 'null') {
                OrdersViewModelMaster.listAvocat = this.allAvocat.filter(avocat => avocat.actId == item.id.toString());
                OrdersViewModelMaster.listSignataire = this.allSignataire.filter(signataire => signataire.actId == item.id.toString());
                this.displayconsultActForAvocatComponent = true;
            }
            if (item.status == "Cree") {

                this.modeCreateOrModify = true;
            }
            if (OrdersViewModelMaster.userConnected && OrdersViewModelMaster.userConnected.roles && OrdersViewModelMaster.userConnected.roles[0] != 'ROLE_COUNSEL') {
                OrdersViewModelMaster.listAvocat = this.allAvocat.filter(avocat => avocat.actId == item.id.toString());
                OrdersViewModelMaster.listSignataire = this.allSignataire.filter(signataire => signataire.actId == item.id.toString());
                for (let av of OrdersViewModelMaster.listAvocat) {
                    if (av.validator && !av.mailSent) {
                        av.validator = false;
                    }
                }
                for (let sig of OrdersViewModelMaster.listSignataire) {
                    if (sig.validator && !sig.mailSent) {
                        sig.validator = false;
                    }
                }
                if (item.status == "En Projet" || item.status == "Cree") {
                    OrdersViewModelMaster.displayCreateOrder = true;
                }
                else {
                    if (item.status == 'En cours de validation' || item.status == 'En cours de signature') {

                        OrdersViewModelMaster.displaySendTovalidation = true;
                        OrdersViewModelMaster.displayConsultOrder = true;
                    }
                    else if (item.status == 'Validation refusee' || item.status == 'Signature refusee') {
                        this.displayActeRefused = true;
                    }else if (item.status == 'Abandonne'){
                        this.displayAbandonedAct = true;
                    }
                    else if (item.status == 'Signee') {
                        this.displayConsultSignedActComponent = true;
                    }
                }
            }
        }
    }
    getData() {
        OrdersViewModelMaster.reloadComplete = false;
        let orderQuery = this.services.orderService.getAllActesAsync();
        let userQuery = this.services.orderService.getUserconnectedAsync();
        let avocatQuery = this.services.avocatService.getAllAvocatsAsync();
        let sigataireQuery = this.services.signataireService.getAllSignataireAsync();

        Promise.all([orderQuery, userQuery, avocatQuery,sigataireQuery,this.services.spinner.show()]).then(results1 => {
            this.services.spinner.hide();
            for (let act of results1[0]) {
                let commonModel = new CommonModel();
                commonModel.nameActe = act.name;
                commonModel.status = act.status;
                commonModel.id = "" + act.id;
                commonModel.folderNumber = act.folderNumber;
                commonModel.folderName = act.folderName;
                if (!act.actValidated) {
                    commonModel.actValidated = 'null';
                }
                else {
                    commonModel.actValidated = act.actValidated;
                }
                commonModel.requestDate = act.receptionDate;
                commonModel.type = "0";
                this.allData.push(commonModel);
                act.actValidated = '' + act.actValidated;

                if (act.lastResentDate) {
                    var momentVariable = moment(act.lastResentDate, 'DD/MM/YYYY HH:mm');
                    let localResentForValidation = new Date(momentVariable.toDate()).toLocaleString();
                    act.lastResentDate = localResentForValidation;
                }
                this.documentsActs.push(act.id);
            }
            this.allAvocat = results1[2];
            for (let s of this.allAvocat) {
                s.birthDate = null;
            }
            this.allSignataire = results1[3];

            for (let s of this.allSignataire) {
                if (!s.enterpriseName) {
                    s.role = "signatory";
                }
                else {
                    s.role = "enterprise";
                }
            }
            if (results1[1].roles[0] != 'ROLE_USER') {
                this.listOrder = results1[0].filter(act => act.status != "Cree" && act.status != "En Projet"&& this.allAvocat.some(item => item.actId == ""+act.id && item.validator == true));
            }
            else {
                this.listOrder = results1[0];
            }

            let actsDocuments = this.services.documentService.getAllActsDocument(this.documentsActs);
            Promise.all([actsDocuments]).then(documentsResult => {
                this.listDocument = documentsResult[0];
                for (let document of documentsResult[0]) {
                    if (document.actId && this.listOrder.filter(act => act.id == document.actId).length == 1) {
                        let commonModel = new CommonModel();
                        commonModel.documentName = document.name;
                        commonModel.id = "" + document.actId;
                        commonModel.type = '1';
                        commonModel.status = this.listOrder.filter(act => act.id == document.actId)[0].status;
                        commonModel.requestDate = this.listOrder.filter(act => act.id == document.actId)[0].receptionDate;
                        this.allData.push(commonModel);
                    }
                }
            })
            OrdersViewModelMaster.reloadComplete = true;
            OrdersViewModelMaster.userConnected = results1[1];
            results1[1].comment = "";
            if (results1[1].roles[0] == 'ROLE_USER') {
                this.coloumnDateReception = new Column(false, true, false, 'Date de signature', 'signingDate', '20px', 3);
            }
            else {
                this.coloumnDateReception = new Column(false, true, false, 'Date de réception', 'receptionDate', '20px', 3);
            }
            this.columns = [
                new Column(false, true, false, 'Nom de l\'acte / N° de l\'acte', 'folderName', '20px', 3),
                new Column(false, true, false, 'Nom du dossier', 'name', '20px', 3),
                new Column(false, true, false, 'Date de dernière modification', 'requestDate', '20px', 3),
                this.coloumnDateReception,
                new Column(false, true, false, 'statut', 'status', '20px', 3),
            ];
            this.loading = "1";
            this.services.spinner.hide();

            let allContactQuery = this.services.avocatService.getAllContactAsync();
            let searchBarUser = this.services.orderService.getAllActesAsyncSearchBar();
            Promise.all([userQuery, allContactQuery, searchBarUser]).then(results => {
                if (OrdersViewModelMaster.userConnected && OrdersViewModelMaster.userConnected.roles && OrdersViewModelMaster.userConnected.roles[0] != 'ROLE_COUNSEL') {
                    for (let contact of results[1]) {
                        let commonModel = new CommonModel();
                        commonModel.contactName = contact.name;
                        commonModel.contactLastName = contact.lastName;
                        commonModel.contactMil = contact.email;
                        commonModel.contactPhoneNumber = contact.phoneNumber;
                        commonModel.contactId = contact.id;
                        commonModel.type = "2";
                        if (contact.roles[0] == "ROLE_COUNSEL") {
                            commonModel.nature = "avocat"
                        }
                        else {
                            commonModel.nature = "signataire"
                        }
                        this.allData.push(commonModel);
                    }
                }
                this.listSearchBarUser = results[2];
                if (results[2].filter(elm => elm.idUser == "" + OrdersViewModelMaster.userConnected.id).length > 0) {
                    this.assignActeModel(this.listOrder.filter(contact => contact.id == results[2].filter(elm => elm.idUser == "" + OrdersViewModelMaster.userConnected.id)[0].idEntity)[0]);
                    let deletQuery = this.services.orderService.deleteSearch(results[2][0]);
                    Promise.all([deletQuery]).then(results => {
                    });
                }
            });
        });
    }

    toggleSidebar() {
        let wrapperElement = document.querySelector('.wrapper');
        if (wrapperElement !== undefined) {
            wrapperElement.classList.toggle('menu-open');
        }

    }
}