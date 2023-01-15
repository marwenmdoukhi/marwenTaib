import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { SignatureComponentModule } from './SignatureComponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(SignatureComponentModule);