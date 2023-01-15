"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var module1_module_1 = require("./module1.module");
var platform_browser_dynamic_1 = require("@angular/platform-browser-dynamic");
var core_1 = require("@angular/core");
var platform = platform_browser_dynamic_1.platformBrowserDynamic();
core_1.enableProdMode();
platform.bootstrapModule(module1_module_1.Module1Module);
