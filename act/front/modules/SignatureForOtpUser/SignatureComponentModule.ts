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
import { SignatureComponent  } from './components/SignatureComponent';
import { SignatureProcess } from '../../shared/services/signatureProcess.services';
import { InputTextModule } from 'primeng/inputtext';
import { WindowService } from '../../shared/services/window.services';
import { ReCaptchaModule } from 'angular2-recaptcha';
import { PdfViewerModule } from 'ng2-pdf-viewer';
import { TooltipModule } from 'ng2-tooltip-directive';
import { CookieService } from 'ngx-cookie-service';
import {NgxSpinnerModule} from "ngx-spinner";




@NgModule({
    imports: [TooltipModule,PdfViewerModule,ReCaptchaModule,InputTextModule,SignaturePadModule, HttpClientModule, RadioButtonModule, DialogModule, CodeHighlighterModule, TabViewModule, ToastModule, BrowserAnimationsModule, MenuModule, StepsModule,
        BrowserAnimationsModule, FormsModule, CommonModule, FormsModule, MultiSelectModule, TreeviewModule.forRoot(),NgxSpinnerModule,
        FileUploadModule, ReactiveFormsModule, HttpModule, CalendarModule, FormsModule, TableModule, CheckboxModule, PaginatorModule, TabMenuModule, ToastrModule.forRoot(), CommonModule, BrowserModule, FormsModule, ReactiveFormsModule, HttpModule, BrowserAnimationsModule, ToastrModule.forRoot(),

    ],schemas: [CUSTOM_ELEMENTS_SCHEMA],
    declarations: [SignatureComponent],
    providers: [CookieService,WindowService,SignatureProcess,SendMail, AvocatService, SignataireService, MessageService, CalendarModule, ActeService, DocumentService, Calendar, FileUploadModule,
        { provide: LOCALE_ID, useValue: 'fr-FR' }],
    bootstrap: [SignatureComponent]
})
export class SignatureComponentModule { }