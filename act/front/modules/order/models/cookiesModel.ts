import { Order } from "../../../shared/entities/order";

export class ActModel {

    private _name: string;
    private _internalNumber: string;
    private _folderName: string;
    private _order: Order;

    constructor(acte: Order) {
        this._order = acte;
    }

    get name(): string {
        return this._name;
    }

    set name(name: string) {
        this._name = name;
    }
    get internalNumber(): string {
        return this._internalNumber;
    }

    set internalNumber(internalNumber: string) {
        this._internalNumber = internalNumber;
    }
    get folderName(): string {
        return this._folderName;
    }

    set folderName(folderName: string) {
        this._folderName = folderName;
    }

    get order(): Order {
        return this._order;
    }

    set order(folderName: Order) {
        this._order = folderName;
    }
}