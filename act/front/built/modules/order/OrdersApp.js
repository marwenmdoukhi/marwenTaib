"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const platform_browser_dynamic_1 = require("@angular/platform-browser-dynamic");
const OrdersModule_1 = require("./OrdersModule");
const core_1 = require("@angular/core");
const platform = platform_browser_dynamic_1.platformBrowserDynamic();
core_1.enableProdMode();
platform.bootstrapModule(OrdersModule_1.OrdersModule);
