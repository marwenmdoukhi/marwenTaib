import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { DisplayValidateComponentModule } from './DisplayValidateComponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(DisplayValidateComponentModule);