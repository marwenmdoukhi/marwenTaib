"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
class DocumentModel {
    constructor(document) {
        this._document = document;
    }
    get extension() {
        return this._extension;
    }
    set extention(extention) {
        this._extension = extention;
    }
    get name() {
        return this._name;
    }
    set name(name) {
        this._name = name;
    }
    get file() {
        return this._file;
    }
    set file(folderName) {
        this._file = folderName;
    }
    get acte() {
        return this._acte;
    }
    set acte(folderName) {
        this._acte = folderName;
    }
}
exports.DocumentModel = DocumentModel;
