import {Avocat} from "../../../shared/entities/avocat";


export class counselModel {

    private _name: string;
    private _email: string;
    private _lastName: string;
    private _birthDate: string;
    private _phoneNumber: string;
    private _codeCountry: string;
    private _birthPlace: string;
    private _role: string;
    private _actId: string;
    private _emailApp: string;
    private _emailEd: string;
    private _avocat: Avocat;


    constructor(avocat: Avocat) {
        this._avocat = avocat;
    }

    get email(): string {
        return this._email;
    }

    set email(emailApp: string) {
        this._emailApp = emailApp;
    }
    get codeCountry(): string {
        return this._codeCountry;
    }

    set codeCountry(codeCountry: string) {
        this._codeCountry = codeCountry;
    }
    get emailApp(): string {
        return this._emailApp;
    }

    set emailApp(emailEd: string) {
        this._emailEd = emailEd;
    }

    get emailEd(): string {
        return this._emailEd;
    }

    set emailEd(email: string) {
        this._email = email;
    }

    get name(): string {
        return this._name;
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

    get actId(): string {
        return this._actId;
    }

    set actId(actId: string) {
        this._actId = actId;
    }

    get avocat(): Avocat {
        return this._avocat;
    }

    set avocat(avocat: Avocat) {
        this._avocat = avocat;
    }


}