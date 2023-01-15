import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { DashboardcomponentModule } from './DashboardcomponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(DashboardcomponentModule);