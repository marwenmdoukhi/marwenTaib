"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
class counselModel {
    constructor(avocat) {
        this._avocat = avocat;
    }
    get email() {
        return this._email;
    }
    set email(email) {
        this._email = email;
    }
    get name() {
        return this._name;
    }
    set name(name) {
        this._name = name;
    }
    get lastName() {
        return this._lastName;
    }
    set lastName(lastName) {
        this._lastName = lastName;
    }
    get birthDate() {
        return this._birthDate;
    }
    set birthDate(birthDate) {
        this._birthDate = birthDate;
    }
    get phoneNumber() {
        return this._phoneNumber;
    }
    set phoneNumber(phoneNumber) {
        this._phoneNumber = phoneNumber;
    }
    get birthPlace() {
        return this._birthPlace;
    }
    set birthplace(birthplace) {
        this._birthPlace = birthplace;
    }
    get role() {
        return this._role;
    }
    set role(role) {
        this._role = role;
    }
    get actId() {
        return this._actId;
    }
    set actId(actId) {
        this._actId = actId;
    }
    get avocat() {
        return this._avocat;
    }
    set avocat(avocat) {
        this._avocat = avocat;
    }
}
exports.counselModel = counselModel;
