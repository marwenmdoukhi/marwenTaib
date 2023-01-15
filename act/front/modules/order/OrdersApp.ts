import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { OrdersModule } from './OrdersModule';
import { enableProdMode } from '@angular/core';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(OrdersModule);