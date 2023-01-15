import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { InformationscomponentModule } from './InformationscomponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(InformationscomponentModule);