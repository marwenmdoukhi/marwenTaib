import { NgModule, LOCALE_ID , CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { CommonModule, APP_BASE_HREF } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ToastrModule } from 'ngx-toastr';
import { ActeService } from '../../shared/services/acte.services';
import { DocumentService } from '../../shared/services/document.services';
import { DialogModule } from 'primeng/dialog';
import { TooltipModule } from 'ng2-tooltip-directive';
import { InputSwitchModule } from 'primeng/inputswitch';

import { TableModule } from 'primeng/table';
import { PaginatorModule } from 'primeng/paginator';
import { TabMenuModule } from 'primeng/tabmenu';
import { CheckboxModule } from 'primeng/checkbox';
import { TreeviewModule } from 'ngx-treeview';
import { MultiSelectModule, CalendarModule } from 'primeng/primeng';
import { Calendar } from 'primeng/primeng';
import { OrdersViewModelMaster } from './view-models/bases/OrdersViewModelMaster';
import { CreateOrderComponent } from './components/CreateOrderComponent';
import { OrdersComponent } from './components/OrdersComponent';
import { AppComponent } from './components/app.component';
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
import { CreateSignataireComponent } from './components/CreateSignataireComponent';
import { consultActComponent } from './components/consultActComponent';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { CreateAvocatComponent } from './components/CreateAvocatComponent';
import { SearchAvocatComponent } from './components/SearchAvocatComponent';
import { SignaturePadModule } from 'angular2-signaturepad';
import { FilterPipe } from './pipe/FilterPipe';
import { SignatureAppComponent } from './components/SignatureAppComponent';
import { SignatureServiceInjector } from './SignatureServiceInjector';
import { SendMail } from '../../shared/services/sendMail.services';
import { DisplayComponentValidate } from './components/DisplayValidateComponent';
import { DatePipe } from './pipe/DatePipe';
import { DisplaySendToSignatureComponent } from './components/DisplaySendToSignatureComponent';
import { DisplayComments } from './components/displayComments';
import { syntheseActeRefusee } from './components/syntheseActeRefusee';
import {ConsultAbandonedActComponent} from "./components/ConsultAbandonedActComponent";
import {InternationalPhoneNumberModule} from "ngx-international-phone-number";
import { consultActForAvocatComponent } from './components/consultActForAvocatComponent';
import { SignatureProcess } from '../../shared/services/signatureProcess.services';
import { InlineSVGModule } from 'ng-inline-svg';
import {consultSignedActComponent} from "./components/consultSignedActComponent";
import { BarPipe } from '../shared/BarPipe ';
import { AllResultComponent } from './components/AllResultComponent';
import { HighlightSearch } from '../shared/HighlightSearch';
import { cookiesComponent } from './components/cookiesComponent';
import { CookieService } from 'ngx-cookie-service';
import {MatomoInjector} from "ngx-matomo";
import {NgxSpinnerModule} from "ngx-spinner";
import {consultActEnCoursDeSignatureComponent} from "./components/consultActEnCoursDeSignatureComponent";
import {ConsultActArchive} from "./components/consultActArchive"
import {NgxPaginationModule} from 'ngx-pagination'; //
import {AccordionModule} from 'primeng/accordion';



@NgModule({
    imports: [InputSwitchModule,TooltipModule,SignaturePadModule, HttpClientModule, RadioButtonModule, DialogModule, CodeHighlighterModule, TabViewModule, ToastModule, BrowserAnimationsModule, MenuModule, StepsModule,
        BrowserAnimationsModule, FormsModule, CommonModule, FormsModule, MultiSelectModule, TreeviewModule.forRoot(),NgxSpinnerModule,NgxPaginationModule,
        FileUploadModule, ReactiveFormsModule, HttpModule, CalendarModule, FormsModule, TableModule, CheckboxModule, PaginatorModule, TabMenuModule, ToastrModule.forRoot(), CommonModule, BrowserModule, FormsModule, ReactiveFormsModule, HttpModule, BrowserAnimationsModule, ToastrModule.forRoot(),
        InternationalPhoneNumberModule.forRoot(), InlineSVGModule, AccordionModule
    ],schemas: [CUSTOM_ELEMENTS_SCHEMA],
    declarations: [cookiesComponent,AllResultComponent, consultActForAvocatComponent, syntheseActeRefusee,ConsultAbandonedActComponent, DisplayComments, DisplaySendToSignatureComponent, DisplayComponentValidate, SignatureAppComponent, HighlightSearch, BarPipe, FilterPipe,DatePipe,CreateAvocatComponent, CreateSignataireComponent, AppComponent, OrdersComponent, CreateOrderComponent,consultActComponent,consultSignedActComponent,consultActEnCoursDeSignatureComponent,ConsultActArchive, SearchAvocatComponent],
    providers: [CookieService,SignatureProcess,SignatureServiceInjector,SendMail,AvocatService, SignataireService, MessageService, CalendarModule, ActeService, DocumentService, Calendar, FileUploadModule,MatomoInjector,
        { provide: 'IOrdersViewModelMaster', useClass: OrdersViewModelMaster }, { provide: LOCALE_ID, useValue: 'en-EN' }
    ],
    bootstrap: [AppComponent]
})
export class OrdersModule { }