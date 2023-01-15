import { Order } from "../../../shared/entities/order";
import { Document } from "../../../shared/entities/document";


export class DocumentModel {

    private _name: string;
    private _file: string;
    private _acte: Order;
    private _extension: string;
    private _document: Document;

    constructor(document: Document) {
        this._document = document;
    }

    get extension(): string {
        return this._extension;
    }

    set extension(extension: string) {
        this._extension = extension;
    }

    get name(): string {
        return this._name;
    }

    set name(name: string) {
        this._name = name;
    }

    get file(): string {
        return this._file;
    }

    set file(folderName: string) {
        this._file = folderName;
    }

    get acte(): Order {
        return this._acte;
    }

    set acte(folderName: Order) {
        this._acte = folderName;
    }
}