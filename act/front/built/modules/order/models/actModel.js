"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
class ActModel {
    constructor(acte) {
        this._order = acte;
    }
    get name() {
        return this._name;
    }
    set name(name) {
        this._name = name;
    }
    get internalNumber() {
        return this._internalNumber;
    }
    set internalNumber(internalNumber) {
        this._internalNumber = internalNumber;
    }
    get folderName() {
        return this._folderName;
    }
    set folderName(folderName) {
        this._folderName = folderName;
    }
    get order() {
        return this._order;
    }
    set order(folderName) {
        this._order = folderName;
    }
}
exports.ActModel = ActModel;
