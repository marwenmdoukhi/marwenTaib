import { Avocat } from "../../../../shared/entities/avocat";
import { Signataire } from "../../../../shared/entities/signataire";
import { Order } from "../../../../shared/entities/order";
import { CommonModel } from "../../../../shared/entities/commonModel";
import { User } from "../../../../shared/entities/user";

export class BaseViewModel {
    static BaseViewModel: any[];
    constructor() { }



    private static _displayContactAdd: boolean;
    public static get displayContactAdd(): boolean {
        return BaseViewModel._displayContactAdd;
    }
    public static set displayContactAdd(displayContactAdd: boolean) {
        BaseViewModel._displayContactAdd = displayContactAdd;
    }
    //    allData: CommonModel[] = [];

    private static _allData: CommonModel[] = [];
    public static get allData(): CommonModel[] {
        return BaseViewModel._allData;
    }
    public static set allData(allData: CommonModel[]) {
        BaseViewModel._allData = allData;
    }



    private static _signataireToModify: Avocat;
    public static get signataireToModify(): Avocat {
        return BaseViewModel._signataireToModify;
    }
    public static set signataireToModify(signataireToModify: Avocat) {
        BaseViewModel._signataireToModify = signataireToModify;
    }

    private static _allContact: Signataire[] = [];
    public static get allContact(): Signataire[] {
        return BaseViewModel._allContact;
    }
    public static set allContact(allContact: Signataire[]) {
        BaseViewModel._allContact = allContact;
    }

    private static _contactToModify: Signataire;


    public static get contactToModify(): Signataire {
        return BaseViewModel._contactToModify;
    }

    public static set contactToModify(contactToModify: Signataire) {
        BaseViewModel._contactToModify = contactToModify;
    }

    private static _modeModify: boolean;

    public static get modeModify(): boolean {
        return BaseViewModel._modeModify;
    }

    public static set modeModify(modeModify: boolean) {
        BaseViewModel._modeModify = modeModify;
    }

    private static _consultContact: boolean;

    public static get consultContact(): boolean {
        return BaseViewModel._consultContact;
    }

    public static set consultContact(consultContact: boolean) {
        BaseViewModel._consultContact = consultContact;
    }

    private static _allSignataire: Signataire[];

    public static get allSignataire(): Signataire[] {
        return BaseViewModel._allSignataire;
    }

    public static set allSignataire(allSignataire: Signataire[]) {
        BaseViewModel._allSignataire = allSignataire;
    }
    private static _userConnected: User;

    public static get userConnected(): User {
        return BaseViewModel._userConnected;
    }

    public static set userConnected(userConnected: User) {
        BaseViewModel._userConnected = userConnected;
    }

    private static _allAvocat: Avocat[];

    public static get allAvocat(): Avocat[] {
        return BaseViewModel._allAvocat;
    }

    public static set allAvocat(allAvocat: Avocat[]) {
        BaseViewModel._allAvocat = allAvocat;
    }


    private static _displayAllResult: boolean;

    public static get displayAllResult(): boolean {
        return BaseViewModel._displayAllResult;
    }

    public static set displayAllResult(displayAllResult: boolean) {
        BaseViewModel._displayAllResult = displayAllResult;
    }


    private static _inputReasearchBar: string;

    public static get inputReasearchBar(): string {
        return BaseViewModel._inputReasearchBar;
    }

    public static set inputReasearchBar(inputReasearchBar: string) {
        BaseViewModel._inputReasearchBar = inputReasearchBar;
    }

    private static _displayDivForReasearchBar: boolean;

    public static get displayDivForReasearchBar(): boolean {
        return BaseViewModel._displayDivForReasearchBar;
    }

    public static set displayDivForReasearchBar(displayDivForReasearchBar: boolean) {
        BaseViewModel._displayDivForReasearchBar = displayDivForReasearchBar;
    }
}