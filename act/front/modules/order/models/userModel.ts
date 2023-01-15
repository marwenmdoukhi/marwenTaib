
export class userModel {

    private _email: string;
    private _username: string;
    private _birthDate: string;
    private _phoneNumber: number;
    private _enterpriseName: string;
    private _id: number;
    private _lastName: string;
    private _name: string;
    private _roles: string;


    get email(): string {
        return this._email;
    }

    set email(value: string) {
        this._email = value;
    }

    get username(): string {
        return this._username;
    }

    set username(value: string) {
        this._username = value;
    }

    get birthDate(): string {
        return this._birthDate;
    }

    set birthDate(value: string) {
        this._birthDate = value;
    }

    get phoneNumber(): number {
        return this._phoneNumber;
    }

    set phoneNumber(value: number) {
        this._phoneNumber = value;
    }

    get enterpriseName(): string {
        return this._enterpriseName;
    }

    set enterpriseName(value: string) {
        this._enterpriseName = value;
    }

    get id(): number {
        return this._id;
    }

    set id(value: number) {
        this._id = value;
    }

    get lastName(): string {
        return this._lastName;
    }

    set lastName(value: string) {
        this._lastName = value;
    }

    get name(): string {
        return this._name;
    }

    set name(value: string) {
        this._name = value;
    }

    get roles(): string {
        return this._roles;
    }

    set roles(value: string) {
        this._roles = value;
    }
}