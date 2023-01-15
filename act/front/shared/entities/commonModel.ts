
export class CommonModel {

    //Actes-Dossier
    nameActe: string;//Nom de l’acte
    folderNumber: string;//N de l’acte
    folderName: string;//Nom du dossier
    internalNumber: string //N interne du dossier
    id: string;//actId
    status: string;//act statut
    //Document
    documentName: string //Nom du document


    //Contacts
    contactName: string;
    contactLastName: string;
    contactPhoneNumber: string;
    contactMil: string;
    contactEnterpriseName: string;
    contactId : string;
    nature: string;
    //common
    type: string;
    actValidated: string;
    requestDate: string;
    receptionDate: string;
}

