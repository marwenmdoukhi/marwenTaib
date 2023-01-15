import { Signataire } from "../../../shared/entities/signataire";


export class SignataireModel {

    private _name: string;
    private _email: string;
    private _lastName: string;
    private _birthDate: string;
    private _phoneNumber: string;
    private _codeCountry: string;
    private _birthPlace: string;
    private _role: string;
    private _actId: string;
    private _signataire: Signataire;
    private _enterpriseName: string;


    constructor(signataire: Signataire) {
        this._signataire = signataire;
    }

    get email(): string {
        return this._email;
    }

    set email(email: string) {
        this._email = email;
    }

    get name(): string {
        return this._name;
    }
    get codeCountry(): string {
        return this._codeCountry;
    }

    set codeCountry(codeCountry: string) {
        this._codeCountry = codeCountry;
    }

    set name(name: string) {
        this._name = name;
    }

    get lastName(): string {
        return this._lastName;
    }

    set lastName(lastName: string) {
        this._lastName = lastName;
    }

    get birthDate(): string {
        return this._birthDate;
    }

    set birthDate(birthDate: string) {
        this._birthDate = birthDate;
    }

    get phoneNumber(): string {
        return this._phoneNumber;
    }

    set phoneNumber(phoneNumber: string) {
        this._phoneNumber = phoneNumber;
    }
    get birthPlace(): string {
        return this._birthPlace;
    }

    set birthplace(birthplace: string) {
        this._birthPlace = birthplace;
    }
    get role(): string {
        return this._role;
    }

    set role(role: string) {
        this._role = role;
    }

    get signatoryId(): string {
        return this._actId;
    }

    set signatoryId(signatoryId: string) {
        this._actId = signatoryId;
    }

    get signataire(): Signataire {
        return this._signataire;
    }

    set signataire(signataire: Signataire) {
        this._signataire = signataire;
    }

    get enterpriseName(): string {
        return this._enterpriseName;
    }

    set enterpriseName(enterpriseName: string) {
        this._enterpriseName = enterpriseName;
    }
}