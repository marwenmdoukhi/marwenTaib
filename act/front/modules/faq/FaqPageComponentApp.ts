import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { FaqPageComponentModule } from './FaqPageComponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(FaqPageComponentModule);