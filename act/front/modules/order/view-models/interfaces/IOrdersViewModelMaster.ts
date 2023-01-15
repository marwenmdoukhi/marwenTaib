
import { EventEmitter } from '@angular/core';

import { Subject } from 'rxjs/Subject';
import { SelectItem } from 'primeng/primeng';
import { Column } from '../../../../shared/models/Column';
import { Order } from '../../../../shared/entities/order';
import { Document } from '../../../../shared/entities/document';

import { Table } from 'primeng/table';
import { ActModel } from '../../models/actModel';
import { DocumentModel } from '../../models/documentModel';
import { Signataire } from '../../../../shared/entities/signataire';
import { Avocat } from '../../../../shared/entities/avocat';
import { SignatureServiceInjector } from '../../SignatureServiceInjector';
import { CommonModel } from '../../../../shared/entities/commonModel';
import { SearchBarUser } from '../../../../shared/entities/searchBarUser';


export interface IOrdersViewModelMaster {

    services: SignatureServiceInjector;
    columns: Array<Column>;
    listOrder: Order[];
    getData(): void;
    idActe:number;
    creationDate: string;
    signatureDate : string;
    fr: any;
    statuts: SelectItem[];
    allData: CommonModel[];
    tableOrder: Table;
    acteModel: ActModel;
    documentsModel: Document[];
    signatairesModel: Signataire[];
    listSignataire: Signataire[];
    allSignataire: Signataire[];
    avocatModel: Avocat[];
    listAvocat: Avocat[];
    allAvocat: Avocat[];
    listDocument: Document[];
    choosenStatuts: string;
    getStatut(value: string): string;
    filterDateCreation: boolean;
    filterDateSigning: boolean;
    filterChocie: string;
    loading: string;
    displayValidate: boolean;
    refuseActe: boolean;
    showSpinner : boolean;
    onDeleteActe(acte: Order);
    downloadSynthese();
    activateFilterClass: boolean;
    testVariable: boolean;
    displaySentToSignature: boolean;
    displayComments: boolean;
    displayActeRefused: boolean;
    displayAbandonedAct : boolean;
    modeCreateOrModify: boolean;
    displayconsultActForAvocatComponent: boolean;
    displayConsultSignedActComponent: boolean;
    customSortDate(source: any, order: boolean, field: string, format: string);
    dismissActe(acte: Order);
    toggleSidebar();
    assignActeModel(item: Order);
    displayDivForReasearchBar: boolean;
    inputReasearchBar: string;
    displayAllResult: boolean;
    displayArchive: boolean;
    listSearchBarUser: SearchBarUser[];
    getRequestDate(date: any);
    getReceptionDate(date:any);
    displaySearchAvocat: boolean;
}