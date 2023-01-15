"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * Classe représentant la structure d'une colonne.
 */
class Column {
    /**
     * Constructeur de la class Column.
     *
     * @params [[ isFrozen: boolean, isSortableDisabled: boolean, isReorderableDisabled: boolean, header: string, colkey: string, width: string, colspan: number]]
     */
    constructor(isFrozen, isSortableDisabled, isReorderableDisabled, header, colkey, witdth, colspan) {
        this._isFrozen = isFrozen;
        this._isSortableDisabled = isSortableDisabled;
        this._isReorderableDisabled = isReorderableDisabled;
        this._header = header;
        this._colkey = colkey;
        this._width = witdth;
        this._colspan = colspan;
    }
    /**
     * Retourne l'état isFrozen du column en boolean. Voir [[_isFrozen]].
     *
     * @returns boolean.
     */
    get isFrozen() {
        return this._isFrozen;
    }
    /**
     * Retourne l'état de la possibilité de faire du sorting sur la column en boolean. Voir [[_isSortableDisabled]].
     *
     * @returns boolean.
     */
    get isSortableDisabled() {
        return this._isSortableDisabled;
    }
    /**
     * Retourne l'état de la possibilité de faire du modifier l'ordre du column en boolean. Voir [[_isReorderableDisabled]].
     *
     * @returns boolean.
     */
    get isReorderableDisabled() {
        return this._isReorderableDisabled;
    }
    /**
     * Retourne la valeur du header de la column en string. Voir [[_header]].
     *
     * @returns string.
     */
    get header() {
        return this._header;
    }
    /**
     * Retourne la valeur du colkey de la column en string. Voir [[_colkey]].
     *
     * @returns string.
     */
    get colkey() {
        return this._colkey;
    }
    /**
     * Retourne la valeur du colspan de la column en number. Voir [[_colspan]].
     *
     * @returns number.
     */
    get colspan() {
        return this._colspan;
    }
    /**
     * Retourne la valeur du width de la column en string. Voir [[_width]].
     *
     * @returns string.
     */
    get width() {
        return this._width;
    }
    static createColumn(localStorageColumn) {
        return new Column(localStorageColumn._isFrozen, localStorageColumn._isSortableDisabled, localStorageColumn._isReorderableDisabled, localStorageColumn._header, localStorageColumn._colkey, localStorageColumn._width, localStorageColumn._colspan);
    }
}
exports.Column = Column;
