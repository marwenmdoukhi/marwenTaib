var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { CommonModule } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ToastrModule } from 'ngx-toastr';
import { Select2Module } from 'external/ng2-select2';
import { ConfirmationPopoverModule } from 'angular-confirmation-popover';
import { CalendarModule } from 'primeng/primeng';
import { DialogModule } from 'primeng/dialog';
import { JWBootstrapSwitchModule } from 'jw-bootstrap-switch-ng2';
import { MultiselectDropdownModule } from 'angular-4-dropdown-multiselect';
import { NgxTreeSelectModule } from 'ngx-tree-select';
import { DropdownModule } from 'primeng/dropdown';
import { PopoverModule } from 'ngx-popover';
import { FocusDirective } from 'directives/focus.directive';
import { ATSValidators } from 'shared/validators';
import { TypeSiteService } from 'services/typeSite.service';
import { SiteService } from 'services/site.service';
import { SignalRService } from 'services/signalr.service';
import { PrefacturationService } from 'services/prefacturation.service';
import { ParametreService } from 'services/parametre.service';
import { MessagingService } from 'services/messaging.service';
import { CookieService } from 'external/cookie.service';
import { SocieteService } from 'services/societe.service';
import { AffreteService } from 'services/affrete.service';
import { EmitterService } from 'services/emitter.service';
import { ReportingDataRequestService } from 'services/reportingDataRequest.service';
import { DetailCommandeCoutService } from 'services/detailCommandeCout.service';
import { PreFacturationPreFacturesComponent } from './components/PrefacturesComponent';
import { PrefacturesDialogSuppressionPrefacturationComponent } from './components/PrefacturesDialogSuppressionPrefacturationComponent';
import { DialogPrintModule } from 'shared/components/DialogPrint/DialogPrintModule';
import { LocalStorageService } from 'ngx-store';
import { ImprimantesService } from 'services/imprimantes.service';
import { GlobalErrorHandler } from 'shared/globalErrorHandler';
let PrefacturesModule = class PrefacturesModule {
};
PrefacturesModule = __decorate([
    NgModule({
        imports: [
            CommonModule, BrowserModule, FormsModule, ReactiveFormsModule, HttpModule, BrowserAnimationsModule, CalendarModule, DialogModule, ToastrModule.forRoot(),
            Select2Module, JWBootstrapSwitchModule, MultiselectDropdownModule, NgxTreeSelectModule.forRoot({ expandMode: "all" }),
            DialogPrintModule, PopoverModule,
            ConfirmationPopoverModule.forRoot({
                confirmButtonType: 'danger',
                confirmText: 'Oui',
                cancelText: 'Non',
                placement: 'left'
            }), DropdownModule
        ],
        declarations: [PreFacturationPreFacturesComponent, PrefacturesDialogSuppressionPrefacturationComponent, FocusDirective],
        providers: [ParametreService, PrefacturationService, AffreteService, SocieteService, ReportingDataRequestService, EmitterService, DetailCommandeCoutService,
            MessagingService, CookieService, ATSValidators, SignalRService, TypeSiteService, SiteService,
            LocalStorageService, ImprimantesService, GlobalErrorHandler],
        bootstrap: [PreFacturationPreFacturesComponent]
    })
], PrefacturesModule);
export { PrefacturesModule };
//# sourceMappingURL=PrefacturesModule.js.map