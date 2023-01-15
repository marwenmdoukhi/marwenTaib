"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
var core_1 = require("@angular/core");
var Module1Component = /** @class */ (function () {
    function Module1Component() {
        this.test = 400;
    }
    Module1Component = __decorate([
        core_1.Component({
            selector: "module1",
            templateUrl: "module1.components.html",
            styles: ['.navbar-fixed { position:fixed; }']
        })
    ], Module1Component);
    return Module1Component;
}());
exports.Module1Component = Module1Component;
