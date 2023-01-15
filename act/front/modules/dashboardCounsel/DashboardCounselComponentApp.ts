import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { DashboardCounselcomponentModule } from './DashboardCounselcomponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(DashboardCounselcomponentModule);