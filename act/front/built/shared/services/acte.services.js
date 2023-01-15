"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
const core_1 = require("@angular/core");
const http_1 = require("@angular/http");
require("rxjs/add/operator/toPromise");
let ActeService = class ActeService {
    constructor(http) {
        this.http = http;
        this.route = '/api/acts/';
        this.userRoute = 'api/users/connected';
    }
    getAllActesAsync() {
        return this.http.get(this.route).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getActByIdAsync(id) {
        return this.http.get(this.route + id).toPromise().then(response => response.json()).catch(this.handleErrorGetById);
    }
    postAct(order) {
        if (order.id)
            return this.http.put(this.route, order).toPromise().then(response => response.json()).catch(this.handleError);
        return this.http.post(this.route, order).toPromise().then(response => response.json()).catch(this.handleError);
    }
    getUserconnectedAsync() {
        return this.http.get(this.userRoute).toPromise().then(response => response.json()).catch(this.handleError);
    }
    handleError(error) {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
    handleErrorGetById(error) {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
};
ActeService = __decorate([
    core_1.Injectable(),
    __metadata("design:paramtypes", [http_1.Http])
], ActeService);
exports.ActeService = ActeService;
