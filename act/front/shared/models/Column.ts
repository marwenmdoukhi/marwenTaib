/**
 * Classe représentant la structure d'une colonne.
 */
export class Column {
    /** Valeur de l'état isFrozen du column en boolean. */
    private _isFrozen: boolean;
    /**
     * Retourne l'état isFrozen du column en boolean. Voir [[_isFrozen]].
     *
     * @returns boolean.
     */
    public get isFrozen(): boolean {
        return this._isFrozen;
    }

    /** Valeur de la possibilité de faire du sorting sur la column en boolean. */
    private _isSortableDisabled: boolean;
    /**
     * Retourne l'état de la possibilité de faire du sorting sur la column en boolean. Voir [[_isSortableDisabled]].
     *
     * @returns boolean.
     */
    public get isSortableDisabled(): boolean {
        return this._isSortableDisabled;
    }

    /** Valeur de l'état de la possibilité de faire du modifier l'ordre du column en boolean. */
    private _isReorderableDisabled: boolean;
    /**
     * Retourne l'état de la possibilité de faire du modifier l'ordre du column en boolean. Voir [[_isReorderableDisabled]].
     *
     * @returns boolean.
     */
    public get isReorderableDisabled(): boolean {
        return this._isReorderableDisabled;
    }

    /** Valeur du header de la column en string. */
    private _header: string;
    /**
     * Retourne la valeur du header de la column en string. Voir [[_header]].
     *
     * @returns string.
     */
    public get header(): string {
        return this._header;
    }

    /** Valeur du colkey de la column en string. */
    private _colkey: string;
    /**
     * Retourne la valeur du colkey de la column en string. Voir [[_colkey]].
     *
     * @returns string.
     */
    public get colkey(): string {
        return this._colkey;
    }

    /** Valeur du colspan de la column en number. */
    private _colspan: number;
    /**
     * Retourne la valeur du colspan de la column en number. Voir [[_colspan]].
     *
     * @returns number.
     */
    public get colspan(): number {
        return this._colspan;
    }

    /** Valeur du width de la column en string. */
    private _width: string;
    /**
     * Retourne la valeur du width de la column en string. Voir [[_width]].
     *
     * @returns string.
     */
    public get width(): string {
        return this._width;
    }

    /**
     * Constructeur de la class Column. 
     *
     * @params [[ isFrozen: boolean, isSortableDisabled: boolean, isReorderableDisabled: boolean, header: string, colkey: string, width: string, colspan: number]]
     */
    constructor(
        isFrozen: boolean,
        isSortableDisabled: boolean,
        isReorderableDisabled: boolean,
        header: string,
        colkey: string,
        witdth: string,
        colspan: number,
    ) {
        this._isFrozen = isFrozen;
        this._isSortableDisabled = isSortableDisabled;
        this._isReorderableDisabled = isReorderableDisabled;
        this._header = header;
        this._colkey = colkey;
        this._width = witdth;
        this._colspan = colspan;
    }

    public static createColumn(localStorageColumn: Column): Column {
        return new Column(
            localStorageColumn._isFrozen,
            localStorageColumn._isSortableDisabled,
            localStorageColumn._isReorderableDisabled,
            localStorageColumn._header,
            localStorageColumn._colkey,
            localStorageColumn._width,
            localStorageColumn._colspan
        );
    }
}