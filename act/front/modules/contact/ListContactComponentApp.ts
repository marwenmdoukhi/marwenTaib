import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { ListContactComponentModule } from './ListContactComponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(ListContactComponentModule);