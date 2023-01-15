import { NgModule, LOCALE_ID } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { CommonModule, APP_BASE_HREF } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ToastrModule } from 'ngx-toastr';
import { ActeService } from '../../shared/services/acte.services';
import { DocumentService } from '../../shared/services/document.services';
import { DialogModule } from 'primeng/dialog';
import { TableModule } from 'primeng/table';
import { PaginatorModule } from 'primeng/paginator';
import { TabMenuModule } from 'primeng/tabmenu';
import { CheckboxModule } from 'primeng/checkbox';
import { TreeviewModule } from 'ngx-treeview';
import { MultiSelectModule, CalendarModule } from 'primeng/primeng';
import { Calendar } from 'primeng/primeng';
import { MenuModule } from 'primeng/primeng';
import { MessageService } from 'primeng/api';
import { StepsModule } from 'primeng/steps';
import { ToastModule } from 'primeng/toast';
import { TabViewModule } from 'primeng/tabview';
import { CodeHighlighterModule } from 'primeng/codehighlighter';
import { FileUploadModule } from 'primeng/fileupload';
import { RadioButtonModule } from 'primeng/radiobutton';
import { SignataireService } from '../../shared/services/signataire.services';
import { AvocatService } from '../../shared/services/avocat.services';
import { HttpClientModule } from '@angular/common/http';
import { SignaturePadModule } from 'angular2-signaturepad';
import { SendMail } from '../../shared/services/sendMail.services';
import { WindowService } from '../../shared/services/window.services';
import { DashboardComponent } from './components/DashboardComponent';
import { InlineSVGModule } from 'ng-inline-svg';
import {CookieService} from "ngx-cookie-service";
import { BarPipe } from '../shared/BarPipe ';
import { AllResultComponent } from './components/AllResultComponent';
import { HighlightSearch } from '../shared/HighlightSearch';
import {NgxSpinnerModule} from "ngx-spinner";
import {InternationalPhoneNumberModule} from "ngx-international-phone-number";


@NgModule({
    imports: [SignaturePadModule, HttpClientModule, RadioButtonModule, DialogModule, CodeHighlighterModule, TabViewModule, ToastModule, BrowserAnimationsModule, MenuModule, StepsModule,
        BrowserAnimationsModule, FormsModule, CommonModule, FormsModule, MultiSelectModule, TreeviewModule.forRoot(),NgxSpinnerModule,
        FileUploadModule, ReactiveFormsModule, HttpModule, CalendarModule, FormsModule, TableModule, CheckboxModule, PaginatorModule, TabMenuModule, ToastrModule.forRoot(), CommonModule, BrowserModule, FormsModule, ReactiveFormsModule, HttpModule, BrowserAnimationsModule, ToastrModule.forRoot(), InlineSVGModule,
        InternationalPhoneNumberModule.forRoot()

    ],
    declarations: [DashboardComponent,BarPipe,AllResultComponent,HighlightSearch],
    providers: [ WindowService,SendMail, AvocatService, SignataireService, MessageService, CalendarModule, ActeService, DocumentService, Calendar, FileUploadModule,
        { provide: LOCALE_ID, useValue: 'fr-FR' },CookieService],
    bootstrap: [DashboardComponent]
})
export class DashboardcomponentModule { }