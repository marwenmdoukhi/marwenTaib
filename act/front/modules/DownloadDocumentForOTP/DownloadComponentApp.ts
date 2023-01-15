import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { enableProdMode } from '@angular/core';
import { DownloadComponentModule } from './DownloadComponentModule';
const platform = platformBrowserDynamic();
enableProdMode();
platform.bootstrapModule(DownloadComponentModule);